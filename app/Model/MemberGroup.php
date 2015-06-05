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
                'team_id' => $team_id,
                'group_id' => $group_id
            ]
        ];
        return $this->find('list', $options);
    }
}
