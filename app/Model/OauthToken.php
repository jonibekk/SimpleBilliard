<?php
App::uses('AppModel', 'Model');

/**
 * OauthToken Model
 *
 * @property User $User
 */
class OauthToken extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'type'    => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'uid'     => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'del_flg' => array(
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
        'User' => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
