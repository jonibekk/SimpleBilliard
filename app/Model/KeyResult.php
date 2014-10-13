<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team              $Team
 * @property Goal              $Goal
 * @property Follower          $Follower
 * @property KeyResultUser     $KeyResultUser
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
        'name'       => [
            'notEmpty' => [
                'rule' => 'notEmpty',
            ],
        ],
        'valued_flg' => [
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
        'KeyResultUser' => [
            'dependent' => true,
        ],
        'Leader'        => [
            'className' => 'KeyResultUser',
        ],
        'Collaborator'  => [
            'className' => 'KeyResultUser',
        ],
        'MyCollabo'     => [
            'className' => 'KeyResultUser',
        ],
        'Follower',
        'MyFollow'      => [
            'className' => 'Follower',
        ],
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
    }

    function getCollaboGoalList($user_id)
    {
        $key_result_ids = $this->KeyResultUser->getCollaboKeyResultList($user_id);
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
                        'MyCollabo.type'    => KeyResultUser::TYPE_COLLABORATOR,
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
        if ($this->save($data)) {
            return true;
        }

        throw new RuntimeException(__d('gl', "基準の保存に失敗しました。"));
    }

}
