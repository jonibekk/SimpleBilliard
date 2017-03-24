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

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-team.jpg',
                'quality'     => 100,
            ]
        ]
    ];

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

    /* number of displaying user photo in topic list page */
    const MAX_DISPLAYING_USER_PHOTO = 4;

    /**
     * Find latest topics with latest message
     * - if keyword set, search from topic_search_keywords
     *
     * @param  int    $userId
     * @param  int    $offset
     * @param  int    $limit
     * @param  string $keyword
     *
     * @return array
     */
    function findLatest(int $userId, int $offset, int $limit, string $keyword = ''): array
    {
        $options = [
            'conditions' => [
                'Topic.team_id'       => $this->current_team_id,
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
                ],
            ],
            'contain'    => [
                'TopicMember' => [
                    'fields' => [
                        'TopicMember.id',
                        'TopicMember.user_id',
                        'TopicMember.topic_id',
                        'TopicMember.last_read_message_id'
                    ],
                    'User'   => [
                        'fields' => $this->TopicMember->User->profileFields,
                        // 10: realistic upper limit for displaying title connecting user name.
                        'limit'  => 10
                    ],
                    'order'  => [
                        'TopicMember.last_message_sent DESC'
                    ]
                ]
            ],
            'order'      => [
                'Topic.latest_message_datetime DESC'
            ],
            'offset'     => $offset,
            'limit'      => $limit
        ];

        // search from topic_search_keywords and topic.title by keyword
        if ($keyword) {
            $options['conditions']['OR'] = [
                'Topic.title LIKE'                 => "%{$keyword}%",
                'TopicSearchKeyword.keywords LIKE' => "%\\n{$keyword}%"
            ];
            $options['joins'][] = [
                'type'       => 'LEFT',
                'table'      => 'topic_search_keywords',
                'alias'      => 'TopicSearchKeyword',
                'conditions' => [
                    'TopicSearchKeyword.topic_id = Topic.id',
                ],
            ];
        }
        $res = $this->find('all', $options);

        // attach user images
        foreach ($res as $i => $topic) {
            foreach ($topic['TopicMember'] as $j => $member) {
                $res[$i]['TopicMember'][$j]['User'] = $this->attachImgUrl($topic['TopicMember'][$j]['User'], 'User',
                    ['medium_large']);
                // number of displaying user photo is less than 4.
                if ($j >= self::MAX_DISPLAYING_USER_PHOTO) {
                    break;
                }
            }
        }

        return $res;
    }

    /*
     * get one topic.
     *
     * @param int $topicId
     *
     * @return array
     */
    public function get(int $topicId): array
    {
        $options = [
            'conditions' => ['id' => $topicId],
            'fields'     => [
                'id',
                'title',
                'latest_message_id',
            ],
        ];
        $ret = $this->find('first', $options);
        $ret = Hash::extract($ret, 'Topic');
        return (array)$ret;
    }

    /**
     * updating latest message
     *
     * @param int $topicId
     * @param int $messageId
     *
     * @return mixed
     */
    function updateLatestMessage(int $topicId, int $messageId)
    {
        $data = [
            'id'                      => $topicId,
            'latest_message_id'       => $messageId,
            'latest_message_datetime' => time()
        ];
        $ret = $this->save($data);
        return $ret;
    }

}
