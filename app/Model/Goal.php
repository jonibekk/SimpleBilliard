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
        'purpose'      => [
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
        'Post'             => [
            'dependent' => true,
        ],
        'KeyResult'        => [
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
            //新規の場合はデフォルトKRを追加
            if (!isset($data['KeyResult'][0]['id']) && isset($data['Goal']['id'])) {
                $kr = $data['KeyResult'][0];
                $kr['goal_id'] = $data['Goal']['id'];
                $kr['name'] = __d('gl', "タイトルを入れてください");
                $kr['special_flg'] = false;
                $kr['priority'] = 0;
                $kr['current_value'] = 0;
                $kr['start_value'] = 0;
                $kr['target_value'] = 100;
                $kr['value_unit'] = KeyResult::UNIT_PERCENT;
                $this->KeyResult->create();
                $this->KeyResult->save($kr);
            }
        }
        $this->create();
        $res = $this->saveAll($data);
        //SKRユーザの保存
        if ($this->KeyResult->getLastInsertID()) {
            $this->KeyResult->KeyResultUser->add($this->KeyResult->getLastInsertID(), null, KeyResultUser::TYPE_OWNER);
        }
        return $res;
    }

    /**
     * オーナー権限チェック
     *
     * @param $id
     *
     * @return bool
     * @throws RuntimeException
     */
    function isPermittedAdmin($id)
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

    /**
     * コラボレータ権限チェック
     *
     * @param $skr_id
     *
     * @return bool
     */
    function isPermittedCollaboFromSkr($skr_id)
    {
        $this->KeyResult->id = $skr_id;
        if (!$this->KeyResult->exists()) {
            throw new RuntimeException(__d('gl', "このゴールは存在しません。"));
        }

        if (!$this->KeyResult->KeyResultUser->isCollaborated($skr_id)) {
            throw new RuntimeException(__d('gl', "このゴールの編集の権限がありません。"));
        }
        return true;
    }

    function getAddData($id)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.id' => $id,
            ],
            'contain'    => [
                'KeyResult' => [
                    'conditions' => [
                        'KeyResult.start_date >' => $start_date,
                        'KeyResult.end_date <'   => $end_date,
                        'KeyResult.team_id'      => $this->current_team_id,
                        'KeyResult.special_flg'  => true,
                    ]
                ]
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
     * 自分が作成したゴール取得
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
                    ],

                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => false,
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

        /**
         * ソート
         * ソートは優先順位が低いものから処理する
         */
        //・第４優先ソート【進捗更新日】
        //　進捗更新日が近→遠。
        //　つまり、「進捗更新日」をデータ登録すること。
        //　目的作成や基準作成時は、0%としての更新があったとする。
        $res = $this->sortModified($res);

        //・第３優先ソート【期限】
        //　期限が近→遠
        $res = $this->sortEndDate($res);

        //・第２優先ソート【重要度】
        //　重要度が高→低
        $res = $this->sortPriority($res);

        //・第１優先ソート【基準ある/なし】
        //　基準登録がなし→ある
        $res = $this->sortExistsSpecialKeyResult($res);

        return $res;
    }

    /**
     * 期限が近→遠　で並べ替え
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortEndDate($goals, $direction = SORT_ASC)
    {
        $end_date_list = array();
        foreach ($goals as $key => $goal) {
            if (isset($goal['SpecialKeyResult'][0]['end_date'])) {
                $end_date_list[$key] = $goal['SpecialKeyResult'][0]['end_date'];
            }
            else {
                //基準なしは下に
                $end_date_list[$key] = 99999999999999999;
            }
        }
        array_multisort($end_date_list, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 進捗更新日で並べ替え 近→遠
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortModified($goals, $direction = SORT_DESC)
    {
        $modify_list = array();
        foreach ($goals as $key => $goal) {
            $modify_list[$key] = $goal['Goal']['modified'];
        }
        array_multisort($modify_list, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 重要度が高→低 で並べ替え
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortPriority($goals, $direction = SORT_DESC)
    {
        $priority_list = array();
        foreach ($goals as $key => $goal) {
            $priority_list[$key] = $goal['Goal']['priority'];
        }
        array_multisort($priority_list, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 基準登録がなし→ある で並べ替え
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortExistsSpecialKeyResult($goals, $direction = SORT_ASC)
    {
        $exists_fkr = array();
        foreach ($goals as $key => $goal) {
            $exists_fkr[$key] = 0;
            if (!empty($goal['SpecialKeyResult'])) {
                $exists_fkr[$key] = 1;
            }
        }
        array_multisort($exists_fkr, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 自分がこらぼったゴール取得
     *
     * @return array
     */
    function getMyCollaboGoals()
    {
        $goal_ids = $this->KeyResult->getCollaboGoalList($this->my_uid);

        $res = $this->getByGoalId($goal_ids);
        $res = $this->sortModified($res);
        $res = $this->sortEndDate($res);
        $res = $this->sortPriority($res);

        return $res;
    }

    function getMyFollowedGoals()
    {
        $goal_ids = $this->KeyResult->getFollowGoalList($this->my_uid);

        $res = $this->getByGoalId($goal_ids);
        $res = $this->sortModified($res);
        $res = $this->sortEndDate($res);
        $res = $this->sortPriority($res);

        return $res;
    }

    function getByGoalId($goal_ids)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.id'      => $goal_ids,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'SpecialKeyResult' => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'SpecialKeyResult.special_flg'   => true,
                        'SpecialKeyResult.start_date >=' => $start_date,
                        'SpecialKeyResult.end_date <'    => $end_date,
                    ],

                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => false,
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ],
                    'fields'     => [
                        'KeyResult.id',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.completed',
                    ],
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
                    'MyCollabo'    => [
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
                    'MyFollow'     => [
                        'conditions' => [
                            'MyFollow.user_id' => $this->my_uid,
                        ],
                        'fields'     => [
                            'MyFollow.id',
                        ],
                    ],
                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.special_flg'   => true,
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ],
                    'fields'     => [
                        'KeyResult.id',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.completed',
                    ],
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
     * @param bool $required_skr
     *
     * @internal param int $page
     * @return array
     */
    function getAllGoals($limit = 20, $params = null, $required_skr = true)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();

        $page = 1;
        if (isset($params['named']['page']) || !empty($params['named']['page'])) {
            $page = $params['named']['page'];
            unset($params['named']['page']);
        }
        $goal_ids = $this->KeyResult->getGoalIdsExistsSkr($start_date, $end_date);
        $options = [
            'conditions' => [
                'Goal.id'      => $goal_ids,
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
                    'MyCollabo'    => [
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
                    'MyFollow'     => [
                        'conditions' => [
                            'MyFollow.user_id' => $this->my_uid,
                        ],
                        'fields'     => [
                            'MyFollow.id',
                        ],
                    ],
                ],
                'KeyResult'        => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ],
                    'fields'     => [
                        'KeyResult.id',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.completed',
                    ],
                ],
                'User'             => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        $res = $this->find('all', $options);
        //skr必須指定の場合はskrが存在しないゴールを除去
        if ($required_skr) {
            foreach ($res as $key => $val) {
                if (isset($val['SpecialKeyResult']) && empty($val['SpecialKeyResult'])) {
                    unset($res[$key]);
                }
            }
        }
        //進捗を計算
        foreach ($res as $key => $goal) {
            $res[$key]['Goal']['progress'] = $this->getProgress($goal);
        }

        return $res;
    }

    function getProgress($goal)
    {
        $res = 0;
        if (empty($goal['KeyResult'])) {
            return $res;
        }

        $target_progress_total = 0;
        $current_progress_total = 0;
        foreach ($goal['KeyResult'] as $key_result) {
            $target_progress_total += $key_result['priority'] * 100;
            $current_progress_total += $key_result['priority'] * $key_result['progress'];
        }
        if ($target_progress_total != 0) {
            $res = round($current_progress_total / $target_progress_total, 2) * 100;
        }
        return $res;
    }

}
