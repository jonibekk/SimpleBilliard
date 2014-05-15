<?php
App::uses('AppModel', 'Model');

/**
 * Thread Model
 *
 * @property User    $FromUser
 * @property User    $ToUser
 * @property Team    $Team
 * @property Message $Message
 */
class Thread extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'from_user_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'to_user_id'   => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'      => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'type'         => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'status'       => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'name'         => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'del_flg'      => array(
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
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
        'ToUser'   => ['className' => 'User', 'foreignKey' => 'to_user_id',],
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Message',
    ];

}
