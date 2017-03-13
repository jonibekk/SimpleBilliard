<?php
App::uses('AppModel', 'Model');

/**
 * Message Model
 *
 * @property Topic       $Topic
 * @property User        $SenderUser
 * @property MessageFile $MessageFile
 */
class Message extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => [
            'numeric' => ['rule' => ['numeric'],],
        ],
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
        'Topic',
        'SenderUser' => [
            'className'  => 'User',
            'foreignKey' => 'sender_user_id',
        ],
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MessageFile',
    ];

    /**
     * get latest message id
     *
     * @param int $topicId
     *
     * @return int
     */
    public function getLatestMessageId(int $topicId): int
    {
        $options = [
            'conditions' => [
                'topic_id' => $topicId,
            ],
            'fields'     => ['id'],
            'limit'      => 1,
            'order'      => ['id' => 'DESC']
        ];
        $ret = $this->find('first', $options);
        return (int)Hash::get($ret, 'Message.id');
    }

}
