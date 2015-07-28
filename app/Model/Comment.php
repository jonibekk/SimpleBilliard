<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * Comment Model
 *
 * @property Post        $Post
 * @property User        $User
 * @property Team        $Team
 * @property CommentLike $CommentLike
 * @property CommentRead $CommentRead
 */
class Comment extends AppModel
{
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
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'site_photo'         => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'body'               => [
            'isString' => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]
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
        ]
    ];

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
        $this->setUidAndTeamId($uid, $team_id);
        $postData['Comment']['user_id'] = $this->uid;
        $postData['Comment']['team_id'] = $this->team_id;
        $res = $this->save($postData);
        //投稿データのmodifiedを更新
        $this->Post->id = $postData['Comment']['post_id'];
        $this->Post->saveField('modified', REQUEST_TIMESTAMP);

        return $res;
    }

    public function getPostsComment($post_id, $get_num = null)
    {
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
            ],
            'limit'      => $get_num
        ];
        $res = $this->find('all', $options);

        //既読済みに
        $comment_list = Hash::extract($res, '{n}.Comment.id');
        $this->CommentRead->red($comment_list);

        return $res;
    }

    function convertData($data)
    {
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        if (isset($data['Comment']) === true) {
            $data['User']['photo_path'] = $upload->uploadUrl($data['User'], 'User.photo', ['style' => 'original']);
            $data['Comment']['created'] = $time->elapsedTime(h($data['Comment']['created']));

        } else {
            foreach ($data as $key => $val) {
                $data[$key]['User']['photo_path'] = $upload->uploadUrl($val['User'], 'User.photo', ['style' => 'original']);
                $data[$key]['Comment']['created'] = $time->elapsedTime(h($val['Comment']['created']));
            }
        }

        return $data;
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

}
