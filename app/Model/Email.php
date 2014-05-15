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
    public $validate = [
        'user_id'        => ['uuid' => ['rule' => ['uuid']]],
        'email'          => ['email' => ['rule' => ['email']]],
        'email_verified' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];
}
