<?php
App::uses('AppModel', 'Model');
App::uses('Collaborator', 'Model');
App::uses('KeyResult', 'Model');

/**
 * Goal Model
 *
 * @property User                    $User
 * @property Team                    $Team
 * @property GoalCategory            $GoalCategory
 * @property Post                    $Post
 * @property KeyResult               $KeyResult
 * @property Collaborator            $Collaborator
 * @property Follower                $Follower
 * @property Purpose                 $Purpose
 * @property ActionResult            $ActionResult
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

    /**
     * 重要度の名前をセット
     */
    private function _setPriorityName()
    {
        $this->priority_list[0] = __d('gl', "0 (認定対象外)");
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
        'Purpose' => [
            "counterCache" => true,
            'counterScope' => ['Purpose.del_flg' => false]
        ],
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post'                => [
            'dependent' => true,
        ],
        'KeyResult'           => [
            'dependent' => true,
        ],
        'ActionResult'        => [
            'dependent' => true,
        ],
        'IncompleteKeyResult' => [
            'className' => 'KeyResult'
        ],
        'Collaborator'        => [
            'dependent' => true,
        ],
        'Leader'              => [
            'className' => 'Collaborator',
        ],
        'MyCollabo'           => [
            'className' => 'Collaborator',
        ],
        'Follower'            => [
            'dependent' => true,
        ],
        'MyFollow'            => [
            'className' => 'Follower',
        ],
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setStatusName();
        $this->_setPriorityName();
    }

    function add($data)
    {
        if (!isset($data['Goal']) || empty($data['Goal'])) {
            return false;
        }
        $add_new = false;
        if (!isset($data['Goal']['id'])) {
            $add_new = true;
        }
        $data['Goal']['team_id'] = $this->current_team_id;
        $data['Goal']['user_id'] = $this->my_uid;
        //on/offの場合は現在値0,目標値1をセット
        if (isset($data['Goal']['value_unit']) && isset($data['Goal']['start_value'])) {
            if ($data['Goal']['value_unit'] == KeyResult::UNIT_BINARY) {
                $data['Goal']['start_value'] = 0;
                $data['Goal']['target_value'] = 1;
            }
            $data['Goal']['current_value'] = $data['Goal']['start_value'];
        }

        //時間をunixtimeに変換
        if (!empty($data['Goal']['start_date'])) {
            $data['Goal']['start_date'] = strtotime($data['Goal']['start_date']) - ($this->me['timezone'] * 60 * 60);
        }
        //期限を+1day-1secする
        if (!empty($data['Goal']['end_date'])) {
            $data['Goal']['end_date'] = strtotime('+1 day -1 sec',
                                                  strtotime($data['Goal']['end_date'])) - ($this->me['timezone'] * 60 * 60);
        }
        //新規の場合はデフォルトKRを追加
        if ($add_new) {
            $kr['name'] = __d('gl', "出したい成果の名前を入れてください");
            $kr['priority'] = 3;
            $kr['current_value'] = 0;
            $kr['start_value'] = 0;
            $kr['target_value'] = 100;
            $kr['value_unit'] = KeyResult::UNIT_PERCENT;
            $kr['start_date'] = $data['Goal']['start_date'];
            $kr['end_date'] = $data['Goal']['end_date'];
            $kr['team_id'] = $this->current_team_id;
            $kr['user_id'] = $this->my_uid;
            $data['KeyResult'][0] = $kr;
            //コラボレータをタイプ　リーダーで保存
            $data['Collaborator'][0]['user_id'] = $this->my_uid;
            $data['Collaborator'][0]['team_id'] = $this->current_team_id;
            $data['Collaborator'][0]['type'] = Collaborator::TYPE_OWNER;
        }
        $this->create();
        $res = $this->saveAll($data);
        if ($add_new) {
            //ゴール投稿
            $this->Post->addGoalPost(Post::TYPE_CREATE_GOAL, $this->getLastInsertID());
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

    function getAddData($id)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.id' => $id,
            ],
            'contain'    => [
                'KeyResult'    => [
                    'conditions' => [
                        'KeyResult.start_date >' => $start_date,
                        'KeyResult.end_date <'   => $end_date,
                        'KeyResult.team_id'      => $this->current_team_id,
                    ]
                ],
                'Purpose',
                'Collaborator' => [
                    'conditions' => [
                        'Collaborator.user_id' => $this->my_uid
                    ]
                ],
            ]
        ];
        $res = $this->find('first', $options);
        //基準の数値を変換
        $res['Goal']['start_value'] = (double)$res['Goal']['start_value'];
        $res['Goal']['current_value'] = (double)$res['Goal']['current_value'];
        $res['Goal']['target_value'] = (double)$res['Goal']['target_value'];

        //KRの数値を変換
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
	 * user_idからgoal_idを取得する
	 * @param $user_id
	 * @param $team_id
	 * @return array|null
	 */
	function getGoalIdFromUserId ($user_id, $team_id) {
		$options = [
			'fields' => ['id'],
			'conditions' => [
				'Goal.user_id'       => $user_id,
				'Goal.team_id'       => $team_id,
				'Goal.start_date >=' => $this->Team->getTermStartDate(),
				'Goal.end_date <'    => $this->Team->getTermEndDate(),
				'Goal.del_flg'       => 0,
			],
		];
		return $this->find('list', $options);
	}

    /**
     * 自分が作成したゴール取得
     *
     * @return array
     */
    function getMyGoals($limit = null, $page = 1)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.user_id'       => $this->my_uid,
                'Goal.team_id'       => $this->current_team_id,
                'Goal.start_date >=' => $start_date,
                'Goal.end_date <'    => $end_date,
            ],
            'contain'    => [
                'MyCollabo' => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
                    ]
                ],
                'KeyResult' => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.start_date >=' => $start_date,
                        'KeyResult.end_date <'    => $end_date,
                    ]
                ],
                'Purpose',
            ],
            'limit'      => $limit,
            'page'       => $page
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

        //目的一覧を取得
        if (!empty($purposes = $this->Purpose->getPurposesNoGoal())) {
            foreach ($purposes as $key => $val) {
                $purposes[$key]['Goal'] = [];
            }
            /** @noinspection PhpParamsInspection */
            $res = array_merge($purposes, $res);
        }

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
            if (isset($goal['Goal']['end_date'])) {
                $end_date_list[$key] = $goal['Goal']['end_date'];
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
            if (isset($goal['MyCollabo'][0]['priority'])) {
                $priority_list[$key] = $goal['MyCollabo'][0]['priority'];
            }
        }
        if (!empty($priority_list)) {
            array_multisort($priority_list, $direction, SORT_NUMERIC, $goals);
        }
        return $goals;
    }

    function getMyCreateGoalsList($uid)
    {
        return $this->find(
            'list',
            [
                'conditions' => [
                    'Goal.user_id' => $uid
                ],
                'fields'     => [
                    'id'
                ]
            ]
        );
    }

    /**
     * 自分がこらぼったゴール取得
     *
     * @return array
     */
    function getMyCollaboGoals($limit = null, $page = 1)
    {
        $goal_ids = $this->Collaborator->getCollaboGoalList($this->my_uid, false, $limit, $page);
        $res = $this->getByGoalId($goal_ids);
        $res = $this->sortModified($res);
        $res = $this->sortEndDate($res);
        $res = $this->sortPriority($res);

        return $res;
    }

    function getMyFollowedGoals($limit = null, $page = 1)
    {
        $goal_ids = $this->Follower->getFollowList($this->my_uid, $limit, $page);
        $res = $this->getByGoalId($goal_ids);
        $res = $this->sortModified($res);
        $res = $this->sortEndDate($res);

        return $res;
    }

    function getByGoalId($goal_ids)
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'Goal.id'            => $goal_ids,
                'Goal.team_id'       => $this->current_team_id,
                'Goal.start_date >=' => $start_date,
                'Goal.end_date <'    => $end_date,
            ],
            'contain'    => [
                'Purpose',
                'KeyResult' => [
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
                'MyCollabo' => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
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
                'Goal.id'            => $id,
                'Goal.team_id'       => $this->current_team_id,
                'Goal.start_date >=' => $start_date,
                'Goal.end_date <'    => $end_date,
            ],
            'contain'    => [
                'Purpose',
                'GoalCategory',
                'Leader'       => [
                    'conditions' => ['Leader.type' => Collaborator::TYPE_OWNER],
                    'fields'     => ['Leader.id', 'Leader.user_id'],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'Collaborator' => [
                    'conditions' => ['Collaborator.type' => Collaborator::TYPE_COLLABORATOR],
                    'fields'     => ['Collaborator.id', 'Collaborator.user_id'],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'Follower'     => [
                    'fields' => ['Follower.id', 'Follower.user_id'],
                    'User'   => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'MyCollabo'    => [
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
                'MyFollow'     => [
                    'conditions' => [
                        'MyFollow.user_id' => $this->my_uid,
                    ],
                    'fields'     => [
                        'MyFollow.id',
                    ],
                ],
                'KeyResult'    => [
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
                'User'         => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        $res = $this->find('first', $options);
        $res['Goal']['progress'] = $this->getProgress($res);

        return $res;
    }

    function getGoalMinimum($id)
    {
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            $res['Goal']['progress'] = $this->getProgress($res);
            //不要な少数を除去
            $res['Goal']['start_value'] = (double)$res['Goal']['start_value'];
            $res['Goal']['current_value'] = (double)$res['Goal']['current_value'];
            $res['Goal']['target_value'] = (double)$res['Goal']['target_value'];
        }

        return $res;
    }

    /**
     * 全てのゴール取得
     *
     * @param int  $limit
     * @param null $params
     *
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
                'Goal.team_id'       => $this->current_team_id,
                'Goal.start_date >=' => $start_date,
                'Goal.end_date <'    => $end_date,
            ],
            'order'      => ['Goal.modified desc'],
            'limit'      => $limit,
            'page'       => $page,
            'contain'    => [
                'Purpose',
                'Leader'       => [
                    'conditions' => ['Leader.type' => Collaborator::TYPE_OWNER],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'Collaborator' => [
                    'conditions' => ['Collaborator.type' => Collaborator::TYPE_COLLABORATOR],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'MyCollabo'    => [
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
                'MyFollow'     => [
                    'conditions' => [
                        'MyFollow.user_id' => $this->my_uid,
                    ],
                    'fields'     => [
                        'MyFollow.id',
                    ],
                ],
                'KeyResult'    => [
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
                'User'         => [
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

    function complete($goal_id)
    {
        $goal = $this->findById($goal_id);
        if (empty($goal)) {
            throw new RuntimeException(__d('gl', "ゴールが存在しません。"));
        }
        $this->id = $goal_id;
        $this->saveField('current_value', $goal['Goal']['target_value']);
        $this->saveField('progress', 100);
        $this->saveField('completed', REQUEST_TIMESTAMP);
        return true;
    }

    function incomplete($goal_id)
    {
        $goal = $this->findById($goal_id);
        if (empty($goal)) {
            throw new RuntimeException(__d('gl', "ゴールが存在しません。"));
        }
        $goal['Goal']['completed'] = null;
        unset($goal['Goal']['modified']);
        $this->create();
        $this->save($goal);
        return true;
    }

    function getCollaboModalItem($id)
    {
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
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
                        'MyCollabo.priority',
                    ],
                ],
            ],
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function getAllUserGoal($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = $this->Team->getTermStartDate();
        }
        if (!$end_date) {
            $end_date = $this->Team->getTermEndDate();
        }
        $team_member_list = $this->Team->TeamMember->getAllMemberUserIdList();
        $options = [
            'conditions' => [
                'User.id' => $team_member_list
            ],
            'fields'     => $this->User->profileFields,
            'contain'    => [
                'LocalName'    => [
                    'conditions' => ['LocalName.language' => $this->me['language']],
                ],
                'Collaborator' => [
                    'conditions' => [
                        'Collaborator.team_id' => $this->current_team_id,
                    ],
                    'Goal'       => [
                        'conditions' => [
                            'Goal.start_date >=' => $start_date,
                            'Goal.end_date <'    => $end_date
                        ],
                        'Purpose',
                        'GoalCategory',
                    ]
                ]
            ]
        ];
        $res = $this->Collaborator->User->find('all', $options);
        return $res;
    }

}
