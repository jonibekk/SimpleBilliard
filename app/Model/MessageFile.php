<?php
App::uses('AppModel', 'Model');

/**
 * MessageFile Model
 *
 * @property Topic        $Topic
 * @property Message      $Message
 * @property AttachedFile $AttachedFile
 */
class MessageFile extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index_num' => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'del_flg'   => [
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
        'Message',
        'AttachedFile',
    ];
}
