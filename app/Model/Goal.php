<?php
App::uses('AppModel', 'Model');
App::uses('KeyResultUser', 'Model');
App::uses('KeyResult', 'Model');

/**
 * Goal Model
 *
 * @property User              $User
 * @property Team              $Team
 * @property GoalCategory      $GoalCategory
 * @property Post              $Post
 * @property KeyResult         $KeyResult
 */
class Goal extends AppModel
{
    /**
     * ステータス
     */
    const STATUS_DOING = 0;
    const STATUS_PAUSE = 1;
    const STATUS_COMPLETE = 2;
    static public $STATUS = [self::STATUS_DOING => "", self::STATUS_PAUSE => "", self::STATUS_COMPLETE => ""];

    /**
     * ステータスの名前をセット
     */
    private function _setStatusName()
    {
        self::$STATUS[self::STATUS_DOING] = __d('gl', "進行中");
        self::$STATUS[self::STATUS_PAUSE] = __d('gl', "中断");
        self::$STATUS[self::STATUS_COMPLETE] = __d('gl', "完了");
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
    public $displayField = 'goal';

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-goal.jpg',
                'quality'     => 100,
            ]
        ]
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'purpose' => [
            'notEmpty' => [
                'rule' => 'notEmpty',
            ],
        ],
        'evaluate_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'status'       => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'priority'     => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'photo'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'GoalCategory',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post'      => [
            'dependent' => true,
        ],
        'KeyResult' => [
            'dependent' => true,
        ],
        'SpecialKeyResult' => [
            'className' => 'KeyResult'
        ],
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setStatusName();
    }

    function add($data)
    {
        if (!isset($data['Goal']) || empty($data['Goal'])) {
            return false;
        }
        $data['Goal']['team_id'] = $this->current_team_id;
        $data['Goal']['user_id'] = $this->my_uid;
        //KeyResultの処理
        //KeyResultの名前が存在しない場合はKeyResultを保存しない。
        if (!isset($data['KeyResult'][0]['name']) || empty($data['KeyResult'][0]['name'])) {
            unset($data['KeyResult']);
        }
        else {
            //SKRをセット
            $data['KeyResult'][0]['team_id'] = $this->current_team_id;
            $data['KeyResult'][0]['user_id'] = $this->my_uid;
            $data['KeyResult'][0]['special_flg'] = true;
            //on/offの場合は現在値0,目標値1をセット
            if ($data['KeyResult'][0]['value_unit'] == KeyResult::UNIT_BINARY) {
                $data['KeyResult'][0]['start_value'] = 0;
                $data['KeyResult'][0]['target_value'] = 1;
            }
            $data['KeyResult'][0]['current_value'] = $data['KeyResult'][0]['start_value'];

            //時間をunixtimeに変換
            if (!empty($data['KeyResult'][0]['start_date'])) {
                $data['KeyResult'][0]['start_date'] = strtotime($data['KeyResult'][0]['start_date']) - ($this->me['timezone'] * 60 * 60);
            }
            //期限を+1day-1secする
            if (!empty($data['KeyResult'][0]['end_date'])) {
                $data['KeyResult'][0]['end_date'] = strtotime('+1 day -1 sec',
                                                              strtotime($data['KeyResult'][0]['end_date'])) - ($this->me['timezone'] * 60 * 60);
            }
        }
        $res = $this->saveAll($data);
        //SKRユーザの保存
        if ($this->KeyResult->getLastInsertID()) {
            $this->KeyResult->KeyResultUser->add($this->KeyResult->getLastInsertID(), null, KeyResultUser::TYPE_OWNER);
        }
        return $res;
    }

    /**
     * 権限チェック
     *
     * @param $id
     *
     * @return bool
     * @throws RuntimeException
     */
    function isPermitted($id)
    {
        $this->id = $id;
        if (!$this->exists()) {
            throw new RuntimeException(__d('gl', "このゴールは存在しません。"));
        }
        if (!$this->isOwner($this->my_uid, $id)) {
            throw new RuntimeException(__d('gl', "このゴールの編集の権限がありません。"));
        }
        return true;
    }

    function getAddData($id)
    {
        $options = [
            'conditions' => [
                'Goal.id' => $id,
            ],
            'contain'    => [
                'KeyResult'
            ]
        ];
        $res = $this->find('first', $options);
        //基準の数値を変換
        if (!empty($res['KeyResult'])) {
            foreach ($res['KeyResult'] as $k => $k_val) {
                $res['KeyResult'][$k]['start_value'] = (double)$k_val['start_value'];
                $res['KeyResult'][$k]['current_value'] = (double)$k_val['current_value'];
                $res['KeyResult'][$k]['target_value'] = (double)$k_val['target_value'];
            }
        }
        return $res;
    }

    /**
     * 自分のゴール取得
     *
     * @return array
     */
    function getMyGoals()
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.user_id' => $this->my_uid,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'SpecialKeyResult' => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'SpecialKeyResult.special_flg'   => true,
                        'SpecialKeyResult.start_date >=' => $start_date,
                        'SpecialKeyResult.end_date <'    => $end_date,
                    ]
                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => true,
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ]
                ],
            ]
        ];
        $res = $this->find('all', $options);
        //進捗を計算
        foreach ($res as $key => $goal) {
            $res[$key]['Goal']['progress'] = $this->getProgress($goal);
        }

        return $res;
    }

    /**
     * ゴール単独取得
     *
     * @param $id
     *
     * @return array
     */
    function getGoal($id)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'SpecialKeyResult' => [
                    //KeyResultは期限が今期内
                    'conditions'   => [
                        'SpecialKeyResult.special_flg'   => true,
                        'SpecialKeyResult.start_date >=' => $start_date,
                        'SpecialKeyResult.end_date <'    => $end_date,
                    ],
                    'Leader'       => [
                        'conditions' => ['Leader.type' => KeyResultUser::TYPE_OWNER],
                        'User'       => [
                            'fields' => $this->User->profileFields,
                        ]
                    ],
                    'Collaborator' => [
                        'conditions' => ['Collaborator.type' => KeyResultUser::TYPE_COLLABORATOR],
                        'User'       => [
                            'fields' => $this->User->profileFields,
                        ]
                    ],
                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => true,
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ]
                ],
                'User'             => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        $res = $this->find('first', $options);
        $res['Goal']['progress'] = $this->getProgress($res);

        return $res;
    }

    /**
     * 全てのゴール取得
     *
     * @param int  $limit
     * @param null $params
     *
     * @internal param int $page
     * @return array
     */
    function getAllGoals($limit = 20, $params = null)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();

        $page = 1;
        if (isset($params['named']['page']) || !empty($params['named']['page'])) {
            $page = $params['named']['page'];
            unset($params['named']['page']);
        }
        $options = [
            'conditions' => [
                'Goal.team_id' => $this->current_team_id,
            ],
            'order'      => ['Goal.modified desc'],
            'limit'      => $limit,
            'page'       => $page,
            'contain'    => [
                'SpecialKeyResult' => [
                    //KeyResultは期限が今期内
                    'conditions'   => [
                        'SpecialKeyResult.special_flg'   => true,
                        'SpecialKeyResult.start_date >=' => $start_date,
                        'SpecialKeyResult.end_date <'    => $end_date,
                    ],
                    'Leader'       => [
                        'conditions' => ['Leader.type' => KeyResultUser::TYPE_OWNER],
                        'User'       => [
                            'fields' => $this->User->profileFields,
                        ]
                    ],
                    'Collaborator' => [
                        'conditions' => ['Collaborator.type' => KeyResultUser::TYPE_COLLABORATOR],
                        'User'       => [
                            'fields' => $this->User->profileFields,
                        ]
                    ],
                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => true,
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ]
                ],
                'User'             => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        $res = $this->find('all', $options);
        //進捗を計算
        foreach ($res as $key => $goal) {
            $res[$key]['Goal']['progress'] = $this->getProgress($goal);
        }

        return $res;
    }

    function getProgress($goal)
    {
        if (empty($goal['KeyResult'])) {
            return 0;
        }
        //全体の重要度の合計
        $total_priority = 0;
        foreach ($goal['KeyResult'] as $key_result) {
            $total_priority += $key_result['priority'];
        }

        //完了のプライオリティを計算
        $completed_total_priority = 0;
        foreach ($goal['KeyResult'] as $key_result) {
            if (!empty($key_result['completed'])) {
                $completed_total_priority += $key_result['priority'];
            }
        }
        if ($total_priority === 0 || $completed_total_priority === 0) {
            return 0;
        }
        $res = round($completed_total_priority / $total_priority, 2) * 100;
        return $res;
    }

}
