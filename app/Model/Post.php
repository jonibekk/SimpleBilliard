<?php
App::uses('AppModel', 'Model');

/**
 * Post Model
 *
 * @property User           $User
 * @property Team           $Team
 * @property CommentMention $CommentMention
 * @property Comment        $Comment
 * @property GivenBadge     $GivenBadge
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 */
class Post extends AppModel
{
    /**
     * 投稿タイプ
     */
    const TYPE_NORMAL = 1;
    const TYPE_ACTION = 2;
    const TYPE_BADGE = 3;

    public $actsAs = [
        'Upload' => [
            'photo1' => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo2' => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo3' => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo4' => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo5' => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'comment_count'   => ['numeric' => ['rule' => ['numeric'],],],
        'post_like_count' => ['numeric' => ['rule' => ['numeric'],],],
        'post_read_count' => ['numeric' => ['rule' => ['numeric'],],],
        'public_flg'      => ['boolean' => ['rule' => ['boolean'],],],
        'important_flg'   => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'         => ['boolean' => ['rule' => ['boolean'],],],
        'photo1' => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2' => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3' => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4' => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5' => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        //TODO ゴールのモデルを追加した後にコメントアウト解除
        //'Goal',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentMention',
        'Comment'  => [
            'dependent' => true,
        ],
        'GivenBadge',
        'PostLike' => [
            'dependent' => true,
        ],
        'PostMention',
        'PostRead' => [
            'dependent' => true,
        ],
        'MyPostLike' => [
            'className' => 'PostLike',
            'fields'    => ['id']
        ]
    ];

    /**
     * 投稿
     *
     * @param      $postData
     * @param int  $type
     * @param null $uid
     * @param null $team_id
     *
     * @return bool|mixed
     */
    public function add($postData, $type = self::TYPE_NORMAL, $uid = null, $team_id = null)
    {
        if (!isset($postData['Post']) || empty($postData['Post'])) {
            return false;
        }
        $this->setUidAndTeamId($uid, $team_id);
        $postData['Post']['user_id'] = $this->uid;
        $postData['Post']['team_id'] = $this->team_id;
        $postData['Post']['type'] = $type;
        $res = $this->save($postData);
        return $res;
    }

    public function get($page = 1, $limit = 20, $start = null, $end = null)
    {
        $one_month = 60 * 60 * 24 * 31;
        if (!$start) {
            $start = time() - $one_month;
        }
        elseif (is_string($start)) {
            $start = strtotime($start);
        }
        if (!$end) {
            $end = time();
        }
        elseif (is_string($end)) {
            $end = strtotime($end);
        }
        $post_options = [
            'conditions' => [
                'Post.team_id'                  => $this->current_team_id,
                'Post.modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'limit'      => $limit,
            'page'       => $page,
            'order'      => [
                'Post.modified' => 'desc'
            ],
        ];
        $post_list = $this->find('list', $post_options);
        //投稿を既読に
        $this->PostRead->red($post_list);
        //コメントを既読に
        $this->Comment->CommentRead->red($post_list);
        $options = [
            'conditions' => [
                'Post.team_id'                  => $this->current_team_id,
                'Post.modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'limit'      => $limit,
            'page'       => $page,
            'order'      => [
                'Post.modified' => 'desc'
            ],
            'contain'    => [
                'User'       => [
                    'fields' => $this->User->profileFields
                ],
                'MyPostLike' => [
                    'conditions' => [
                        'MyPostLike.user_id' => $this->me['id'],
                        'MyPostLike.team_id' => $this->current_team_id,
                    ],
                ],
                'Comment'    => [
                    'conditions'    => ['Comment.team_id' => $this->current_team_id],
                    'order'         => [
                        'Comment.created' => 'desc'
                    ],
                    'limit'         => 3,
                    'User'          => ['fields' => $this->User->profileFields],
                    'MyCommentLike' => [
                        'conditions' => [
                            'MyCommentLike.user_id' => $this->me['id'],
                            'MyCommentLike.team_id' => $this->current_team_id,
                        ]
                    ],
                ],
            ],
        ];
        $res = $this->find('all', $options);
        //コメントを逆順に
        foreach ($res as $key => $val) {
            if (!empty($val['Comment'])) {
                $res[$key]['Comment'] = array_reverse($res[$key]['Comment']);
            }
        }
        return $res;
    }
}
