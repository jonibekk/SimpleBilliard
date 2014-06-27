<?php
App::uses('AppModel', 'Model');

/**
 * TeamMember Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property User        $CoachUser
 * @property Group       $Group
 * @property JobCategory $JobCategory
 */
class TeamMember extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'               => ['uuid' => ['rule' => ['uuid'],],],
        'team_id'               => ['uuid' => ['rule' => ['uuid'],],],
        'active_flg'            => ['boolean' => ['rule' => ['boolean'],],],
        'evaluation_enable_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'invitation_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'             => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'               => ['boolean' => ['rule' => ['boolean'],],],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'CoachUser' => ['className' => 'User', 'foreignKey' => 'coach_user_id',],
        'Group',
        'JobCategory',
    ];

    /**
     * 現在有効なチーム一覧を取得
     */
    function getActiveTeamList($uid)
    {
        $options = [
            'conditions' => [
                'TeamMember.user_id'    => $uid,
                'TeamMember.active_flg' => true
            ],
            'fields'     => ['team_id'],
            'contain'    => [
                'Team' => [
                    'fields' => ['name'],
                ]
            ]
        ];
        $res = $this->find('all', $options);
        /** @noinspection PhpDeprecationInspection */
        $res = array_filter(Set::combine($res, '{n}.Team.id', '{n}.Team.name'));
        return $res;
    }
}
