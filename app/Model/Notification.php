<?php
App::uses('AppModel', 'Model');
App::uses('NotifySetting', 'Model');

/**
 * Notification Model
 *
 * @property User              $User
 * @property Team              $Team
 * @property User              $FromUser
 * @property NotifySetting     $NotifySetting
 * @property NotifyToUser      $NotifyToUser
 * @property NotifyFromUser    $NotifyFromUser
 */
class Notification extends AppModel
{
    public $uses = [
        'NotifySetting',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
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

    public $hasMany = [
        'NotifyToUser',
        'NotifyFromUser',
    ];

}
