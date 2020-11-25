<?php
App::uses('AppModel', 'Model');

/**
 * GoalMember Model
 *
 * @property Team $Team
 * @property Goal $Goal
 * @property User $User
 */
class GoalMember extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_COLLABORATOR = 0;
    const TYPE_OWNER = 1;

    static public $TYPE = [
        self::TYPE_COLLABORATOR => "",
        self::TYPE_OWNER        => "",
    ];

    const APPROVAL_STATUS_NEW = 0;
    const APPROVAL_STATUS_REAPPLICATION = 1;
    const APPROVAL_STATUS_DONE = 2;
    const APPROVAL_STATUS_WITHDRAWN = 3;

    // TODO: 中身をセットする処理は未実装。表示文言が決まり次第実装する。
    static public $STATUS = [
        self::APPROVAL_STATUS_NEW           => "",
        self::APPROVAL_STATUS_REAPPLICATION => "",
        self::APPROVAL_STATUS_DONE          => "",
        self::APPROVAL_STATUS_WITHDRAWN     => ""
    ];

    /**
     * 評価対象判定
     */
    const IS_NOT_TARGET_EVALUATION = 0;
    const IS_TARGET_EVALUATION = 1;

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_COLLABORATOR] = __("Collaborator");
        self::$TYPE[self::TYPE_OWNER] = __("Leader");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'role'        => [
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notBlank'  => ['rule' => 'notBlank',],
        ],
        'description' => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
            'notBlank'  => ['rule' => 'notBlank',],
        ],
        'type'        => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'priority'    => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
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
        'User',
    ];

    public $hasMany = [
        'ApprovalHistory',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

    function add($goal_id, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $goalMember = [
            'team_id' => $this->current_team_id,
            'user_id' => $uid,
            'type'    => $type,
            'goal_id' => $goal_id,
        ];
        $res = $this->save($goalMember);
        return $res;
    }

    function edit($data, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!isset($data['GoalMember']) || empty($data['GoalMember'])) {
            return false;
        }
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $data['GoalMember']['user_id'] = $uid;
        $data['GoalMember']['team_id'] = $this->current_team_id;
        $data['GoalMember']['type'] = $type;

        $res = $this->save($data);
        $this->Goal->Follower->deleteFollower($data['GoalMember']['goal_id']);
        return $res;
    }

    function findEvaluatableGoalList($uid)
    {
        $options = [
            'conditions' => [
                'user_id'              => $uid,
                'is_target_evaluation' => true
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
        ];
        $ret = $this->find('list', $options);
        return $ret;
    }

    function getCollaboGoalList($user_id, $with_owner = false, $limit = null, $page = 1, $approval_status = null)
    {
        $is_default = false;
        if ($user_id == $this->my_uid && $with_owner === true && $limit === null && $page === 1 && $approval_status === null) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), 'user_data');
            if ($res !== false) {
                return $res;
            }
        }
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
                'type'    => [
                    GoalMember::TYPE_COLLABORATOR,
                ],
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        if ($with_owner) {
            unset($options['conditions']['type']);
        }
        if ($approval_status) {
            $options['conditions']['approval_status'] = $approval_status;
        }
        $res = $this->find('list', $options);

        if ($is_default) {
            //TODO: KRカラム一次リリース時点ではこのキャッシュを利用しない。このキャッシュは問題を含んでいる。詳しくは -> http://bit.ly/2jH27dF
            //Cache::write($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), $res, 'user_data');
        }
        return $res;
    }

    // for getting incomplete goal ids for goal_member right column
    function getIncompleteCollaboGoalIds(
        $user_id,
        $start_date,
        $end_date,
        $limit = null,
        $page = 1,
        $with_owner = false,
        $approval_status = null
    ) {
        $is_default = false;
        if ($user_id == $this->my_uid && $with_owner === true && $limit === null && $page === 1 && $approval_status === null) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), 'user_data');
            if ($res !== false) {
                return $res;
            }
        }
        $options = [
            'joins'      => [
                [
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                        'Goal.end_date >=' => $start_date,
                        'Goal.end_date <=' => $end_date,
                        'Goal.completed'   => null,
                    ]
                ]
            ],
            'conditions' => [
                'GoalMember.user_id' => $user_id,
                'GoalMember.team_id' => $this->current_team_id,
                'GoalMember.type'    => [
                    GoalMember::TYPE_COLLABORATOR,
                ],
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'order'      => [
                'GoalMember.priority DESC'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        if ($with_owner) {
            unset($options['conditions']['type']);
        }
        if ($approval_status) {
            $options['conditions']['approval_status'] = $approval_status;
        }
        $res = $this->find('list', $options);

        if ($is_default) {
            //TODO: KRカラム一次リリース時点ではこのキャッシュを利用しない。このキャッシュは問題を含んでいる。詳しくは -> http://bit.ly/2jH27dF
            //Cache::write($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), $res, 'user_data');
        }
        return $res;
    }

    /**
     * 自分のゴールのプライオリティを返す
     * 返り値のフォーマットkey:goal_id,value:priorityの配列
     *
     * @param int    $userId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findGoalPriorities(int $userId, string $startDate, string $endDate): array
    {
        $options = [
            'joins'      => [
                [
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                        'Goal.end_date >=' => $startDate,
                        'Goal.end_date <=' => $endDate,
                    ]
                ]
            ],
            'conditions' => [
                'GoalMember.user_id' => $userId,
                'GoalMember.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'goal_id',
                'priority',
            ],
        ];
        $ret = $this->find('list', $options);
        return $ret;
    }

    // getting incomplete goal ids for owner, for right side leader goal column
    function getIncompleteGoalIdsForRightColumn($limit, $page, $user_id, $start_date, $end_date)
    {
        $options = [
            'joins'      => [
                [
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                        'Goal.end_date >=' => $start_date,
                        'Goal.end_date <=' => $end_date,
                        'Goal.completed'   => null,
                    ]
                ]
            ],
            'conditions' => [
                'GoalMember.user_id' => $user_id,
                'GoalMember.team_id' => $this->current_team_id,
                'type'               => [
                    GoalMember::TYPE_OWNER,
                ],
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'order'      => [
                'GoalMember.priority DESC'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];

        $res = $this->find('list', $options);

        return $res;
    }

    /**
     * @param integer $user_id
     * @param array   $gids
     * @param string  $direction "desc" or "asc"
     *
     * @return array|null
     */
    function goalIdOrderByPriority($user_id, $gids, $direction = "desc")
    {
        $options = [
            'conditions' => [
                'goal_id' => $gids,
                'user_id' => $user_id,
            ],
            'order'      => ['priority' => $direction],
            'fields'     => ['goal_id', 'goal_id']
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * Check whether is collaborating in a goal
     *
     * @param int      $goalId
     * @param int|null $userId
     *
     * @return bool
     */
    public function isCollaborated(int $goalId, ?int $userId = null): bool
    {
        if (!$userId) {
            $userId = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'GoalMember.user_id' => $userId,
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    function getCollaboGoalDetail(
        $team_id,
        $goal_user_id,
        $approvalStatus = null,
        $is_include_priority_0 = true,
        $term_type = null
    ) {
        $conditions = [
            'GoalMember.team_id' => $team_id,
            'GoalMember.user_id' => $goal_user_id,
        ];
        if (!empty($approvalStatus)) {
            $conditions['GoalMember.approval_status'] = $approvalStatus;
        }
        if ($term_type !== null) {
            $conditions['Goal.end_date >='] = $this->Goal->Team->Term->getTermData($term_type)['start_date'];
            $conditions['Goal.end_date <='] = $this->Goal->Team->Term->getTermData($term_type)['end_date'];
        }

        $options = [
            'fields'     => ['id', 'type', 'role', 'priority', 'approval_status'],
            'conditions' => $conditions,
            'contain'    => [
                'Goal'            => [
                    'fields'       => [
                        'name',
                        'goal_category_id',
                        'end_date',
                        'photo_file_name',
                        'description'
                    ],
                    'GoalCategory' => ['fields' => 'name'],
                ],
                'User'            => [
                    'fields' => $this->User->profileFields
                ],
                'ApprovalHistory' => [
                    'User'   => [
                        'fields' => $this->User->profileFields
                    ],
                    'fields' => ['user_id', 'comment', 'created'],
                    'order'  => ['ApprovalHistory.created DESC'],
                    //'limit' => 1,
                ]
            ],
            'type'       => 'inner',
            'order'      => ['GoalMember.created DESC'],
        ];
        if (!$is_include_priority_0) {
            $options['conditions']['NOT'] = array('GoalMember.priority' => "0");
        }
        if (is_array($approvalStatus)) {
            unset($options['conditions']['GoalMember.approval_status']);
            foreach ($approvalStatus as $val) {
                $options['conditions']['OR'][]['GoalMember.approval_status'] = $val;
            }
        }
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * ゴール認定一覧に表示するリスト取得
     * - 認定ステータス、コラボレーター作成日の順でソート
     *
     * @param $goalUserId
     *
     * @return array|null
     */
    function findActive($goalUserId)
    {
        $currentTerm = $this->Goal->Team->Term->getTermData(Term::TYPE_CURRENT);
        $conditions = [
            'GoalMember.team_id'          => $this->current_team_id,
            'GoalMember.user_id'          => $goalUserId,
            'GoalMember.is_wish_approval' => true,
            'Goal.end_date >='            => $currentTerm['start_date'],
            'Goal.end_date <='            => $currentTerm['end_date'],
        ];

        $options = [
            'fields'     => [
                'id',
                'type',
                'role',
                'priority',
                'approval_status',
                'is_wish_approval',
                'is_target_evaluation'
            ],
            'conditions' => $conditions,
            'contain'    => [
                'Goal' => [
                    'fields' => [
                        'id',
                        'name',
                        'photo_file_name',
                    ],
                ],
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
            'type'       => 'INNER',
            'order'      => ['GoalMember.approval_status ASC', 'GoalMember.created DESC'],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function changeApprovalStatus($id, $status)
    {
        $this->id = $id;
        $this->save(['approval_status' => $status]);
        $goalMember = $this->findById($this->id);
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $goalMember['GoalMember']['user_id']),
            'user_data');
    }

    /**
     * コーチとしての未対応のゴール認定件数取得
     *
     * @param $userId
     *
     * @return int
     */
    function countUnapprovedGoal($userId)
    {
        $currentTerm = $this->Team->Term->getCurrentTermData();

        if (empty($currentTerm)) {
            return 0;
        }

        $options = [
            'fields'     => ['GoalMember.id'],
            'joins'      => [
                [
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                        'Goal.end_date >=' => $currentTerm['start_date'],
                        'Goal.end_date <=' => $currentTerm['end_date'],
                        'Goal.team_id'     => $this->current_team_id,
                        'Goal.completed'   => null,
                    ]
                ],
                [
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'type'       => 'INNER',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.coach_user_id'         => $userId,
                        'TeamMember.team_id'               => $this->current_team_id,
                        'TeamMember.evaluation_enable_flg' => true,
                    ]
                ]
            ],
            'conditions' => [
                'GoalMember.team_id'          => $this->current_team_id,
                'GoalMember.approval_status'  => [
                    self::APPROVAL_STATUS_NEW,
                    self::APPROVAL_STATUS_REAPPLICATION,
                ],
                'GoalMember.is_wish_approval' => true
            ],
        ];
        /** @var int $count */
        $count = $this->find('count', $options);
        return $count;
    }

    function getLeader(int $goalId)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goalId,
                'team_id' => $this->current_team_id,
                'type'    => [
                    GoalMember::TYPE_OWNER,
                ],
            ],
            'joins'      => [
                [
                    'table'      => 'users',
                    'alias'      => 'User',
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.id = GoalMember.user_id',
                    ]
                ]
            ],
            'fields'     => [
                'GoalMember.id',
                'GoalMember.goal_id',
                'User.id',
                'User.photo_file_name',
                'User.first_name',
                'User.last_name',
                'User.middle_name',
            ],
        ];

        $res = $this->find('first', $options);
        return $res ?? null;
    }

    function getLeaderUid($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
                'type'    => [
                    GoalMember::TYPE_OWNER,
                ],
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $res = $this->find('first', $options);
        if (Hash::get($res, 'GoalMember.user_id')) {
            return $res['GoalMember']['user_id'];
        }
        return null;
    }

    /**
     * ゴールメンバーの一覧を返す
     *
     * @param       $goal_id
     * @param array $params
     *                'limit' : find() の limit
     *                'page'  : find() の page
     *                'order' : find() の order
     *
     * @return array|null
     */
    function getGoalMemberByGoalId($goal_id, array $params = [])
    {
        $params = array_merge([
            'limit' => null,
            'page'  => 1,
            'order' => ['GoalMember.created' => 'ASC'],
        ], $params);
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'contain'    => ['User'],
            'limit'      => $params['limit'],
            'page'       => $params['page'],
            'order'      => $params['order'],

        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * @param      $goal_id
     * @param null $type
     *
     * @return array
     */
    function findActiveByGoalId($goalId, $type = null)
    {
        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'GoalMember.team_id' => $this->current_team_id,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
                'User.active_flg'    => true,
            ],
            'fields'     => [
                'GoalMember.user_id',
                'GoalMember.user_id'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.team_id = GoalMember.team_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id',
                    ],
                ],
            ],
        ];
        if ($type !== null) {
            $options['conditions']['type'] = $type;
        }
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * @deprecated
     */
    function getGoalMember($team_id, $user_id, $goal_id, $owner = true)
    {
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'user_id' => $user_id,
                'goal_id' => $goal_id,
                'type'    => self::TYPE_OWNER,
            ],
        ];
        if ($owner === false) {
            $options['conditions']['type'] = self::TYPE_COLLABORATOR;
        }
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * ゴールのリーダー情報を取得
     *
     * @param $goalIds
     *
     * @return array
     */
    function findLeaders($goalIds)
    {
        $options = [
            'fields'     => [
                'GoalMember.goal_id',
                'User.id',
                'User.photo_file_name',
                'User.first_name',
                'User.last_name',
                'User.middle_name',
            ],
            'conditions' => [
                'team_id' => $this->current_team_id,
                'goal_id' => $goalIds,
                'type'    => self::TYPE_OWNER,
            ],
            'joins'      => [
                [
                    'table'      => 'users',
                    'alias'      => 'User',
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.id = GoalMember.user_id',
                    ]
                ]
            ],
        ];
        $users = $this->find('all', $options);
        $ret = [];
        foreach ($users as $v) {
            $ret[] = array_merge(
                Hash::extract($v, 'User'),
                Hash::extract($v, 'GoalMember')
            );
        }
        return $ret;
    }

    /**
     * Get a unique record
     *
     * @param int $userId
     * @param int $goalId
     *
     * @return array
     */
    public function getUnique(int $userId, int $goalId): array
    {
        $options = [
            'conditions' => [
                'user_id' => $userId,
                'goal_id' => $goalId,
            ],
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            return [];
        }
        return $res;
    }

    function getOwnersStatus($goal_ids)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_ids,
                'type'    => self::TYPE_OWNER
            ],
            'fields'     => [
                'goal_id',
                'user_id',
                'approval_status',
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * カウント数を返す
     *
     * @param array $params
     *
     * @return int
     */
    public function getCount(array $params = [])
    {
        $params = array_merge(
            [
                'user_id' => null,
                'start'   => null,
                'end'     => null,
                'type'    => null,
            ], $params);

        $options = [
            'conditions' => [
                'GoalMember.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['GoalMember.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["GoalMember.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["GoalMember.created <="] = $params['end'];
        }
        if ($params['type'] !== null) {
            $options['conditions']["GoalMember.type"] = $params['type'];
        }

        return $this->find('count', $options);
    }

    function getForApproval($goalMemberId)
    {
        $currentTerm = $this->Goal->Team->Term->getTermData(Term::TYPE_CURRENT);
        $conditions = [
            'GoalMember.id'    => $goalMemberId,
            'Goal.end_date >=' => $currentTerm['start_date'],
            'Goal.end_date <=' => $currentTerm['end_date'],
        ];

        $options = [
            'fields'     => [
                'id',
                'user_id',
                'approval_status',
                'is_wish_approval',
                'is_target_evaluation',
                'role',
                'description',
                'type'
            ],
            'conditions' => $conditions,
            'contain'    => [
                'Goal'            => [
                    'fields'       => [
                        'Goal.id',
                        'Goal.name',
                        'Goal.photo_file_name',
                    ],
                    'GoalCategory' => [
                        'fields' => [
                            'GoalCategory.name',
                        ]
                    ],
                    'Leader'       => [
                        'fields'     => [
                            'Leader.id',
                            'Leader.user_id'
                        ],
                        'conditions' => ['Leader.type' => GoalMember::TYPE_OWNER],
                        'User'       => [
                            'fields' => $this->User->profileFields
                        ]
                    ],
                    'TopKeyResult' => [
                        'conditions' => [
                            'TopKeyResult.tkr_flg' => '1'
                        ],
                        'fields'     => [
                            'TopKeyResult.name',
                            'TopKeyResult.start_value',
                            'TopKeyResult.target_value',
                            'TopKeyResult.current_value',
                            'TopKeyResult.value_unit',
                            'TopKeyResult.description'
                        ]
                    ]
                ],
                'User'            => [
                    'fields' => $this->User->profileFields
                ],
                'ApprovalHistory' => [
                    'fields' => [
                        'ApprovalHistory.id',
                        'ApprovalHistory.goal_member_id',
                        'ApprovalHistory.user_id',
                        'ApprovalHistory.comment',
                        'ApprovalHistory.select_clear_status',
                        'ApprovalHistory.select_important_status',
                        'ApprovalHistory.action_status'
                    ],
                    'User'   => [
                        'fields' => $this->User->profileFields
                    ]
                ]
            ],
            'order'      => ['GoalMember.created DESC'],
        ];
        return $this->find('first', $options);
    }

    function getUserIdByGoalMemberId($goalMemberId)
    {
        if (!$goalMemberId) {
            return null;
        }

        $res = $this->findById($goalMemberId);
        if (!$res) {
            return null;
        }
        return $res['GoalMember']['user_id'];
    }

    /**
     * ゴールIDごとの件数取得
     *
     * @param $goalIds
     *
     * @return bool
     */
    public function countEachGoalId($goalIds)
    {
        $ret = $this->find('all', [
            'fields'     => ['goal_id', 'COUNT(goal_id) as cnt'],
            'conditions' => ['goal_id' => $goalIds],
            'group'      => ['goal_id'],
        ]);
        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultCountEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.GoalMember.goal_id', '{n}.0.cnt');
        return $ret + $defaultCountEachGoalId;
    }

    /**
     * ゴールごとにメンバーであるか判定
     *
     * @param $goalIds
     *
     * @return bool
     */
    public function isMemberCountEachGoalId($goalIds, $userId)
    {
        $ret = $this->find('all', [
            'fields'     => ['goal_id', 'count(goal_id) as exist'],
            'conditions' => [
                'goal_id' => $goalIds,
                'user_id' => $userId,
                'type'    => self::TYPE_COLLABORATOR
            ],
            'group'      => ['goal_id'],
        ]);
        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.GoalMember.goal_id', '{n}.0.exist');
        return $ret + $defaultEachGoalId;
    }

    /**
     * @param array $goalIds
     * @param int   $userId
     *
     * @return array
     */
    public function getCollaborationGoalIds(array $goalIds, int $userId): array
    {
        $options = [
            'fields'     => ['goal_id'],
            'conditions' => [
                'goal_id' => $goalIds,
                'user_id' => $userId,
                'type'    => self::TYPE_COLLABORATOR,
                'del_flg' => false
            ]
        ];
        $res = $this->find('all', $options);
        if (!$res) {
            return [];
        }

        return array_unique(Hash::extract($res, '{n}.GoalMember.goal_id'));
    }

    /**
     * ゴールメンバーが認定希望かどうか判定
     *
     * @param  $goalMemberId
     *
     * @return boolean
     */
    function isWishGoalApproval($goalMemberId)
    {
        if (!$goalMemberId) {
            return false;
        }

        $res = $this->findById($goalMemberId, ['is_wish_approval']);
        if (!$res) {
            return false;
        }

        return Hash::get($res, 'GoalMember.is_wish_approval');
    }

    /**
     * ゴールリーダーのIDを取得
     *
     * @param  $goalId
     *
     * @return $goalMemberId|null
     */
    function getGoalLeaderId($goalId)
    {
        if (!$goalId) {
            return null;
        }

        $res = $this->find('first', [
            'conditions' => [
                'goal_id' => $goalId,
                'type'    => self::TYPE_OWNER
            ],
            'fields'     => [
                'id'
            ]
        ]);
        if (!$res) {
            return null;
        }
        return Hash::get($res, 'GoalMember.id');
    }

    /**
     * ゴールリーダか判定
     *
     * @param  $goalId
     *
     * @return boolean
     */
    function isLeader($goalId, $userId)
    {
        if (empty($goalId) || empty($userId)) {
            return false;
        }

        $cnt = $this->find('count', [
            'conditions' => [
                'goal_id' => $goalId,
                'user_id' => $userId,
                'type'    => self::TYPE_OWNER
            ],
        ]);
        return $cnt > 0;
    }

    /**
     * コラボレーターか判定
     *
     * @param $goalId
     * @param $userId
     *
     * @return boolean
     */
    function isCollaborator(int $goalId, int $userId): bool
    {
        $res = $this->find('count', [
            'conditions' => [
                'goal_id' => $goalId,
                'user_id' => $userId,
                'type'    => self::TYPE_COLLABORATOR
            ],
        ]);

        return $res > 0;
    }

    /**
     * アクティブなゴールリーダーを取得.
     *
     * @param int $goalId
     *
     * @return array || null
     */
    function getActiveLeader(int $goalId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'GoalMember.type'    => $GoalMember::TYPE_OWNER,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
                'User.active_flg'    => true,
            ],
            'fields'     => [
                'GoalMember.id',
                'User.*',
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.team_id = GoalMember.team_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id',
                    ],
                ],
            ],
        ];

        $res = $GoalMember->find('first', $options);
        return $res ?? null;
    }

    /**
     * ゴールメンバーがアクティブかどうか判定
     *
     * @param int $goalMemberId
     *
     * @return bool
     */
    function isActiveGoalMember(int $goalMemberId, int $goalId): bool
    {
        if (!$goalMemberId) {
            return false;
        }

        $res = $this->find('first', [
            'conditions' => [
                'GoalMember.id'     => $goalMemberId,
                'GoalMember.type'   => self::TYPE_COLLABORATOR,
                'Goal.id'           => $goalId,
                'TeamMember.status' => TeamMember::USER_STATUS_ACTIVE,
                'User.active_flg'   => true
            ],
            'fields'     => [
                'GoalMember.id'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.team_id = GoalMember.team_id'
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id'
                    ],
                ],
            ]
        ]);

        return (boolean)$res;
    }

    /**
     * アクティブなコラボレーター一覧をリスト形式で返す
     *
     * @param int $goalId
     *
     * @return array
     */
    function getActiveCollaboratorList(int $goalId): array
    {
        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'GoalMember.type'    => self::TYPE_COLLABORATOR,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
                'User.active_flg'    => true
            ],
            'fields'     => [
                'GoalMember.id',
                'User.*'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.team_id = GoalMember.team_id'
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id'
                    ],
                ],
            ]
        ];

        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }

        $combined = Hash::combine($res, '{n}.GoalMember.id', '{n}.User.display_username');
        return $combined;
    }

    /**
     * GoalMemberIdよりGoalIdを取得する
     *
     * @param int $id
     *
     * @return int|null
     */
    function getGoalIdById(int $id)
    {
        $options = [
            'conditions' => [
                'id' => $id
            ],
            'fields'     => [
                'goal_id'
            ]
        ];
        $res = $this->find('first', $options);
        if (!$res) {
            return null;
        }

        $goalId = Hash::get($res, 'GoalMember.goal_id');
        return $goalId;
    }

    /**
     * 全ゴールメンバーのユーザーID一覧を取得
     *
     * @param int $goalId
     *
     * @return array
     */
    function findAllMemberUserIds(int $goalId): array
    {
        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
                'User.active_flg'    => true
            ],
            'fields'     => ['GoalMember.user_id'],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                        'TeamMember.team_id = GoalMember.team_id'
                    ],
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id'
                    ],
                ],
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }
}
