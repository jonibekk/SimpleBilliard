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

    const STATUS_UNAPPROVED = 0;
    const STATUS_APPROVAL = 1;
    const STATUS_HOLD = 2;
    const STATUS_MODIFY = 3;

    static public $STATUS = [
        self::STATUS_UNAPPROVED => "",
        self::STATUS_APPROVAL   => "",
        self::STATUS_HOLD       => "",
        self::STATUS_MODIFY     => "",
    ];

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_COLLABORATOR] = __("Collaborator");
        self::$TYPE[self::TYPE_OWNER] = __("Owner");
    }

    /**
     * ステータス表示名をセット
     */
    private function _setStatusName()
    {
        self::$STATUS[self::STATUS_UNAPPROVED] = __("Waiting for approval");
        self::$STATUS[self::STATUS_APPROVAL] = __("In Evaluation");
        self::$STATUS[self::STATUS_HOLD] = __("Out of Evaluation");
        self::$STATUS[self::STATUS_MODIFY] = __("Waiting for modified");
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
        'GoalCategory',
        'User',
    ];

    public $hasMany = [
        'ApprovalHistory',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
        $this->_setStatusName();
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
            $options['conditions']['valued_flg'] = $approval_status;
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
            $options['conditions']['valued_flg'] = $approval_status;
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
        $approval_flg,
        $is_include_priority_0 = true,
        $term_type = null
    ) {
        $conditions = [
            'Collaborator.team_id'    => $team_id,
            'Collaborator.user_id'    => $goal_user_id,
            'Collaborator.valued_flg' => $approval_flg,
        ];
        if ($term_type !== null) {
            $conditions['Goal.end_date >='] = $this->Goal->Team->EvaluateTerm->getTermData($term_type)['start_date'];
            $conditions['Goal.end_date <='] = $this->Goal->Team->EvaluateTerm->getTermData($term_type)['end_date'];
        }

        $options = [
            'fields'     => ['id', 'type', 'role', 'priority', 'valued_flg'],
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
        if (is_array($approval_flg)) {
            unset($options['conditions']['Collaborator.valued_flg']);
            foreach ($approval_flg as $val) {
                $options['conditions']['OR'][]['Collaborator.valued_flg'] = $val;
            }
        }
        $res = $this->find('all', $options);
        return $res;
    }

    function changeApprovalStatus($id, $status)
    {
        $this->id = $id;
        $this->save(['valued_flg' => $status]);
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
            'fields'     => ['id', 'type', 'valued_flg', 'priority'],
            'conditions' => [
                'Collaborator.team_id'    => $team_id,
                'Collaborator.user_id'    => $goal_user_id,
                'Collaborator.valued_flg' => $approval_flg,
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
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['valued_flg'] !== '3') {
                continue;
            }
            // 自分のゴール + 修正待ち + コラボレーター
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['valued_flg'] === '3'
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
                'valued_flg',
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
}
