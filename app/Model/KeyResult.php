<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team              $Team
 * @property Goal              $Goal
 * @property Follower          $Follower
 * @property Collaborator      $Collaborator
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
        self::UNIT_NUMBER  => "",
        self::UNIT_BINARY  => "",
        self::UNIT_YEN     => "",
        self::UNIT_DOLLAR  => "",
    ];

    /**
     * 目標値の単位の表示名をセット
     */
    private function _setUnitName()
    {
        self::$UNIT[self::UNIT_PERCENT] = __d('gl', "%");
        self::$UNIT[self::UNIT_NUMBER] = __d('gl', "数値");
        self::$UNIT[self::UNIT_BINARY] = __d('gl', 'ON/OFF');
        self::$UNIT[self::UNIT_YEN] = __d('gl', '¥');
        self::$UNIT[self::UNIT_DOLLAR] = __d('gl', '$');
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
        'name'        => [
            'notEmpty' => [
                'rule' => 'notEmpty',
            ],
        ],
        'valued_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'special_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'     => [
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
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
    }

    function getCollaboGoalList($user_id)
    {
        $key_result_ids = $this->Collaborator->getCollaboKeyResultList($user_id);
        $options = [
            'conditions' => [
                'id' => $key_result_ids,
            ],
            'fields'     => [
                'goal_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function getFollowGoalList($user_id)
    {
        $key_result_ids = $this->Follower->getFollowList($user_id);
        $options = [
            'conditions' => [
                'id' => $key_result_ids,
            ],
            'fields'     => [
                'goal_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * キーリザルトが現在のチームで有効かどうか
     *
     * @param $id
     *
     * @return bool
     */
    function isBelongCurrentTeam($id)
    {
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id
            ],
            'fields'     => [
                'id'
            ]
        ];
        if ($this->find('first', $options)) {
            return true;
        }
        return false;
    }

    function getGoalIdsExistsSkr($start_date, $end_date)
    {
        $options = [
            'conditions' => [
                'KeyResult.start_date >=' => $start_date,
                'KeyResult.end_date <'    => $end_date,
                'KeyResult.special_flg'   => true,
                'KeyResult.team_id'       => $this->current_team_id,
            ],
            'fields'     => ['KeyResult.goal_id']
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function getCollaboModalItem($id)
    {
        $options = [
            'conditions' => [
                'KeyResult.id'      => $id,
                'KeyResult.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'MyCollabo' => [
                    'conditions' => [
                        'MyCollabo.type'    => Collaborator::TYPE_COLLABORATOR,
                        'MyCollabo.user_id' => $this->my_uid,
                    ],
                    'fields'     => [
                        'MyCollabo.id',
                        'MyCollabo.role',
                        'MyCollabo.description',
                    ],
                ],
            ],
        ];
        $res = $this->find('first', $options);
        return $res;
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

    function getKeyResults($goal_id, $with_skr = false)
    {
        $options = [
            'conditions' => [
                'goal_id'     => $goal_id,
                'team_id'     => $this->current_team_id,
                'special_flg' => false,
            ],
        ];
        if ($with_skr) {
            unset($options['conditions']['special_flg']);
        }
        $res = $this->find('all', $options);
        return $res;
    }

    function getSkr($goal_id)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();

        $options = [
            'conditions' => [
                'goal_id'       => $goal_id,
                'special_flg'   => true,
                'start_date >=' => $start_date,
                'end_date <'    => $end_date
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * キーリザルト変更権限
     * コラボレータならtrueを返す
     *
     * @param $key_result_id
     *
     * @return bool
     */
    function isPermitted($key_result_id)
    {
        $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $key_result_id]]);
        if (empty($key_result)) {
            return false;
        }
        $skr = $this->Goal->KeyResult->getSkr($key_result['KeyResult']['goal_id']);
        if (empty($skr)) {
            return false;
        }
        return $this->Goal->KeyResult->Collaborator->isCollaborated($skr['KeyResult']['id']);
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
        $data['KeyResult']['progress'] = $this->getProgress($data['KeyResult']['start_value'],
                                                            $data['KeyResult']['target_value'],
                                                            $data['KeyResult']['current_value']);
        $this->create();
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
        $this->saveField('completed', time());
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
        $this->create();
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
