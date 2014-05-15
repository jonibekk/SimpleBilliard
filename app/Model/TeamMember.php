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
    public $belongsTo = array(
        'User'        => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team'        => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'CoachUser'   => array(
            'className'  => 'User',
            'foreignKey' => 'coach_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Group'       => array(
            'className'  => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'JobCategory' => array(
            'className'  => 'JobCategory',
            'foreignKey' => 'job_category_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
