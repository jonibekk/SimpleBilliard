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

    function getMyGroupListNotExistsVision()
    {
        $group_list = $this->getMyGroupList();
        $group_ids = array_keys($group_list);
        $group_visions = $this->Group->GroupVision->getGroupVisionsByGroupIds($group_ids, true);
        $exists_group_ids = array_unique(Hash::extract($group_visions, '{n}.GroupVision.group_id'));
        foreach ($exists_group_ids as $gid) {
            unset($group_list[$gid]);
        }
        return $group_list;
    }

}
