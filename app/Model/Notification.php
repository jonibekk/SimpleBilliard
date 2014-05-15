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
    public $validate = [
        'user_id'    => ['uuid' => ['rule' => ['uuid'],],],
        'team_id'    => ['uuid' => ['rule' => ['uuid'],],],
        'type'       => ['numeric' => ['rule' => ['numeric'],],],
        'unread_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'    => ['boolean' => ['rule' => ['boolean'],],],
    ];

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
