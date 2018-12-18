<?php
App::uses('AppModel', 'Model');
App::uses('Message', 'Model');
App::uses('User', 'Model');
App::import('Model/Entity', 'UserEntity');
App::import('Service', 'ImageStorageService');

/**
 * Topic Model
 *
 * @property User        $CreatorUser
 * @property MessageFile $MessageFile
 * @property Message     $Message
 * @property TopicMember $TopicMember
 */

use Goalous\Enum as Enum;

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
        'title'   => [
            'maxLength' => ['rule' => ['maxLength', 128]],
            'notBlank'  => ['rule' => 'notBlank'],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'CreatorUser'   => [
            'className'  => 'User',
            'foreignKey' => 'creator_user_id',
        ],
        'LatestMessage' => [
            'className'  => 'Message',
            'foreignKey' => 'latest_message_id',
        ]
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
     * hasOne associations
     *
     * @var array
     */
    public $hasOne = [
        'TopicSearchKeyword'
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
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'Topic.team_id'       => $this->current_team_id,
                'TopicMember.user_id' => $userId,
            ],
            'fields'     => [
                'Topic.*'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'topic_members',
                    'alias'      => 'TopicMember',
                    'conditions' => [
                        'TopicMember.topic_id = Topic.id',
                        'TopicMember.del_flg' => false
                    ],
                ]
            ],
            'contain'    => [
                'LatestMessage' => [
                    'fields'     => [
                        'LatestMessage.id',
                        'LatestMessage.sender_user_id',
                        'LatestMessage.body',
                        'LatestMessage.type',
                        'LatestMessage.attached_file_count',
                        'LatestMessage.created',
                    ],
                    'SenderUser' => [
                        'fields' => $this->TopicMember->User->profileFields,
                    ]
                ],
                'TopicMember'   => [
                    'fields'     => [
                        'TopicMember.id',
                        'TopicMember.user_id',
                        'TopicMember.topic_id',
                        'TopicMember.last_read_message_id'
                    ],
                    'conditions' => [
                        'user_id' => $activeTeamMembersList
                    ],
                    'User'       => [
                        'fields' => $this->TopicMember->User->profileFields,
                        // 10: realistic upper limit for displaying title connecting user name.
                        'limit'  => 10
                    ],
                    'order'      => [
                        'TopicMember.last_message_sent DESC',
                        'TopicMember.id DESC',
                    ]
                ],
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
                'TopicSearchKeyword.keywords LIKE' => "%\n{$keyword}%"
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

    /**
     * get a topic with latest message
     *
     * @param  int $topicId [description]
     *
     * @return array
     */
    public function getWithLatestMesasge(int $topicId)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $activeTeamMembersList = $TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'Topic.id' => $topicId
            ],
            'fields'     => [
                'Topic.*',
                'LatestMessage.*'
            ],
            'joins'      => [
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
                    'fields'     => [
                        'TopicMember.id',
                        'TopicMember.user_id',
                        'TopicMember.topic_id',
                        'TopicMember.last_read_message_id'
                    ],
                    'conditions' => [
                        'user_id' => $activeTeamMembersList
                    ],
                    'User'       => [
                        'fields' => $this->TopicMember->User->profileFields,
                        // 10: realistic upper limit for displaying title connecting user name.
                        'limit'  => 10
                    ],
                    'order'      => [
                        'TopicMember.last_message_sent DESC',
                        'TopicMember.id DESC'
                    ]
                ]
            ]
        ];

        $res = $this->find('first', $options);

        // attach user images
        foreach ($res['TopicMember'] as $j => $member) {
            $res['TopicMember'][$j]['User'] = $this->attachImgUrl($res['TopicMember'][$j]['User'], 'User',
                ['medium_large']);
            // number of displaying user photo is less than 4.
            if ($j >= self::MAX_DISPLAYING_USER_PHOTO) {
                break;
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

    /**
     * create new topic
     *
     * @param  int $userId
     *
     * @return int|false
     */
    function add(int $userId)
    {
        $data = [
            'creator_user_id' => $userId,
            'team_id'         => $this->current_team_id
        ];

        if (!$this->save($data)) {
            return false;
        }

        $newTopicId = $this->getLastInsertID();
        return $newTopicId;
    }

    /**
     * fetch search topic keywords
     *
     * @param  int $topicId
     *
     * @return string
     */
    function fetchSearchKeywords(int $topicId): string
    {
        $options = [
            'conditions' => [
                'Topic.id' => $topicId
            ],
            'fields'     => [
                'User.id',
                'User.first_name',
                'User.last_name',
                'LocalName.first_name',
                'LocalName.last_name'
            ],
            'joins'      => [
                [
                    'type'       => 'inner',
                    'table'      => 'topic_members',
                    'alias'      => 'TopicMember',
                    'conditions' => [
                        'Topic.id = TopicMember.topic_id',
                        'TopicMember.del_flg' => false,
                    ],
                ],
                [
                    'type'       => 'inner',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'TopicMember.user_id = User.id',
                        'User.active_flg' => true,
                        'User.del_flg'    => false,
                    ],
                ],
                [
                    'type'       => 'inner',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TopicMember.team_id = TeamMember.team_id',
                        'TopicMember.user_id = TeamMember.user_id',
                        'TeamMember.status' => Enum\Model\TeamMember\Status::ACTIVE
                    ],
                ],
                [
                    'type'       => 'left',
                    'table'      => 'local_names',
                    'alias'      => 'LocalName',
                    'conditions' => [
                        'User.id = LocalName.user_id'
                    ],
                ],
            ]
        ];

        $res = $this->find('all', $options);
        $filtered = [];
        foreach ($res as &$user) {
            unset($user['User']['id']);
            $filtered[] = $user;
        }
        $res = AppUtil::flattenUnique($filtered);
        $keywords = "\n" . implode("\n", $res);
        return $keywords;
    }

    /**
     * get latest message id in topic
     *
     * @param int $topicId
     *
     * @return null|int
     */
    function getLatestMessageId(int $topicId)
    {
        $topic = $this->getById($topicId, ['latest_message_id']);
        if (empty($topic)) {
            return null;
        }
        return $topic['latest_message_id'];
    }

    /**
     * Get latest message senders in a topic
     *
     * @param int  $topicId
     * @param int  $count
     * @param bool $uniqueUserFlag
     *
     * @return UserEntity[]
     */
    public function getLatestSenders(
        int $topicId,
        int $count = Topic::MAX_DISPLAYING_USER_PHOTO,
        bool $uniqueUserFlag = false
    ): array {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $userFields = $User->profileFields;

        //If getting unique users, replace field id with DISTINCT id
        if ($uniqueUserFlag) {
            $userFields[0] = 'DISTINCT id';
        }

        $condition = [
            'conditions' => [
                'User.del_flg' => false
            ],
            'table'      => 'users',
            'alias'      => 'User',
            'limit'      => $count,
            'fields'     => $userFields,
            'joins'      => [
                [
                    'type'       => 'inner',
                    'table'      => 'messages',
                    'alias'      => 'Message',
                    'conditions' => [
                        'Message.sender_user_id = User.id',
                        'Message.topic_id' => $topicId,
                        'Message.del_flg'  => false,
                        'Message.type'     => Enum\Model\Message\MessageType::NORMAL
                    ]
                ]
            ],
            'order'      => [
                'Message.id' => 'DESC'
            ]
        ];

        $result = $User->useType()->useEntity()->find('all', $condition);

        // Set profile image url each data
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        /** @var UserEntity $v */
        foreach ($result as $v) {
            $v['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($v->toArray(true), 'User');
        }

        return $result;
    }

    /**
     * Get latest message senders' profile images in a topic
     *
     * @param int  $topicId
     * @param int  $count
     * @param bool $uniqueUserFlag
     *
     * @return array
     */
    public function getLatestSendersImage(
        int $topicId,
        int $count = Topic::MAX_DISPLAYING_USER_PHOTO,
        bool $uniqueUserFlag = false
    ): array {
        $users = $this->getLatestSenders($topicId, $count, $uniqueUserFlag);

        $result = [];

        foreach ($users as $user) {
            $result[] = $user['profile_img_url']['medium_large'] ?: "";
        }

        return $result;
    }
}
