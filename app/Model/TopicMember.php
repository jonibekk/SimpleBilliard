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
     * @param int   $topicId
     * @param array $activeTeamMembersList
     *
     * @return int
     */
    function countMember(int $topicId, array $activeTeamMembersList): int
    {
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
     * @param int   $topicId
     * @param int   $messageId
     * @param array $activeTeamMembersList
     *
     * @return int
     */
    function countReadMember(int $topicId, int $messageId, array $activeTeamMembersList): int
    {
        $options = [
            'conditions' => [
                'topic_id'             => $topicId,
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
     * @param array $activeTeamMembersList
     * @param int   $limit if 0, unlimited
     *
     * @return array
     */
    function findMembers(int $topicId, array $activeTeamMembersList, int $limit = 0): array
    {
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
        if ($limit !== 0) {
            $options['limit'] = $limit;
        }
        $ret = $this->find('all', $options);
        return (array)$ret;
    }
}
