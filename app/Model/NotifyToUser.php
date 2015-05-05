<?php
App::uses('AppModel', 'Model');

/**
 * NotifyToUser Model
 *
 * @property Notification $Notification
 * @property User         $User
 * @property Team         $Team
 */
class NotifyToUser extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'unread_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'    => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Notification',
        'User',
        'Team',
    );

}
