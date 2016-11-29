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

    function getAllGroupList($team_id)
    {
        $options = [
            'fields'     => ['id', 'name'],
            'conditions' => [
                'team_id' => $team_id,
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * 全てのグループと所属ユーザのidを返す
     *
     * @return array|null
     */
    function getAllGroupWithMemberIds()
    {
        $activeUsers = $this->Team->TeamMember->getActiveTeamMembersList();
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

        return $res;
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
     * @return array|null
     */
    function getAllList($isActive = true)
    {
        $options = [
            'conditions' => [
                'active_flg' => $isActive,
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
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
                'Group.name LIKE' => $keyword . "%",
            ],
            'limit'      => $limit,
        ];
        $res = $this->find('all', $options);
        return $res;
    }
}
