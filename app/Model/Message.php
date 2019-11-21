<?php
App::uses('AppModel', 'Model');

/**
 * Message Model
 *
 * @property Topic       $Topic
 * @property User        $SenderUser
 * @property MessageFile $MessageFile
 */

use Goalous\Enum\DataType\DataType as DataType;

use Goalous\Enum as Enum;

class Message extends AppModel
{
    /**
     * @deprecated
     */
    const TYPE_NORMAL = 1;
    /**
     * @deprecated
     */
    const TYPE_ADD_MEMBER = 2;
    /**
     * @deprecated
     */
    const TYPE_LEAVE = 3;
    /**
     * @deprecated
     */
    const TYPE_SET_TOPIC_NAME = 4;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'topic_id'       => [
            'numeric'       => ['rule' => ['numeric'],],
            'notBlank'      => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'isTopicMember' => ['rule' => ['customValidateIsTopicMember'],],
        ],
        'sender_user_id' => [
            'numeric'       => ['rule' => ['numeric'],],
            'notBlank'      => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'isTopicMember' => ['rule' => ['customValidateSenderIsBelongTheTopic']]
        ],
        'body'           => [
            'maxLength'                  => ['rule' => ['maxLength', 5000]],
            'isString'                   => ['rule' => 'isString', 'message' => 'Invalid Submission'],
            'bodyOrAttachedFileRequired' => ['rule' => ['customValidateBody']],
        ],
        'type'           => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'del_flg'        => [
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

    public $modelConversionTable = [
        'topic_id'            => DataType::INT,
        'sender_user_id'      => DataType::INT,
        'team_id'             => DataType::INT,
        'type'                => DataType::INT,
        'attached_file_count' => DataType::INT
    ];

    /**
     * Find messages
     *
     * @param int      $topicId
     * @param int|null $cursor
     * @param int      $limit
     * @param string   $direction "old" or "new"
     *
     * @return array
     */
    function findMessages(
        int $topicId,
        $cursor,
        int $limit,
        string $direction = Enum\Model\Message\MessageDirection::OLD
    ): array {
        $options = [
            'conditions' => [
                'Message.topic_id' => $topicId,
            ],
            'fields'     => [
                'id',
                'body',
                'type',
                'meta_data',
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
                        'file_size',
                        'file_type',
                        'file_ext'
                    ]
                ]
            ],
            'limit'      => $limit,
        ];

        if ($cursor) {
            if ($direction == Enum\Model\Message\MessageDirection::OLD) {
                $options['conditions']['Message.id <='] = $cursor;
            } elseif ($direction == Enum\Model\Message\MessageDirection::NEW) {
                $options['conditions']['Message.id >='] = $cursor;
                $options['order']['Message.id'] = 'ASC';
            }
        }

        $res = $this->find('all', $options);

        if ($direction === Enum\Model\Message\MessageDirection::NEW) {
            return array_reverse($res);
        }

