<?php
App::uses('AppModel', 'Model');
App::uses('TeamMember', 'Model');
App::uses('AppModel', 'GoalGroup');
App::uses('Term', 'Model');

/**
 * Group Model
 *
 * @property Team        $Team
 * @property MemberGroup $MemberGroup
 * @property GroupVision $GroupVision
 */
class Group extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'       => [
            'isString' => [
                'rule' => ['isString',],
            ],
            'notBlank' => ['rule' => ['notBlank']]
        ],
        'active_flg' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MemberGroup',
        'GroupVision',
        'GoalGroup'
    ];

    function findIdsHavingMembers($teamId)
    {
        return $this->MemberGroup->find('list', array(
            'fields' => array('group_id'),
            'conditions' => [
                'team_id' => $teamId,
            ],
            'group' => 'group_id having COUNT(group_id) > 0'
        ));
    }

    function findAllList($team_id, bool $requiresUser = false)
    {
        $options = [
            'fields'     => ['id', 'name'],
            'conditions' => [
                'team_id' => $team_id,
            ],
        ];
        if ($requiresUser) {
            $options['conditions']['id'] = $this->findIdsHavingMembers($team_id);
        }
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * 全てのグループと所属ユーザのidを返す
     *
     * @return array
     */
    function findAllGroupWithMemberIds(): array
    {
        $activeUsers = $this->Team->TeamMember->getActiveTeamMembersList();
        if (empty($activeUsers)) {
            return [];
        }
        $options = [
            'fields'  => ['Group.id', 'Group.name'],
            'contain' => [
                'MemberGroup' => [
                    'conditions' => ['MemberGroup.user_id' => $activeUsers],
                    'fields'     => ['MemberGroup.user_id']
                ]
            ]
        ];
        $res = $this->find('all', $options);

        return (array) $res;
    }

    function getByName($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'name'    => $name
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function saveNewGroup($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [
            'name'    => $name,
            'team_id' => $team_id
        ];
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    function getByNameIfNotExistsSave($name, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        if (!empty($group = $this->getByName($name, $team_id))) {
            return $group;
        }
        $group = $this->saveNewGroup($name);
        return $group;
    }

    /**
     * 現在のチームのグループを全て返す
     *
     * @return array|null
     */
    function getAll()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * 全てのグループのリストを返す
     *
     * @param bool $isActive
     *
     * @return array
     */
    function getAllList(bool $isActive = true, bool $requiresUser = false): array
    {
        $options = [
            'conditions' => [
                'active_flg' => $isActive,
            ]
        ];
        if ($requiresUser) {
            $options['conditions']['id'] = $this->findIdsHavingMembers($this->current_team_id);
        }
        $res = $this->find('list', $options);
        return (array) $res;
    }

    /**
     * グループ名がキーワードにマッチするグループを返す
     *
     * @param $keyword
     * @param $limit
     *
     * @return array|null
     */
    function getGroupsByKeyword($keyword, $limit = 10)
    {
        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $options = [
            'conditions' => [
                'Group.name LIKE' => '%' . $keyword . '%',
            ],
            'limit'      => $limit,
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function findForUser(int $userId)
    {
        return $this->find("all", [
            "joins" => [
                [
                    "alias" => "MemberGroup",
                    "table" => "member_groups",
                    "conditions" => [
                        "MemberGroup.group_id = Group.id"
                    ]
                ],
                [
                    "alias" => "User",
                    "table" => "users",
                    "conditions" => [
                        "MemberGroup.user_id = User.id",
                        "User.id" => $userId
                    ]
                ]
            ],
            "order" => "Group.name"
        ]);
    }

    function findMembers(int $groupId): array
    {
        /** @var TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $options = [
            "joins" => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.user_id = User.id',
                        'MemberGroup.group_id' => $groupId,
                    ],
                ],
                [
                    'table' => 'team_members',
                    'conditions' => [
                        'MemberGroup.user_id = team_members.user_id', 
                        'MemberGroup.team_id = team_members.team_id',
                        'team_members.status' => $TeamMember::USER_STATUS_ACTIVE
                    ],
                ],
            ],
            "order" => [
                "User.first_name ASC"
            ]    
        ];

        return $this->MemberGroup->User->find("all", $options);
    }

    function findGroupsWithMemberCount(array $scope): array
    {
        /** @var TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $options = [
            'joins' => [
                [
                    'table' => 'member_groups',
                    'type' => 'LEFT',
                    'conditions' => [
                        'member_groups.group_id = Group.id'
                    ],
                ],
                [
                    'table' => 'team_members',
                    'type' => 'LEFT',
                    'conditions' => [
                        'member_groups.user_id = team_members.user_id',
                        'member_groups.team_id = team_members.team_id',
                    ],
                ],
                [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => [
                        'users.id = member_groups.user_id'
                    ]
                ]
            ],
            'fields' => [
                'Group.*',
                'COALESCE(COUNT(member_groups.user_id)) AS member_count'
            ],
            'group' => [
                'Group.id', 
                'team_members.status',
                'users.del_flg HAVING users.del_flg != 1 AND (team_members.status = 1) OR (team_members.status IS NULL AND member_count = 0)',
            ],
            "order" => [
                "Group.name ASC"
            ] 
        ];

        $options = array_merge_recursive($options, $scope);

        $results =  $this->find('all', $options);

        return array_map(
            function ($row) {
                $row['Group']['member_count'] = (int) $row['0']['member_count'];
                return $row;
            },
            $results
        );
    }

    function groupByUserIdSubQuery($userId)
    {
        $db = $this->getDataSource();
        return $db->buildStatement([
            "fields" => ['Group.id'],
            "table" => $db->fullTableName($this),
            "alias" => "Group",
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.group_id = Group.id',
                        'MemberGroup.user_id' => $userId
                    ]
                ]
            ]
        ], $this);
    }

    function groupForCoacheesSubQuery($userId)
    {
        $db = $this->getDataSource();
        return $db->buildStatement([
            "fields" => ['Group.id'],
            "table" => $db->fullTableName($this),
            "alias" => "Group",
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.group_id = Group.id'
                    ]
                ],
                [
                    'alias' => 'TeamMember',
                    'table' => 'team_members',
                    'conditions' => [
                        'TeamMember.user_id = MemberGroup.user_id',
                        'TeamMember.coach_user_id' => $userId,
                    ]
                ]
            ]
        ], $this);
    }

    function groupForEvaluateesSubQuery($userId)
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        $db = $this->getDataSource();
        return $db->buildStatement([
            "fields" => ['Group.id'],
            "table" => $db->fullTableName($this),
            "alias" => "Group",
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.group_id = Group.id'
                    ]
                ],
                [
                    'alias' => 'Evaluation',
                    'table' => 'evaluations',
                    'conditions' => [
                        'Evaluation.evaluatee_user_id = MemberGroup.user_id',
                        'Evaluation.evaluator_user_id' => $userId
                    ]
                ],
                [
                    'alias' => 'GoalGroup',
                    'table' => 'goal_groups',
                    'conditions' => [
                        'GoalGroup.group_id = Group.id'
                    ]
                ],
                [
                    'alias' => 'Goal',
                    'table' => 'goals',
                    'conditions' => [
                        'Goal.id = GoalGroup.goal_id'
                    ]
                ],
                [
                    'alias' => 'Term',
                    'table' => 'terms',
                    'conditions' => [
                        'Term.id = Evaluation.term_id',
                        'Term.evaluate_status' => $Term::STATUS_EVAL_IN_PROGRESS,
                        'Goal.start_date >= Term.start_date',
                        'Goal.end_date <= Term.end_date',
                    ]
                ]
            ]
        ], $this);
    }
}
