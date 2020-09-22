<?php
App::uses('AppModel', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

/**
 * TopicMember Model
 *
 * @property Topic $Topic
 * @property User  $User
 */
class TopicMember extends AppModel
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
        'Topic',
        'User',
    ];

    /**
     * User is topic member?
     *
     * @param int $topicId
     * @param int $userId
     *
     * @return bool
     */
    function isMember(int $topicId, int $userId): bool
    {
        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $userId,
                'del_flg'  => false
            ]
        ];
        $ret = $this->find('first', $options);
        return (bool)$ret;
    }

    /**
     * Count members
     *
     * @param int $topicId
     *
     * @return int
     */
    function countMember(int $topicId): int
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $activeTeamMembersList,
            ]
        ];
        $ret = $this->find('count', $options);
        return (int)$ret;
    }

    /**
     * Count read members
     *
     * @param int $messageId
     *
     * @return int
     * @internal param int $topicId
     */
    function countReadMember(int $messageId): int
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'last_read_message_id' => $messageId,
                'user_id'              => $activeTeamMembersList,
            ],
        ];
        $ret = $this->find('count', $options);
        return (int)$ret;
    }

    /**
     * Find members
     * - order by last_message_sent DESC
     *
     * @param int   $topicId
     * @param int   $limit if 0, unlimited
     * @param array $excludeUids
     *
     * @return array
     */
    function findUsersSortedBySentMessage(int $topicId, int $limit = 0, array $excludeUids = []): array
    {
        $options = [
            'conditions' => [
                'TopicMember.topic_id' => $topicId,
            ],
            'fields'     => [
                'TopicMember.id',
            ],
            'order'      => [
                'TopicMember.last_message_sent DESC',
                'TopicMember.id DESC',
            ],
            'contain'    => [
                'User' => $this->User->profileFields,
            ]
        ];
        if (!empty($excludeUids)) {
            $options['conditions']['NOT']['user_id'] = $excludeUids;
        }
        if ($limit !== 0) {
            $options['limit'] = $limit;
        }
        $ret = $this->find('all', $options);
        if (!$ret) {
            return $ret;
        }
        $ret = Hash::extract($ret, '{n}.User');
        return $ret;
    }

    /**
     * finding member id list
     *
     * @param int   $topicId
     * @param array $excludeUids
     *
     * @return array
     */
    function findMemberIdList(int $topicId, array $excludeUids = []): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $activeTeamMembersList,
            ],
            'fields'     => ['user_id'],
        ];
        if (!empty($excludeUids)) {
            $options['conditions']['NOT']['user_id'] = $excludeUids;
        }
        $ret = $this->find('list', $options);
        return $ret;
    }

    /**
     * add members to topic
     *
     * @param  int   $topicId
     * @param  array $users
     *
     * @return array
     */
    function add(int $topicId, array $users): bool
    {
        $saveData = [];
        foreach ($users as $userId) {
            $saveData[] = [
                'user_id'  => $userId,
                'topic_id' => $topicId,
                'team_id'  => $this->current_team_id,
            ];
        }
        return $this->bulkInsert($saveData);
    }

    /**
     * updating last message sent
     *
     * @param int $topicId
     * @param int $userId
     *
     * @return bool
     */
    function updateLastMessageSentDate(int $topicId, int $userId): bool
    {
        $fields = ['TopicMember.last_message_sent' => time()];
        $conditions = [
            'TopicMember.topic_id' => $topicId,
            'TopicMember.user_id'  => $userId,
        ];
        $ret = $this->updateAll($fields, $conditions);
        return (bool)$ret;
    }

    /**
     * updating last read message id
     *
     * @param int $topicId
     * @param int $messageId
     * @param int $userId
     *
     * @return bool
     */
    function updateLastReadMessageIdAndDate(int $topicId, int $messageId, int $userId)
    {
        $fields = [
            'TopicMember.last_read_message_id'       => $messageId,
            'TopicMember.last_read_message_datetime' => time(),
        ];
        $conditions = [
            'TopicMember.topic_id' => $topicId,
            'TopicMember.user_id'  => $userId,
        ];
        $ret = $this->updateAll($fields, $conditions);
        return (bool)$ret;
    }

    /**
     * leave from topic
     *
     * @param int $topicId
     * @param int $userId
     *
     * @return bool
     */
    function leave(int $topicId, int $userId): bool
    {
        $conditions = [
            'TopicMember.topic_id' => $topicId,
            'TopicMember.user_id'  => $userId,
        ];
        $ret = $this->softDeleteAll($conditions);
        return (bool)$ret;
    }

    /**
     * Add members to topic
     *
     * @param int   $topicId
     * @param array $userIds
     *
     * @return bool
     * @internal param int $userId
     */
    function bulkAdd(int $topicId, array $userIds): bool
    {
        $baseData = [
            'topic_id' => $topicId,
            'team_id'  => $this->current_team_id
        ];
        $insertData = [];
        foreach ($userIds as $userId) {
            $insertData[] = array_merge($baseData, [
                'user_id' => $userId,
            ]);
        }
        $ret = $this->bulkInsert($insertData);
        return (bool)$ret;
    }

    /**
     * find read members
     *
     * @param  int $latestMessageId
     *
     * @return array
     */
    function findReadMembers(int $latestMessageId): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'last_read_message_id' => $latestMessageId,
                'user_id'              => $activeTeamMembersList
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ]
            ],
            'order'      => [
                'TopicMember.last_read_message_datetime DESC'
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * find members
     *
     * @param  int $topicId
     *
     * @return array
     */
    function findMembers(int $topicId): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $activeTeamMembersList,
                'del_flg'  => 0
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * get last read message id
     *
     * @param  int $topicId
     * @param  int $userId
     *
     * @return null|int
     */
    function getLastReadMessageId(int $topicId, int $userId)
    {
        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $userId
            ],
            'fields'     => ['last_read_message_id']
        ];
        $res = $this->find('first', $options);
        if (!$res) {
            return $res;
        }
        $lastReadMessageId = Hash::get($res, 'TopicMember.last_read_message_id');
        return $lastReadMessageId;
    }

}