        return $res;
    }

    /**
     * Return newer message.id in the topic.
     * If not existing, returning null.
     *
     * @param int $topicId
     * @param int $messageId
     *
     * @return int|null
     */
    function findNewerMessageId(int $topicId, int $messageId)
    {

        $options = [
            'conditions' => [
                'Message.topic_id' => $topicId,
                'Message.id >'     => $messageId,
            ],
            'fields'     => [
                'id',
            ],
            'order'      => [
                'Message.id' => 'ASC'
            ],
            'limit'      => 1,
        ];

        $res = $this->find('first', $options);
        if (empty($res)) {
            return null;
        }
        return Hash::get($res, 'Message.id');
    }

    /**
     * Find One message
     *
     * @param int $id
     *
     * @return array
     */
    function get(int $id): array
    {
        $options = [
            'conditions' => [
                'Message.id' => $id,
            ],
            'fields'     => [
                'id',
                'body',
                'type',
                'meta_data',
                'created'
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
                        'file_size',
                        'file_type',
                        'file_ext'
                    ]
                ]
            ],
        ];

        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * Add a new message.
     *
     * @param array $data
     * @param int   $userId
     *
     * @return mixed
     */
    function saveNormal(array $data, int $userId)
    {
        $data = am($data, [
            'type'           => Enum\Model\Message\MessageType::NORMAL,
            'sender_user_id' => $userId,
            'team_id'        => $this->current_team_id,
        ]);
        $ret = $this->save($data);
        return $ret;
    }

    /**
     * Saving leave member
     *
     * @param int $topicId
     * @param int $userId
     *
     * @return bool
     */
    function saveLeave(int $topicId, int $userId): bool
    {
        $data = [
            'topic_id'       => $topicId,
            'team_id'        => $this->current_team_id,
            'sender_user_id' => $userId,
            'type'           => Enum\Model\Message\MessageType::LEAVE,
            'meta_data'      => json_encode(['target_user_ids' => $userId])

        ];
        $ret = $this->save($data);
        return (bool)$ret;
    }

    /**
     * Saving set topic title
     *
     * @param int    $topicId
     * @param int    $userId
     * @param string $title
     *
     * @return bool
     */
    function saveSetTopicTitle(int $topicId, int $userId, string $title): bool
    {
        $data = [
            'topic_id'       => $topicId,
            'team_id'        => $this->current_team_id,
            'sender_user_id' => $userId,
            'type'           => Enum\Model\Message\MessageType::SET_TOPIC_NAME,
            'meta_data'      => json_encode(['updated_topic_title' => $title])
        ];
        $ret = $this->save($data);
        return (bool)$ret;
    }

    /**
     * Saving add member
     *
     * @param int   $topicId
     * @param int   $loginUserId
     * @param array $addUserIds
     *
     * @return bool
     */
    function saveAddMembers(int $topicId, int $loginUserId, array $addUserIds): bool
    {
        $data = [
            'topic_id'       => $topicId,
            'team_id'        => $this->current_team_id,
            'sender_user_id' => $loginUserId,
            'type'           => Enum\Model\Message\MessageType::ADD_MEMBER,
            'meta_data'      => json_encode(['target_user_ids' => $addUserIds])
        ];
        $ret = $this->save($data);
        return (bool)$ret;
    }

    /**
     * Is topic member?
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateIsTopicMember(array $val): bool
    {
        $topicId = array_shift($val);

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        if ($TopicMember->isMember($topicId, $this->my_uid)) {
            return true;
        }
        return false;
    }

    /**
     * sender user id belongs to the topic?
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateSenderIsBelongTheTopic(array $val): bool
    {
        $senderUid = array_shift($val);

        $topicId = Hash::get($this->data, 'Message.topic_id');

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        if ($TopicMember->isMember($topicId, $senderUid)) {
            return true;
        }
        return false;
    }

    /**
     * Is body valid?
     * - body on normal message is required when no files.
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateBody(array $val): bool
    {
        $body = array_shift($val);

        if (!empty($body)) {
            return true;
        }
        $type = Hash::get($this->data, 'Message.type');
        if ($type === null) {
            // default type
            $type = Enum\Model\Message\MessageType::NORMAL;
        }

        if ($type != Enum\Model\Message\MessageType::NORMAL) {
            return true;
        }

        $fileIds = Hash::get($this->data, 'Message.file_ids');
        if (!empty($fileIds)) {
            // remove empty values.
            $fileIds = array_filter($fileIds, "strlen");
        }

        if (!empty($fileIds)) {
            return true;
        }
        return false;
    }

    /**
     * メッセージ数（返信も含める）を返す
     *
     * @param array|null $userIds
     * @param int        $startTimestamp
     * @param int        $endTimestamp
     *
     * @return int
     */
    function getCount($userIds = null, $startTimestamp = null, $endTimestamp = null): int
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ]
        ];

        if ($startTimestamp !== null) {
            $options['conditions']["created >="] = $startTimestamp;
        }
        if ($endTimestamp !== null) {
            $options['conditions']["created <="] = $endTimestamp;
        }
        if ($userIds !== null) {
            $options['conditions']["sender_user_id"] = $userIds;
        }
        $count = $this->find('count', $options);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $count;
    }

    /**
     * 投稿したユニークユーザー数を返す
     *
     * @param null $userIds
     * @param null $startTimestamp
     * @param null $endTimestamp
     *
     * @return mixed
     */
    public function getUniqueUserCount($userIds = null, $startTimestamp = null, $endTimestamp = null)
    {
        $options = [
            'fields'     => [
                'COUNT(DISTINCT sender_user_id) as cnt',
            ],
            'conditions' => [
                'team_id' => $this->current_team_id,
            ],
        ];
        if ($startTimestamp !== null) {
            $options['conditions']["created >="] = $startTimestamp;
        }
        if ($endTimestamp !== null) {
            $options['conditions']["created <="] = $endTimestamp;
        }
        if ($userIds !== null) {
            $options['conditions']["sender_user_id"] = $userIds;
        }
        $row = $this->find('first', $options);

        $count = 0;
        if (isset($row[0]['cnt'])) {
            $count = $row[0]['cnt'];
        }
        return $count;
    }

    /**
     * Update language of the message
     *
     * @param int    $messageId
     * @param string $language
     *
     * @throws Exception
     */
    public function updateLanguage(int $messageId, string $language)
    {
        $this->id = $messageId;

        $newData = [
            'language' => $language
        ];

        $this->save($newData, false);
    }

}
