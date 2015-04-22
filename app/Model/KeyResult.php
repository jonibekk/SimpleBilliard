<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team              $Team
 * @property Goal              $Goal
 * @property ActionResult      $ActionResult
 * @property Post              $Post
 */
class KeyResult extends AppModel
{
    /**
     * 目標値の単位
     */
    const UNIT_PERCENT = 0;
    const UNIT_NUMBER = 1;
    const UNIT_BINARY = 2;
    const UNIT_YEN = 3;
    const UNIT_DOLLAR = 4;

    static public $UNIT = [
        self::UNIT_PERCENT => "",
        self::UNIT_YEN     => "",
        self::UNIT_DOLLAR  => "",
        self::UNIT_NUMBER  => "",
        self::UNIT_BINARY  => "",
    ];

    /**
     * 目標値の単位の表示名をセット
     */
    function _setUnitName()
    {
        self::$UNIT[self::UNIT_PERCENT] = __d('gl', "%");
        self::$UNIT[self::UNIT_YEN] = __d('gl', '¥');
        self::$UNIT[self::UNIT_DOLLAR] = __d('gl', '$');
        self::$UNIT[self::UNIT_NUMBER] = __d('gl', "その他の単位");
        self::$UNIT[self::UNIT_BINARY] = __d('gl', 'なし');
    }

    /**
     * 重要度の名前をセット
     */
    private function _setPriorityName()
    {
        $this->priority_list[0] = __d('gl', "0 (進捗に影響しない)");
        $this->priority_list[1] = __d('gl', "1 (とても低い)");
        $this->priority_list[3] = __d('gl', "3 (デフォルト)");
        $this->priority_list[5] = __d('gl', "5 (とても高い)");
    }

    public $priority_list = [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
    ];

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
            'notEmpty' => [
                'rule' => 'notEmpty',
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'Goal',
    ];

    public $hasMany = [
        'ActionResult',
        'Post',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
        $this->_setPriorityName();
    }

    /**
     * @param      $data
     * @param      $goal_id
     * @param null $uid
     *
     * @return bool
     * @throws Exception
     */
    function add($data, $goal_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        if (!isset($data['KeyResult']) || empty($data['KeyResult'])) {
            throw new RuntimeException(__d('gl', "基準のデータがありません。"));
        }
        $data['KeyResult']['goal_id'] = $goal_id;
        $data['KeyResult']['user_id'] = $uid;
        $data['KeyResult']['team_id'] = $this->current_team_id;

        if ($data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY) {
            $data['KeyResult']['start_value'] = 0;
            $data['KeyResult']['target_value'] = 1;
        }
        $data['KeyResult']['current_value'] = $data['KeyResult']['start_value'];

        //時間をunixtimeに変換
        if (!empty($data['KeyResult']['start_date'])) {
            $data['KeyResult']['start_date'] = strtotime($data['KeyResult']['start_date']) - ($this->me['timezone'] * 60 * 60);
        }
        //期限を+1day-1secする
        if (!empty($data['KeyResult']['end_date'])) {
            $data['KeyResult']['end_date'] = strtotime('+1 day -1 sec',
                                                       strtotime($data['KeyResult']['end_date'])) - ($this->me['timezone'] * 60 * 60);
        }
        $this->create();
        if (!$this->save($data)) {
            throw new RuntimeException(__d('gl', "基準の保存に失敗しました。"));
        }
        return true;
    }

    function getKeyResults($goal_id, $find_type = "all")
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'order'      => [
                'KeyResult.progress ASC',
                'KeyResult.start_date ASC',
                'KeyResult.end_date ASC',
                'KeyResult.priority DESC',
            ]
        ];
        $res = $this->find($find_type, $options);
        return $res;
    }

    function getKrCount($goal_ids, $user_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_ids,
                'user_id' => $user_id,
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    function getGoalTotalProgress($goal_ids)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_ids,
            ],
            'fields'     => ['floor(sum(KeyResult.progress) / count(*)) as progress'],
        ];
        $res = $this->find('all', $options);
        if (viaIsSet($res[0][0]['progress'])) {
            return $res[0][0]['progress'];
        }
        return 0;
    }

    /**
     * キーリザルト変更権限
     * コラボレータならtrueを返す
     *
     * @param $kr_id
     *
     * @return bool
     */
    function isPermitted($kr_id)
    {
        $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $kr_id]]);
        if (empty($key_result)) {
            return false;
        }
        $goal = $this->Goal->getGoalMinimum($key_result['KeyResult']['goal_id']);
        if (empty($goal)) {
            return false;
        }
        return $this->Goal->Collaborator->isCollaborated($goal['Goal']['id']);
    }

    function saveEdit($data)
    {
        if (!isset($data['KeyResult']) || empty($data['KeyResult'])) {
            return false;
        }
        //on/offの場合は現在値0,目標値1をセット
        if ($data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY) {
            $data['KeyResult']['start_value'] = 0;
            $data['KeyResult']['current_value'] = 0;
            $data['KeyResult']['target_value'] = 1;
        }
        //時間をunixtimeに変換
        $data['KeyResult']['start_date'] = strtotime($data['KeyResult']['start_date']) - ($this->me['timezone'] * 60 * 60);
        $data['KeyResult']['end_date'] = strtotime('+1 day -1 sec',
                                                   strtotime($data['KeyResult']['end_date'])) - ($this->me['timezone'] * 60 * 60);
//TODO 現在値を使わないため、この計算は行わない
//        $data['KeyResult']['progress'] = $this->getProgress($data['KeyResult']['start_value'],
//                                                            $data['KeyResult']['target_value'],
//                                                            $data['KeyResult']['current_value']);
        return $this->save($data);
    }

    function complete($kr_id)
    {
        $current_kr = $this->findById($kr_id);
        if (empty($current_kr)) {
            throw new RuntimeException(__d('gl', "成果が存在しません。"));
        }
        $this->id = $kr_id;
        $this->saveField('current_value', $current_kr['KeyResult']['target_value']);
        $this->saveField('progress', 100);
        $this->saveField('completed', REQUEST_TIMESTAMP);
        return true;
    }

    function incomplete($kr_id)
    {
        $current_kr = $this->findById($kr_id);
        if (empty($current_kr)) {
            throw new RuntimeException(__d('gl', "成果が存在しません。"));
        }
        $current_kr['KeyResult']['completed'] = null;
        unset($current_kr['KeyResult']['modified']);
        //progressを元に戻し、current_valueにstart_valueをsetする
        $current_kr['KeyResult']['progress'] = 0;
        $current_kr['KeyResult']['current_value'] = $current_kr['KeyResult']['start_value'];
        $this->save($current_kr);
        return true;
    }

    function getProgress($start_val, $target_val, $current_val)
    {
        $progress = round(($current_val - $start_val) / ($target_val - $start_val), 2) * 100;
        if ($progress < 0) {
            return 0;
        }
        return $progress;
    }

}
