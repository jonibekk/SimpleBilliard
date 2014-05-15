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
    public $validate = [
        'user_id' => ['uuid' => ['rule' => ['uuid'],],],
        'type'    => ['numeric' => ['rule' => ['numeric'],],],
        'uid'     => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

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
