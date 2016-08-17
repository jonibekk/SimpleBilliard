<?php
App::uses('AppModel', 'Model');

/**
 * MessageRead Model
 *
 * @property Message $Message
 * @property User    $User
 */
class MessageRead extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Message',
        'User',
    ];
}
