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
    public $validate = [
        'from_user_id' => ['uuid' => ['rule' => ['uuid'],],],
        'to_user_id'   => ['uuid' => ['rule' => ['uuid'],],],
        'team_id'      => ['uuid' => ['rule' => ['uuid'],],],
        'type'         => ['numeric' => ['rule' => ['numeric'],],],
        'status'       => ['numeric' => ['rule' => ['numeric'],],],
        'name'         => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'del_flg'      => ['boolean' => ['rule' => ['boolean'],],],
    ];

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
