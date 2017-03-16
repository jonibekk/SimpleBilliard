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
     * Find messages
     *
     * @param int      $topicId
     * @param int|null $cursor
     * @param int      $limit
     *
     * @return array
     */
    function findMessages(int $topicId, $cursor, int $limit): array
    {
        $options = [
            'conditions' => [
                'Message.topic_id' => $topicId,
            ],
            'order'      => [
                'Message.id' => 'DESC'
            ],
            'contain'    => [
                'SenderUser'  => [
                    'fields' => $this->SenderUser->profileFields
                ],
                'MessageFile' => [
                    'order'        => ['MessageFile.index_num asc'],
                    'AttachedFile' => []
                ]
            ],
            'limit'      => $limit,
        ];

        if ($cursor) {
            $options['conditions']['Message.id <'] = $cursor;
        }

        $res = $this->find('all', $options);
        return $res;
    }

}
