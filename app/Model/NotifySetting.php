<?php
App::uses('AppModel', 'Model');

/**
 * NotifySetting Model
 *
 * @property User $User
 */
class NotifySetting extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'feed_app_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'feed_email_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'circle_app_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'circle_email_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'          => [
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
        'User'
    ];
}
