<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('TextExHelper', 'View/Helper');
App::uses('View', 'View');

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

    public function getCommentCount($post_id)
    {
        $options = [
            'conditions' => [
                'Comment.post_id' => $post_id,
                'Comment.team_id' => $this->current_team_id,
            ]
        ];
        return $this->find('count', $options);
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
        return $res;
    }

    function convertData($data)
    {
        $upload = new UploadHelper(new View());
        $text_ex = new TextExHelper(new View());

        //add photo_path
        if (isset($data['Comment']) === true) {
            $data['User']['photo_path'] = $upload->uploadUrl($data['User'], 'User.photo', ['style' => 'medium']);

        } else {
            foreach ($data as $key => $val) {
                $data[$key]['User']['photo_path'] = $upload->uploadUrl($val['User'], 'User.photo',
                    ['style' => 'medium']);
            }
        }

        //add url of red user modal
        if (isset($data['Comment']) === true) {
            $data['get_red_user_model_url'] = Router::url(
                [
                    'controller' => 'posts',
                    'action'     => 'ajax_get_message_red_users',
                    'comment_id' => $data['Comment']['id']
                ]
            );
        } else {
            foreach ($data as $key => $val) {
                $data[$key]['get_red_user_model_url'] = Router::url(
                    [
                        'controller' => 'posts',
                        'action'     => 'ajax_get_message_red_users',
                        'comment_id' => $val['Comment']['id']
                    ]
                );
            }
        }

        //auto link
        if (isset($data['Comment']) === true) {
            $data['Comment']['body'] = nl2br($text_ex->autoLink($data['Comment']['body']));
        } else {
            foreach ($data as $key => $val) {
                $data[$key]['Comment']['body'] = nl2br($text_ex->autoLink($data[$key]['Comment']['body']));
            }
        }
        return $data;
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

    function getCommentedUniqueUsersList($post_id, $without_me = true)
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
        $res = $this->find('all', $options);
        /** @noinspection PhpDeprecationInspection */
        $res = Hash::combine($res, '{n}.Comment.user_id', '{n}.Comment.user_id');
        return $res;
    }

    /**
     * 期間内のいいねの数の合計を取得
     *
     * @param      $user_id
     * @param null $start_date
     * @param null $end_date
     *
     * @return mixed
     */
    public function getLikeCountSumByUserId($user_id, $start_date = null, $end_date = null)
    {
        $options = [
            'fields'     => [
                'SUM(Comment.comment_like_count) as sum_like',
            ],
            'conditions' => [
                'Comment.user_id' => $user_id,
                'Comment.team_id' => $this->current_team_id,
                'Post.type'       => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            ],
            'contain'    => [
                'Post'
            ]
        ];
        //期間で絞り込む
        if ($start_date) {
            $options['conditions']['Comment.modified >'] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['Comment.modified <'] = $end_date;
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
        return $this->find('count', $options);
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
}
