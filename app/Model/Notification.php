<?php
App::uses('AppModel', 'Model');

/**
 * Notification Model
 *
 * @property User $User
 * @property Team $Team
 * @property User $FromUser
 */
class Notification extends AppModel
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
        'type'       => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'unread_flg' => array(
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
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
    ];
}
