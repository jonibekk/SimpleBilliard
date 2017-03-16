<?php
App::uses('AppModel', 'Model');
App::uses('User', 'Model');

/**
 * Topic Model
 *
 * @property User        $CreatorUser
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
        'CreatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'creator_user_id',
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

    /**
     * Find latest update topics with latest message
     *
     * @param  int $userId
     * @param  int $offset
     * @param  int $limit
     *
     * @return array
     */
    function findLatest(int $userId, int $offset, int $limit): array
    {
        $options = [
            'conditions' => [
                'Topic.team_id' => $this->current_team_id,
                'TopicMember.user_id' => $userId,
            ],
            'fields'     => [
                'Topic.*',
                'LatestMessage.*'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'topic_members',
                    'alias'      => 'TopicMember',
                    'conditions' => [
                        'TopicMember.topic_id = Topic.id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'messages',
                    'alias'      => 'LatestMessage',
                    'conditions' => [
                        'LatestMessage.id = Topic.latest_message_id',
                    ],
                ]
            ],
            'contain'    => [
                'TopicMember' => [
                    'fields' => [
                        'id',
                        'user_id',
                        'topic_id'
                    ],
                    'User' => [
                        'fields' => $this->TopicMember->User->profileFields
                    ]
                ]
            ],
            'order'      => [
                'Topic.latest_message_datetime DESC'
            ],
            'offset'     => $offset,
            'limit'      => $limit
        ];


        $res = $this->find('all', $options);

        // attach user images
        foreach($res as $i => $topic) {
            foreach($topic['TopicMember'] as $j => $member) {
                $res[$i]['TopicMember'][$j]['User'] = $this->attachImgUrl($member['User'], 'User');
            }
        }

        return $res;
    }
}
