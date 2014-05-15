<?php
App::uses('AppModel', 'Model');

/**
 * Invite Model
 *
 * @property User $FromUser
 * @property User $ToUser
 * @property Team $Team
 */
class Invite extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'from_user_id'   => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'        => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'email'          => array(
            'email' => array(
                'rule' => array('email'),
            ),
        ),
        'email_verified' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'del_flg'        => array(
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
        'FromUser' => array(
            'className'  => 'User',
            'foreignKey' => 'from_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'ToUser'   => array(
            'className'  => 'User',
            'foreignKey' => 'to_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team'     => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
