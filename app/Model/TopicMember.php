<?php
App::uses('AppModel', 'Model');
App::uses('TeamMember', 'Model');

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
    function findMembers(int $topicId, int $limit = 0, array $excludeUids = []): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'topic_id' => $topicId,
                'user_id'  => $activeTeamMembersList,
            ],
            'fields'     => [
                'id',
                'last_read_message_id',
                'last_message_sent'
            ],
            'order'      => ['last_message_sent' => 'DESC'],
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
}
