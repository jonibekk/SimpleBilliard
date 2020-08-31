<?php
App::uses('AppModel', 'Model');

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

    function findMembers(int $groupId): array
    {
        $members = $this->MemberGroup->User->find("all", [
            "joins" => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.user_id = User.id',
                        'MemberGroup.group_id' => $groupId,
                    ],
                ],
            ],
            "order" => [
                "User.first_name ASC"
            ]
        ]);

        return $members;
    }

    function findGroupsWithMemberCount(array $scope): array
    {
        $options = [
            'joins' => [
                [
                    'table' => 'member_groups',
                    'type' => 'LEFT',
                    'conditions' => [
                        'member_groups.group_id = Group.id'
                    ],
                ],
            ],
            'fields' => [
                'Group.*',
                'COALESCE(COUNT(member_groups.id)) AS member_count'
            ],
            'group' => 'Group.id',
        ];

        $options = array_merge_recursive($options, $scope);

        $results =  $this->find('all', $options);

        return array_map(
            function ($row) {
                $row['Group']['member_count'] = $row['0']['member_count'];
                return $row;
            },
            $results
        );
    }
}
