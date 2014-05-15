<?php
App::uses('AppModel', 'Model');

/**
 * Email Model
 *
 * @property User $User
 */
class Email extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id'        => array(
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
    public $belongsTo = [
        'User',
    ];
}
