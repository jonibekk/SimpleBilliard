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
    const TYPE_NORMAL = 1;
    const TYPE_ADD_MEMBER = 2;
    const TYPE_LEAVE = 3;
    const TYPE_SET_TOPIC_NAME = 4;

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
            'fields'     => [
                'id',
                'body',
                'type',
                'target_user_ids',
                'created'
            ],
            'order'      => [
                'Message.id' => 'DESC'
            ],
            'contain'    => [
                'SenderUser'  => [
                    'fields' => $this->SenderUser->profileFields
                ],
                'MessageFile' => [
                    'fields'       => [],
                    'order'        => ['MessageFile.index_num asc'],
                    'AttachedFile' => [
                        'id',
                        'attached_file_name',
                        'file_type',
                        'file_ext'
                    ]
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
