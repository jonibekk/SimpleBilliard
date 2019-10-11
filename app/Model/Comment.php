<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('TextExHelper', 'View/Helper');
App::uses('View', 'View');

App::uses('User', 'Model');
App::uses('Circle', 'Model');
App::import('Model', 'HavingMentionTrait');
App::import('Model/Entity', 'CommentEntity');
App::import('Lib/DataExtender', 'CommentExtender');

use Goalous\Enum\DataType\DataType as DataType;
/**
 * Comment Model
 *
 * @property Post         $Post
 * @property User         $User
 * @property Team         $Team
 * @property CommentLike  $CommentLike
 * @property CommentRead  $CommentRead
 * @property AttachedFile $AttachedFile
 * @property CommentFile  $CommentFile
 */
class Comment extends AppModel
{
    use HavingMentionTrait;

    const MAX_COMMENT_LIMIT = 3;

    public $bodyProperty = 'body';

    public $uses = [
        'AttachedFile'
    ];

    public $actsAs = [
        'Upload' => [
            'photo1'     => [
                'styles'  => [
                    'small' => '420l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo2'     => [
                'styles'  => [
                    'small' => '420l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo3'     => [
                'styles'  => [
                    'small' => '420l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo4'     => [
                'styles'  => [
                    'small' => '420l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo5'     => [
                'styles'  => [
                    'small' => '420l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'site_photo' => [
                'styles'      => [
                    'small' => '80w',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image-link.png',
            ],
        ],
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'comment_like_count' => ['numeric' => ['rule' => ['numeric']]],
        'comment_read_count' => ['numeric' => ['rule' => ['numeric']]],
        'del_flg'            => ['boolean' => ['rule' => ['boolean']]],
        'photo1'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo2'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo3'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo4'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo5'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'site_photo'         => [
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'body'               => [
            'maxLength' => ['rule' => ['maxLength', 5000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ],
        'site_info_url'      => [
            'isString' => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],

        ],
        'post_id'            => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
            'counterScope' => ['Comment.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentLike'   => [
            'dependent' => true,
        ],
        'CommentRead'   => [
            'dependent' => true,
        ],
        'MyCommentLike' => [
            'className' => 'CommentLike',
            'fields'    => ['id']
        ],
        'CommentFile',
    ];

    /**
     * Type conversion table for Comment model
     *
     * @var array
     */
    protected $modelConversionTable = [
        'post_id'            => DataType::INT,
        'user_id'            => DataType::INT,
        'team_id'            => DataType::INT,
        'comment_like_count' => DataType::INT,
        'comment_read_count' => DataType::INT,
        'site_info'          => DataType::JSON
    ];

    public function beforeValidate($options = [])
    {
        parent::beforeValidate($options);

        // OGP 画像が存在する場合、画像の形式をチェックして
        // 通常の画像形式でない場合はデフォルトの画像を表示するようにする
        // （validate の段階でチェックすると投稿エラーになってしまうため）
        if (isset($this->data['Comment']['site_photo']['type'])) {
            if (isset($this->validate['site_photo']['image_type']['rule'][1])) {
                $image_types = $this->validate['site_photo']['image_type']['rule'][1];
                if (!in_array($this->data['Comment']['site_photo']['type'], $image_types)) {
                    // 画像形式が許容されていない場合、画像が存在しないものとする
                    $this->data['Comment']['site_photo'] = null;
                }
            }
        }
        return true;
    }

    /**
     * コメント
     *
     * @param      $postData
     * @param null $uid
     * @param null $team_id
     *
     * @return bool|mixed
     */
    public function add($postData, $uid = null, $team_id = null)
    {
        $this->begin();

        // コメントデータ保存
        $this->setUidAndTeamId($uid, $team_id);
        $postData['Comment']['user_id'] = $this->uid;
        $postData['Comment']['team_id'] = $this->team_id;
        $res = $this->save($postData);
        if (empty($res)) {
            $this->rollback();
            return false;
        }

        $comment_id = $this->getLastInsertID();
        $results = [];
        // ファイルが添付されている場合
        if (isset($postData['file_id']) && is_array($postData['file_id'])) {
            $results[] = $this->CommentFile->AttachedFile->saveRelatedFiles($comment_id,
                AttachedFile::TYPE_MODEL_COMMENT,
                $postData['file_id']);
        }
        // 投稿データのmodifiedを更新
        $this->Post->id = $postData['Comment']['post_id'];
        $results[] = $this->Post->saveField('modified', REQUEST_TIMESTAMP);
        //post_share_users,post_share_circlesの更新
        $results[] = $this->Post->PostShareUser->updateAll(['PostShareUser.modified' => REQUEST_TIMESTAMP],
            ['PostShareUser.post_id' => $postData['Comment']['post_id']]);
        $results[] = $this->Post->PostShareCircle->updateAll(['PostShareCircle.modified' => REQUEST_TIMESTAMP],
            ['PostShareCircle.post_id' => $postData['Comment']['post_id']]);

        // どこかでエラーが発生した場合は rollback
        foreach ($results as $r) {
            if (!$r) {
                $this->rollback();
                $this->CommentFile->AttachedFile->deleteAllRelatedFiles($comment_id, AttachedFile::TYPE_MODEL_COMMENT);
                return false;
            }
        }
        $this->commit();

        // 添付ファイルが存在する場合は一時データを削除
        if (isset($postData['file_id']) && is_array($postData['file_id'])) {
            $Redis = ClassRegistry::init('GlRedis');
            foreach ($postData['file_id'] as $hash) {
                $Redis->delPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
            }
        }

        return $comment_id;
    }

    /**
     * Get the count of comments in a post
     *
     * @param $post_id
     *
     * @return int
     */
    public function getCommentCount(int $post_id): int
    {
        $options = [
            'conditions' => [
                'Comment.post_id' => $post_id
            ],
            'fields'     => [
                'Comment.id'
            ]
        ];

        return (int)$this->find('count', $options);
    }

    /**
     * コメント一覧データを返す
     *
     * @param       $post_id
     * @param null  $get_num
     * @param null  $page
     * @param null  $order_by
     * @param array $params
     *                start: 指定すると、この時間以降に投稿されたコメントのみを返す
     *
     * @return array|null
     */
    public function getPostsComment($post_id, $get_num = null, $page = null, $order_by = null, $params = [])
    {
        $params = array_merge(['start' => null], $params);

        $options = [
            'conditions' => [
                'Comment.post_id' => $post_id,
                'Comment.team_id' => $this->current_team_id,
            ],
            'order'      => [
                'Comment.created' => 'asc'
            ],
            'contain'    => [
                'User'          => [
                    'fields' => $this->User->profileFields
                ],
                'MyCommentLike' => [
                    'conditions' => [
                        'MyCommentLike.user_id' => $this->my_uid,
                        'MyCommentLike.team_id' => $this->current_team_id,
                    ]
                ],
                'CommentFile'   => [
                    'order'        => ['CommentFile.index_num asc'],
                    'AttachedFile' => [
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ]
                ]
            ],
            'limit'      => $get_num,
            'page'       => $page
        ];

        if (is_null($page) === false) {
            $options['page'] = $page;
        }

        if (is_null($order_by) === false) {
            $options['order']['Comment.created'] = $order_by;
        }

        if (is_null($params['start']) === false) {
            $options['conditions']['Comment.created >='] = $params['start'];
        }

        $res = $this->find('all', $options);

        // Add translation
        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');

        foreach ($res as $key => $value) {
            $res[$key]['Comment'] = $CommentExtender->extend($res[$key]['Comment'], $this->my_uid, $this->current_team_id, [CommentExtender::EXTEND_TRANSLATION_LANGUAGE]);
        }

        return $res;
    }

    public function getComment($comment_id)
    {
        $options = [
            'conditions' => [
                'Comment.id'      => $comment_id,
                'Comment.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'User'          => [
                    'fields' => $this->User->profileFields
                ],
                'MyCommentLike' => [
                    'conditions' => [
                        'MyCommentLike.user_id' => $this->my_uid,
                        'MyCommentLike.team_id' => $this->current_team_id,
                    ]
                ],
                'CommentFile'   => [
                    'order'        => ['CommentFile.index_num asc'],
                    'AttachedFile' => [
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ]
                ]
            ],
        ];
        $res = $this->find('first', $options);

        return $res;
    }

    public function getLatestPostsComment($post_id, $last_comment_id = 0)
    {
        //既読済みに
        $options = [
            'conditions' => [
                'Comment.post_id' => $post_id,
                'Comment.team_id' => $this->current_team_id,
                'Comment.id > '   => $last_comment_id
            ],
            'order'      => [
                'Comment.created' => 'desc'
            ],
            'contain'    => [
                'User'          => [
                    'fields' => $this->User->profileFields
                ],
                'MyCommentLike' => [
                    'conditions' => [
                        'MyCommentLike.user_id' => $this->my_uid,
                        'MyCommentLike.team_id' => $this->current_team_id,
                    ]
                ],
                'CommentFile'   => [
                    'order'        => ['CommentFile.index_num asc'],
                    'AttachedFile' => [
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ]
                ]
            ],
        ];
        //表示を昇順にする
        $res = array_reverse($this->find('all', $options));

        // Add translation
        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');

        foreach ($res as $key => $value) {
            $res[$key]['Comment'] = $CommentExtender->extend($res[$key]['Comment'], $this->my_uid, $this->current_team_id, [CommentExtender::EXTEND_TRANSLATION_LANGUAGE]);
        }

        // Add these comment to red list
        $commentIdList = Hash::extract($res, '{n}.Comment.id');
        $this->CommentRead->red($commentIdList);

        return $res;
    }

    function commentEdit($data)
    {
        if (isset($data['photo_delete']) && !empty($data['photo_delete'])) {
            foreach ($data['photo_delete'] as $index => $val) {
                if ($val) {
                    $data['Comment']['photo' . $index] = null;
                }
            }
        }
        $res = $this->save($data);
        return $res;
    }

    function getCountCommentUniqueUser($post_id, $without_user_id_list = [])
    {
        $options = [
            'conditions' => [
                'post_id' => $post_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'COUNT(DISTINCT user_id) as count',
            ]
        ];
        if (!empty($without_user_id_list)) {
            $options['conditions']['NOT']['user_id'] = $without_user_id_list;
        }
        $res = $this->find('count', $options);
        return $res;
    }

    function getCommentedUniqueUsersList($post_id, $without_me = true, $excludedUsers = [])
    {
        $options = [
            'conditions' => [
                'post_id' => $post_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'DISTINCT user_id',
            ]
        ];
        if ($without_me) {
            $options['conditions']['NOT']['user_id'] = $this->my_uid;
        }
        if (!empty($excludedUsers)) {
            $options['conditions']['NOT']['user_id'] = $excludedUsers;
        }
        $res = $this->find('all', $options);
        /** @noinspection PhpDeprecationInspection */
        $res = Hash::combine($res, '{n}.Comment.user_id', '{n}.Comment.user_id');
        return $res;
    }

    /**
     * 期間内のいいねの数の合計を取得
     *
     * @param int      $userId
     * @param int|null $startTimestamp
     * @param int|null $endTimestamp
     *
     * @return int
     */
    public function getLikeCountSumByUserId(int $userId, int $startTimestamp = null, int $endTimestamp = null)
    {
        $options = [
            'fields'     => [
                'SUM(Comment.comment_like_count) as sum_like',
            ],
            'conditions' => [
                'Comment.user_id' => $userId,
                'Comment.team_id' => $this->current_team_id,
                'Post.type'       => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            ],
            'contain'    => [
                'Post'
            ]
        ];
        //期間で絞り込む
        if ($startTimestamp) {
            $options['conditions']['Comment.created >'] = $startTimestamp;
        }
        if ($endTimestamp) {
            $options['conditions']['Comment.created <'] = $endTimestamp;
        }
        $res = $this->find('first', $options);
        return $res ? $res[0]['sum_like'] : 0;
    }

    /**
     * カウント数を返す
     *
     * @param array $params
     *
     * @return int
     */
    public function getCount(array $params = [])
    {
        $params = array_merge(
            [
                'post_id'   => null,
                'user_id'   => null,
                'start'     => null,
                'end'       => null,
                'post_type' => null,
            ], $params);

        $options = [
            'conditions' => [
                'Comment.team_id' => $this->current_team_id,
            ],
            'contain'    => [],
        ];
        if ($params['post_id'] !== null) {
            $options['conditions']['Comment.post_id'] = $params['post_id'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']['Comment.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["Comment.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Comment.created <="] = $params['end'];
        }
        if ($params['post_type'] !== null) {
            $options['conditions']["Post.type"] = $params['post_type'];
            $options['contain'][] = 'Post';
        }
        return (int)$this->find('count', $options);
    }

    /**
     * コメントしたユニークユーザー数を返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getUniqueUserCount($params = [])
    {
        $params = array_merge(
            [
                'start'     => null,
                'end'       => null,
                'user_id'   => null,
                'post_type' => null,
            ], $params);

        $options = [
            'fields'     => [
                'COUNT(DISTINCT Comment.user_id) as cnt',
            ],
            'conditions' => [
                'Comment.team_id' => $this->current_team_id,
            ],
            'contain'    => [],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["Comment.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Comment.created <="] = $params['end'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']["Comment.user_id"] = $params['user_id'];
        }
        if ($params['post_type'] !== null) {
            $options['conditions']["Post.type"] = $params['post_type'];
            $options['contain'][] = 'Post';
        }
        $row = $this->find('first', $options);

        $count = 0;
        if (isset($row[0]['cnt'])) {
            $count = $row[0]['cnt'];
        }
        return $count;
    }

    /**
     * 投稿別コメント数ランキングを返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getRanking($params = [])
    {
        $params = array_merge([
            'limit'           => null,
            'start'           => null,
            'end'             => null,
            'post_type'       => null,
            'post_user_id'    => null,
            'share_circle_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'Comment.post_id',
                'COUNT(DISTINCT Comment.id) as cnt',
            ],
            'conditions' => [
                'Comment.team_id' => $this->current_team_id,
            ],
            'group'      => ['Comment.post_id'],
            'order'      => ['cnt' => 'DESC'],
            'limit'      => $params['limit'],
            'contain'    => [],
        ];
        if ($params['post_type'] !== null) {
            $options['conditions']["Post.type"] = $params['post_type'];
            $options['contain'][] = 'Post';
        }
        if ($params['post_user_id'] !== null) {
            $options['conditions']["Post.user_id"] = $params['post_user_id'];
            $options['contain'][] = 'Post';
        }
        if ($params['start'] !== null) {
            $options['conditions']["Comment.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Comment.created <="] = $params['end'];
        }
        if ($params['share_circle_id'] !== null) {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'post_share_circles',
                'alias'      => 'PostShareCircle',
                'conditions' => [
                    'Comment.post_id = PostShareCircle.post_id',
                    'PostShareCircle.team_id'   => $this->current_team_id,
                    'PostShareCircle.circle_id' => $params['share_circle_id'],
                    'PostShareCircle.del_flg = 0',
                ],
            ];
        }
        $rows = $this->find('all', $options);
        $ranking = [];
        foreach ($rows as $v) {
            $ranking[$v['Comment']['post_id']] = $v[0]['cnt'];
        }
        return $ranking;
    }

    /**
     * Get the like count of a comment
     *
     * @param int $commentId
     *
     * @return int
     */
    public function getCommentLikeCount(int $commentId): int
    {
        $condition = [
            'conditions' => [
                'id' => $commentId
            ],
            'fields'     => [
                'Comment.comment_like_count'
            ]
        ];

        return $this->find('first', $condition)['Comment']['comment_like_count'];
    }


    /**
     * Update language of the comment
     *
     * @param int    $commentId
     * @param string $language
     *
     * @throws Exception
     */
    public function updateLanguage(int $commentId, string $language)
    {
        $this->id = $commentId;

        $newData = [
            'language' => $language
        ];

        $this->save($newData, false);
    }

    /**
     * Delete language of a comment
     *
     * @param int $commentId
     *
     * @throws Exception
     */
    public function clearLanguage(int $commentId)
    {
        $this->id = $commentId;

        $newData = [
            'language' => null
        ];

        $this->save($newData, false);
    }

    /**
     * Check whether the owner is owned by the user
     *
     * @param int $commentId
     * @param int $userId
     *
     * @return bool True if owned
     */
    public function isCommentOwned(int $commentId, int $userId): bool
    {
        $options = [
            'conditions' => [
                'id'      => $commentId,
                'user_id' => $userId,
            ]
        ];
        $res = $this->find('count', $options);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
