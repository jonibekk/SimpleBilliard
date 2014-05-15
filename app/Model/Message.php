<?php
App::uses('AppModel', 'Model');

/**
 * Message Model
 *
 * @property User   $FromUser
 * @property User   $ToUser
 * @property Thread $Thread
 */
class Message extends AppModel
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
        'thread_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
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
        'Thread'   => array(
            'className'  => 'Thread',
            'foreignKey' => 'thread_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
