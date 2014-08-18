<?php
App::uses('AppModel', 'Model');

/**
 * SendMailToUser Model
 *
 * @property SendMail     $SendMail
 * @property User         $User
 * @property Team         $Team
 * @property Notification $Notification
 */
class SendMailToUser extends AppModel
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
        'SendMail',
        'User',
        'Team',
        'Notification',
    ];
}
