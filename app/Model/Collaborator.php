<?php
App::uses('AppModel', 'Model');

/**
 * Collaborator Model
 *
 * @property Team $Team
 * @property Goal $Goal
 * @property User $User
 */
class Collaborator extends AppModel
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
    const APPROVAL_STATUS_WITHDRAW = 3;

    // TODO: 中身をセットする処理は未実装。表示文言が決まり次第実装する。
    static public $STATUS = [
        self::APPROVAL_STATUS_NEW => "",
        self::APPROVAL_STATUS_REAPPLICATION => "",
        self::APPROVAL_STATUS_DONE => "",
        self::APPROVAL_STATUS_WITHDRAW => ""
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
        self::$TYPE[self::TYPE_OWNER] = __("Owner");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'role'        => [
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notEmpty'  => ['rule' => 'notEmpty',],
        ],
        'description' => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
            'notEmpty'  => ['rule' => 'notEmpty',],
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
        $collaborator = [
            'team_id' => $this->current_team_id,
            'user_id' => $uid,
            'type'    => $type,
            'goal_id' => $goal_id,
        ];
        $res = $this->save($collaborator);
        return $res;
    }

    function edit($data, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!isset($data['Collaborator']) || empty($data['Collaborator'])) {
            return false;
        }
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $data['Collaborator']['user_id'] = $uid;
        $data['Collaborator']['team_id'] = $this->current_team_id;
        $data['Collaborator']['type'] = $type;

        $res = $this->save($data);
        $this->Goal->Follower->deleteFollower($data['Collaborator']['goal_id']);
        return $res;
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
                    Collaborator::TYPE_COLLABORATOR,
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
            Cache::write($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), $res, 'user_data');
        }
        return $res;
    }

    // for getting incomplete goal ids for collaborator right column
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
                        'Goal.id = Collaborator.goal_id',
                        'Goal.end_date >=' => $start_date,
                        'Goal.end_date <=' => $end_date,
                        'Goal.completed'   => null,
                    ]
                ]
            ],
            'conditions' => [
                'Collaborator.user_id' => $user_id,
                'Collaborator.team_id' => $this->current_team_id,
                'Collaborator.type'    => [
                    Collaborator::TYPE_COLLABORATOR,
                ],
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'order'      => [
                'Collaborator.priority DESC'
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
            Cache::write($this->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $user_id), $res, 'user_data');
        }
        return $res;
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
                        'Goal.id = Collaborator.goal_id',
                        'Goal.end_date >=' => $start_date,
                        'Goal.end_date <=' => $end_date,
                        'Goal.completed'   => null,
                    ]
                ]
            ],
            'conditions' => [
                'Collaborator.user_id' => $user_id,
                'Collaborator.team_id' => $this->current_team_id,
                'type'                 => [
                    Collaborator::TYPE_OWNER,
                ],
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'order'      => [
                'Collaborator.priority DESC'
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

    function isCollaborated($goal_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'Collaborator.goal_id' => $goal_id,
                'Collaborator.user_id' => $uid,
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
            'Collaborator.team_id' => $team_id,
            'Collaborator.user_id' => $goal_user_id,
        ];
        if (!empty($approvalStatus)) {
            $conditions['Collaborator.approval_status'] = $approvalStatus;
        }
        if ($term_type !== null) {
            $conditions['Goal.end_date >='] = $this->Goal->Team->EvaluateTerm->getTermData($term_type)['start_date'];
            $conditions['Goal.end_date <='] = $this->Goal->Team->EvaluateTerm->getTermData($term_type)['end_date'];
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
            'order'      => ['Collaborator.created DESC'],
        ];
        if (!$is_include_priority_0) {
            $options['conditions']['NOT'] = array('Collaborator.priority' => "0");
        }
        if (is_array($approvalStatus)) {
            unset($options['conditions']['Collaborator.approval_status']);
            foreach ($approvalStatus as $val) {
                $options['conditions']['OR'][]['Collaborator.approval_status'] = $val;
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
        $currentTerm = $this->Goal->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
        $conditions = [
            'Collaborator.team_id' => $this->current_team_id,
            'Collaborator.user_id' => $goalUserId,
            'Goal.end_date >='     => $currentTerm['start_date'],
            'Goal.end_date <='     => $currentTerm['end_date'],
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
            'order'      => ['Collaborator.approval_status ASC', 'Collaborator.created DESC'],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function changeApprovalStatus($id, $status)
    {
        $this->id = $id;
        $this->save(['approval_status' => $status]);
        $collabo = $this->findById($this->id);
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $collabo['Collaborator']['user_id']),
            'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true, $collabo['Collaborator']['user_id']),
            'user_data');
    }

    function countCollaboGoal($team_id, $user_id, $goal_user_id, $approval_flg)
    {
        $options = [
            'fields'     => ['id', 'type', 'approval_status', 'priority'],
            'conditions' => [
                'Collaborator.team_id'         => $team_id,
                'Collaborator.user_id'         => $goal_user_id,
                'Collaborator.approval_status' => $approval_flg,
            ],
            'contain'    => [
                'Goal' => [
                    'fields'       => ['id'],
                    'GoalCategory' => ['fields' => 'id'],
                ],
                'User' => [
                    'fields' => ['id'],
                ],
            ],
            'type'       => 'inner',
        ];

        $res = [];
        foreach ($this->find('all', $options) as $key => $val) {
            if ($this->Goal->isPresentTermGoal($val['Goal']['id']) === false) {
                continue;
            }
            // 自分のゴール + 修正待ち以外
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['approval_status'] !== '3') {
                continue;
            }
            // 自分のゴール + 修正待ち + コラボレーター
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['approval_status'] === '3'
                && $val['Collaborator']['type'] === '0'
            ) {
                continue;
            }
            //他人のゴール + 重要度0 = 対象外
            if ($val['User']['id'] !== (string)$user_id && $val['Collaborator']['priority'] === '0') {
                continue;
            }
            $res[] = $val;
        }
        return count($res);
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
        $currentTerm = $this->Team->EvaluateTerm->getCurrentTermData();

        $options = [
            'fields'     => ['Collaborator.id'],
            'joins'      => [
                [
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Goal.id = Collaborator.goal_id',
                        'Goal.end_date >=' => $currentTerm['start_date'],
                        'Goal.end_date <=' => $currentTerm['end_date'],
                        'Goal.completed'   => null,
                    ]
                ],
                [
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'type'       => 'INNER',
                    'conditions' => [
                        'TeamMember.user_id = Collaborator.user_id',
                        'TeamMember.coach_user_id' => $userId,
                    ]
                ]
            ],
            'conditions' => [
                'Collaborator.team_id'         => $this->current_team_id,
                'Collaborator.approval_status' => [
                    self::APPROVAL_STATUS_NEW,
                    self::APPROVAL_STATUS_REAPPLICATION,
                ],
            ],
        ];

        $count = $this->find('count', $options);
        return $count;
    }

    function getLeaderUid($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
                'type'    => [
                    Collaborator::TYPE_OWNER,
                ],
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['Collaborator']['user_id'])) {
            return $res['Collaborator']['user_id'];
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
    function getCollaboratorByGoalId($goal_id, array $params = [])
    {
        $params = array_merge([
            'limit' => null,
            'page'  => 1,
            'order' => ['Collaborator.created' => 'ASC'],
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
    function getCollaboratorListByGoalId($goal_id, $type = null)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'user_id',
                'user_id'
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
    function getCollaborator($team_id, $user_id, $goal_id, $owner = true)
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
     * ユニークのレコード取得
     * @param      $user_id
     * @param      $goal_id
     * @param bool $owner
     *
     * @return mixed
     */
    function getUnique($user_id, $goal_id, $owner = true)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
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
                'Collaborator.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['Collaborator.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["Collaborator.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Collaborator.created <="] = $params['end'];
        }
        if ($params['type'] !== null) {
            $options['conditions']["Collaborator.type"] = $params['type'];
        }

        return $this->find('count', $options);
    }

    function getCollaboratorForApproval($collaboratorId)
    {
        $currentTerm = $this->Goal->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
        $conditions = [
            'Collaborator.id' => $collaboratorId,
            'Goal.end_date >='     => $currentTerm['start_date'],
            'Goal.end_date <='     => $currentTerm['end_date'],
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
                'Goal' => [
                    'fields' => [
                        'Goal.id',
                        'Goal.name',
                        'Goal.photo_file_name',
                    ],
                    'GoalCategory' => [
                        'fields' => [
                            'GoalCategory.name',
                        ]
                    ],
                    'Leader'            => [
                        'fields'     => [
                            'Leader.id',
                            'Leader.user_id'
                        ],
                        'conditions' => ['Leader.type' => Collaborator::TYPE_OWNER],
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ],
                    'TopKeyResult' => [
                        'conditions' => [
                            'TopKeyResult.tkr_flg' => '1'
                        ],
                        'fields' => [
                            'TopKeyResult.name',
                            'TopKeyResult.start_value',
                            'TopKeyResult.target_value',
                            'TopKeyResult.value_unit',
                            'TopKeyResult.description'
                        ]
                    ]
                ],
                'User' => [
                    'fields' => $this->User->profileFields
                ],
                'ApprovalHistory' => [
                    'fields' => [
                        'ApprovalHistory.id',
                        'ApprovalHistory.collaborator_id',
                        'ApprovalHistory.user_id',
                        'ApprovalHistory.comment'
                    ],
                    'User' => [
                        'fields' => $this->User->profileFields
                    ]
                ]
            ],
            'order'      => ['Collaborator.created DESC'],
        ];
        return $this->find('first', $options);
    }

    function getUserIdByCollaboratorId($collaboratorId)
    {
        if(!$collaboratorId) {
            return null;
        }

        $res = $this->findById($collaboratorId);
        if(!$res) {
            return null;
        }
        return $res['Collaborator']['user_id'];
    }
}
