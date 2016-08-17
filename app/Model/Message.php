<?php
App::uses('AppModel', 'Model');

/**
 * Message Model
 *
 * @property Topic       $Topic
 * @property User        $User
 * @property MessageFile $MessageFile
 * @property MessageRead $MessageRead
 */
class Message extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'               => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'message_read_count' => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'del_flg'            => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Topic',
        'User',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MessageFile',
        'MessageRead',
    ];

}
