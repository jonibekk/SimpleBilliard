<?php
App::uses('AppModel', 'Model');

/**
 * MemberGroup Model
 *
 * @property Team  $Team
 * @property User  $User
 * @property Group $Group
 */
class MemberGroup extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index_num' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'   => [
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
        'User',
        'Group',
    ];

    /**
     * get all ids list
     *
     * @param $team_id
     * @param $user_id
     *
     * @return array|null
     */
    public function getAllGroupMemberIds($team_id, $user_id)
    {
        $options = [
            'fields'     => ['id'],
            'conditions' => [
                'team_id' => $team_id,
                'user_id' => $user_id
            ]
        ];
        return $this->find('list', $options);
    }

    /**
     * get member group id
     *
     * @param $team_id
     * @param $user_id
     * @param $group_id
     *
     * @return array|null
     */
    public function getGroupMemberId($team_id, $user_id, $group_id)
    {
        $options = [
            'fields'     => ['id'],
            'conditions' => [
                'team_id'  => $team_id,
                'user_id'  => $user_id,
                'group_id' => $group_id
            ]
        ];
        return $this->find('first', $options);
    }

    public function getGroupMemberUserId($team_id, $group_id)
    {
        $options = [
            'fields'     => ['user_id'],
            'conditions' => [
                'team_id'  => $team_id,
                'group_id' => $group_id
            ]
        ];
        return $this->find('list', $options);
    }

    function getMyGroupList()
    {
        $options = [
            'conditions' => [
                'MemberGroup.team_id' => $this->current_team_id,
                'MemberGroup.user_id' => $this->my_uid,
            ],
            'contain'    => [
                'Group' => [
                    'conditions' => ['Group.active_flg' => true,],
                    'fields'     => ['Group.id', 'Group.name'],
                ]
            ]
        ];
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.Group.id', '{n}.Group.name');
        return $res;
    }

    /**
     * まだグループビジョンが存在しないグループのリストを返す
     *
     * @param bool $isOnlyMyGroup
     *
     * @return array|null
     */
    function getGroupListNotExistsVision($isOnlyMyGroup = true)
    {
        if ($isOnlyMyGroup) {
            $group_list = $this->getMyGroupList();
        } else {
            $group_list = $this->Group->getAllList();
        }
        $group_ids = array_keys($group_list);
        $group_visions = $this->Group->GroupVision->getGroupVisionsByGroupIds($group_ids, true);
        $exists_group_ids = array_unique(Hash::extract($group_visions, '{n}.GroupVision.group_id'));
        foreach ($exists_group_ids as $gid) {
            unset($group_list[$gid]);
        }
        return $group_list;
    }

    /**
     * 指定グループに属しているメンバーデータを返す
     *
     * @param $group_id
     *
     * @return array|null
     */
    public function getGroupMember($group_id)
    {
        $active_user_ids = $this->Team->TeamMember->getActiveTeamMembersList();
        $options = [
            'conditions' => [
                'group_id' => $group_id,
                'user_id'  => $active_user_ids
            ],
            'contain'    => ['User']
        ];
        return $this->find('all', $options);
    }

}
