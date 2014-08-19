<?php
App::uses('AppModel', 'Model');

/**
 * NotifyFromUser Model
 *
 * @property Notification $Notification
 * @property User         $User
 * @property Team         $Team
 */
class NotifyFromUser extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Notification',
        'User',
        'Team',
    ];
}
