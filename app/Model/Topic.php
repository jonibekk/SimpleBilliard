<?php
App::uses('AppModel', 'Model');

/**
 * Topic Model
 *
 * @property User        $CreateUser
 * @property MessageFile $MessageFile
 * @property Message     $Message
 * @property TopicMember $TopicMember
 */
class Topic extends AppModel
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
        'CreateUser' => [
            'className'  => 'User',
            'foreignKey' => 'create_user_id',
        ],
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MessageFile',
        'Message',
        'TopicMember',
    ];

}
