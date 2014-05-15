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
    public $validate = array(
        'user_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'active_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'admin_flg'  => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'del_flg'    => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    );

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
}
