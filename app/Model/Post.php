<?php
App::uses('AppModel', 'Model');

/**
 * Post Model
 *
 * @property User               $User
 * @property Team               $Team
 * @property CommentMention     $CommentMention
 * @property Comment            $Comment
 * @property GivenBadge         $GivenBadge
 * @property PostLike           $PostLike
 * @property PostMention        $PostMention
 * @property PostShareUser      $PostShareUser
 * @property PostShareCircle    $PostShareCircle
 * @property PostRead           $PostRead
 */
class Post extends AppModel
{
    /**
     * 投稿タイプ
     */
    const TYPE_NORMAL = 1;
    const TYPE_ACTION = 2;
    const TYPE_BADGE = 3;

    public $orgParams = [
        'circle_id' => null,
        'post_id'   => null,
    ];

    public $actsAs = [
        'Upload' => [
            'photo1'     => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo2'     => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo3'     => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo4'     => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo5'     => [
                'styles'  => [
                    'small' => '511l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'site_photo' => [
                'styles'  => [
                    'small' => '80w',
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
        'photo1'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'site_photo' => [
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
        'Comment'         => [
            'dependent' => true,
        ],
        'PostShareUser'   => [
            'dependent' => true,
        ],
        'PostShareCircle' => [
            'dependent' => true,
        ],
        'GivenBadge',
        'PostLike'        => [
            'dependent' => true,
        ],
        'PostMention',
        'PostRead'        => [
            'dependent' => true,
        ],
        'MyPostLike'      => [
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
        $share = null;
        if (isset($postData['Post']['share']) && !empty($postData['Post']['share'])) {
            $share = explode(",", $postData['Post']['share']);
            foreach ($share as $key => $val) {
                if (stristr($val, 'public')) {
                    $postData['Post']['public_flg'] = true;
                    unset($share[$key]);
                }
            }
        }
        $postData['Post']['user_id'] = $this->uid;
        $postData['Post']['team_id'] = $this->team_id;
        $postData['Post']['type'] = $type;
        $res = $this->save($postData);
        if (!empty($share)) {
            //ユーザとサークルに分割
            $users = [];
            $circles = [];
            foreach ($share as $val) {
                //ユーザの場合
                if (stristr($val, 'user_')) {
                    $users[] = str_replace('user_', '', $val);
                }
                //サークルの場合
                elseif (stristr($val, 'circle_')) {
                    $circles[] = str_replace('circle_', '', $val);
                }
            }
            //共有ユーザ保存
            $this->PostShareUser->add($this->getLastInsertID(), $users);
            //共有サークル保存
            $this->PostShareCircle->add($this->getLastInsertID(), $circles);
            //共有サークル指定されてた場合の未読件数更新
            $this->User->CircleMember->incrementUnreadCount($circles);
        }
        return $res;
    }

    public function getPublicList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $options = [
            'conditions' => [
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
                'public_flg'               => true,
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function isPublic($post_id)
    {
        $options = [
            'conditions' => [
                'id'         => $post_id,
                'team_id'    => $this->current_team_id,
                'public_flg' => true,
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function isMyPost($post_id)
    {
        $options = [
            'conditions' => [
                'id'      => $post_id,
                'team_id' => $this->current_team_id,
                'user_id' => $this->me['id'],
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function getMyPostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $options = [
            'conditions' => [
                'user_id'                  => $this->me['id'],
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function get($page = 1, $limit = 20, $start = null, $end = null, $params = null)
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
        if (isset($params['named']['page']) || !empty($params['named']['page'])) {
            $page = $params['named']['page'];
            unset($params['named']['page']);
        }

        $p_list = [];

        $org_param_exists = false;
        if ($params) {
            foreach ($this->orgParams as $key => $val) {
                if (array_key_exists($key, $params)) {
                    $org_param_exists = true;
                    $this->orgParams[$key] = $params[$key];
                }
                elseif (array_key_exists($key, $params['named'])) {
                    $org_param_exists = true;
                    $this->orgParams[$key] = $params['named'][$key];
                }
            }
        }

        //独自パラメータ指定なし
        if (!$org_param_exists) {
            //公開の投稿
            $p_list = array_merge($p_list, $this->getPublicList($start, $end));
            //自分の投稿
            $p_list = array_merge($p_list, $this->getMyPostList($start, $end));
            //自分が共有範囲指定された投稿
            $p_list = array_merge($p_list, $this->PostShareUser->getShareWithMeList($start, $end));
            //自分のサークルが共有範囲指定された投稿
            $p_list = array_merge($p_list, $this->PostShareCircle->getMyCirclePostList($start, $end));
        }
        //パラメータ指定あり
        else {
            //サークル指定
            if ($this->orgParams['circle_id']) {
                //サークル所属チェック
                if (empty($this->User->CircleMember->isBelong($this->orgParams['circle_id']))) {
                    throw new RuntimeException(__d('gl', "サークルが存在しないか、権限がありません。"));
                }
                $p_list = array_merge($p_list,
                                      $this->PostShareCircle->getMyCirclePostList($start, $end, 'modified', 'desc',
                                                                                  1000, $this->orgParams['circle_id']));
            }
            //単独投稿指定
            elseif ($this->orgParams['post_id']) {
                //アクセス可能かチェック
                //公開か？
                $p_list = $this->isPublic($this->orgParams['post_id']);
                //自分の投稿か？
                if (empty($p_list)) {
                    $p_list = $this->isMyPost($this->orgParams['post_id']);
                }
                //自分が共有範囲指定された投稿か？
                if (empty($p_list)) {
                    $p_list = $this->PostShareUser->isShareWithMe($this->orgParams['post_id']);
                }
                //自分のサークルが共有範囲指定された投稿か？
                if (empty($p_list)) {
                    $p_list = $this->PostShareCircle->isMyCirclePost($this->orgParams['post_id']);
                }
            }
        }

        if (!empty($this->orgParams['post_id'])) {
            //単独投稿指定の場合はそのまま
            $post_list = $p_list;
        }
        else {
            //単独投稿以外は再度、件数、オーダーの条件を入れ取得
            $post_options = [
                'conditions' => [
                    'Post.id' => $p_list,
                ],
                'limit'      => $limit,
                'page'       => $page,
                'order'      => [
                    'Post.modified' => 'desc'
                ],
            ];
            $post_list = $this->find('list', $post_options);
        }
        //投稿を既読に
        $this->PostRead->red($post_list);
        //コメントを既読に
        $this->Comment->CommentRead->red($post_list);
        $options = [
            'conditions' => [
                'Post.id' => $post_list,
            ],
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
        if (!empty($this->orgParams['post_id'])) {
            //単独の場合はコメントの件数上限外す
            unset($options['contain']['Comment']['limit']);
        }

        $res = $this->find('all', $options);
        //コメントを逆順に
        foreach ($res as $key => $val) {
            if (!empty($val['Comment'])) {
                $res[$key]['Comment'] = array_reverse($res[$key]['Comment']);
            }
        }
        return $res;
    }

    function postEdit($data)
    {
        if (isset($data['photo_delete']) && !empty($data['photo_delete'])) {
            foreach ($data['photo_delete'] as $index => $val) {
                if ($val) {
                    $data['Post']['photo' . $index] = null;
                }
            }
        }
        return $this->save($data);
    }
}
