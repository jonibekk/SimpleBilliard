<?php
App::uses('AppModel', 'Model');

/**
 * Post Model
 *
 * @property User            $User
 * @property Team            $Team
 * @property CommentMention  $CommentMention
 * @property Comment         $Comment
 * @property Goal            $Goal
 * @property GivenBadge      $GivenBadge
 * @property PostLike        $PostLike
 * @property PostMention     $PostMention
 * @property PostShareUser   $PostShareUser
 * @property PostShareCircle $PostShareCircle
 * @property PostRead        $PostRead
 * @property ActionResult    $ActionResult
 * @property KeyResult       $KeyResult
 * @property Circle          $Circle
 */
class Post extends AppModel
{
    /**
     * post type
     */
    const TYPE_NORMAL = 1;
    const TYPE_CREATE_GOAL = 2;
    const TYPE_ACTION = 3;
    const TYPE_BADGE = 4;
    const TYPE_KR_COMPLETE = 5;
    const TYPE_GOAL_COMPLETE = 6;
    const TYPE_CREATE_CIRCLE = 7;
    const TYPE_MESSAGE = 8;

    static public $TYPE_MESSAGE = [
        self::TYPE_NORMAL        => null,
        self::TYPE_CREATE_GOAL   => null,
        self::TYPE_ACTION        => null,
        self::TYPE_BADGE         => null,
        self::TYPE_KR_COMPLETE   => null,
        self::TYPE_GOAL_COMPLETE => null,
        self::TYPE_CREATE_CIRCLE => null,
        self::TYPE_MESSAGE       => null,
    ];

    function _setTypeMessage()
    {
        self::$TYPE_MESSAGE[self::TYPE_CREATE_GOAL] = __d('gl', "あたらしいゴールをつくりました。");
        self::$TYPE_MESSAGE[self::TYPE_CREATE_CIRCLE] = __d('gl', "あたらしいサークルをつくりました。");
    }

    const SHARE_PEOPLE = 2;
    const SHARE_ONLY_ME = 3;
    const SHARE_CIRCLE = 4;

    public $orgParams = [
        'author_id'   => null,
        'circle_id'   => null,
        'user_id'     => null,
        'post_id'     => null,
        'goal_id'     => null,
        'filter_goal' => null,
        'type'        => null,
    ];

    public $actsAs = [
        'Upload' => [
            'photo1'     => [
                'styles'  => [
                    'small' => '460l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo2'     => [
                'styles'  => [
                    'small' => '460l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo3'     => [
                'styles'  => [
                    'small' => '460l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo4'     => [
                'styles'  => [
                    'small' => '460l',
                    'large' => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo5'     => [
                'styles'  => [
                    'small' => '460l',
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
        'comment_count'   => ['numeric' => ['rule' => ['numeric'],],],
        'post_like_count' => ['numeric' => ['rule' => ['numeric'],],],
        'post_read_count' => ['numeric' => ['rule' => ['numeric'],],],
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
        'site_photo'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'body'            => [
            'isString' => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]

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
        'Goal',
        'Circle',
        'ActionResult',
        'KeyResult',
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
        'CommentId'       => [
            'className' => 'Comment',
            'fields'    => ['CommentId.id', 'CommentId.user_id'],
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

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        $this->_setTypeMessage();
    }

    /**
     * 投稿
     *
     * @param      $postData
     * @param null $uid
     * @param null $team_id
     *
     * @return bool|mixed
     */
    public function addNormal($postData, $uid = null, $team_id = null)
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
                    $teamAllCircle = $this->Circle->getTeamAllCircle();
                    $share[$key] = 'circle_' . $teamAllCircle['Circle']['id'];
                }
            }
        }
        $postData['Post']['user_id'] = $this->uid;
        $postData['Post']['team_id'] = $this->team_id;
        if (!isset($postData['Post']['type'])) {
            $postData['Post']['type'] = Post::TYPE_NORMAL;
        }

        $res = $this->save($postData);

        if (empty($res)) {
            return false;
        }
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
            //共有サークル指定されてた場合、更新日時更新
            $this->User->CircleMember->updateModified($circles);
            $this->PostShareCircle->Circle->updateModified($circles);

        }
        return $res;
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $order
     * @param string $order_direction
     * @param int    $limit
     *
     * @return array|null|void
     */
    public function getRelatedPostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $g_list = [];
        $g_list = array_merge($g_list, $this->Goal->Follower->getFollowList($this->my_uid));
        $g_list = array_merge($g_list, $this->Goal->Collaborator->getCollaboGoalList($this->my_uid, true));
        $g_list = array_merge($g_list, $this->Goal->User->TeamMember->getCoachingGoalList($this->my_uid));

        if (empty($g_list)) {
            return [];
        }
        $options = [
            'conditions' => [
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
                'goal_id'                  => $g_list,
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['id'],
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
                'user_id' => $this->my_uid,
            ]
        ];
        $res = $this->find('list', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    public function getMyPostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $options = [
            'conditions' => [
                'user_id'                  => $this->my_uid,
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

    public function getPostById($post_id)
    {
        $options = [
            'conditions' => [
                'Post.id' => $post_id,
                'team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'User'          => [],
                'PostShareUser' => [],
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    public function getPhotoPath($user_arr)
    {
        $upload = new UploadHelper(new View());
        return $upload->uploadUrl($user_arr, 'User.photo', ['style' => 'small']);
    }

    public function get($page = 1, $limit = 20, $start = null, $end = null, $params = null)
    {
        $one_month = 60 * 60 * 24 * 31;
        if (!$start) {
            $start = REQUEST_TIMESTAMP - $one_month;
        }
        elseif (is_string($start)) {
            $start = strtotime($start);
        }
        if (!$end) {
            $end = REQUEST_TIMESTAMP;
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
                elseif (isset($params['named']) && array_key_exists($key, $params['named'])) {
                    $org_param_exists = true;
                    $this->orgParams[$key] = $params['named'][$key];
                }
            }
        }
        //独自パラメータ指定なし
        if (!$org_param_exists) {
            //自分の投稿
            $p_list = array_merge($p_list, $this->getMyPostList($start, $end));
            //自分が共有範囲指定された投稿
            $p_list = array_merge($p_list, $this->PostShareUser->getShareWithMeList($start, $end));
            //自分のサークルが共有範囲指定された投稿
            $p_list = array_merge($p_list, $this->PostShareCircle->getMyCirclePostList($start, $end));
            //フォローorコラボorマイメンバーのゴール投稿を取得
            $p_list = array_merge($p_list, $this->getRelatedPostList($start, $end));

        }
        //パラメータ指定あり
        else {
            //サークル指定
            if ($this->orgParams['circle_id']) {
                //サークル所属チェック
                $is_secret = $this->Circle->isSecret($this->orgParams['circle_id']);
                $is_exists_circle = $this->Circle->isBelongCurrentTeam($this->orgParams['circle_id'],
                                                                       $this->current_team_id);
                $is_belong_circle_member = $this->User->CircleMember->isBelong($this->orgParams['circle_id']);
                if (!$is_exists_circle || ($is_secret && !$is_belong_circle_member)) {
                    throw new RuntimeException(__d('gl', "サークルが存在しないか、権限がありません。"));
                }
                $p_list = array_merge($p_list,
                                      $this->PostShareCircle->getMyCirclePostList($start, $end, 'modified', 'desc',
                                                                                  1000, $this->orgParams['circle_id'],
                                                                                  PostShareCircle::SHARE_TYPE_SHARED));
            }
            //単独投稿指定
            elseif ($this->orgParams['post_id']) {
                //アクセス可能かチェック
                //ゴール投稿なら参照可能なゴールか？
                if ($this->isGoalPost($this->orgParams['post_id'])) {
                    $p_list = $this->orgParams['post_id'];
                }
                elseif (
                    //自分の投稿か？
                    $this->isMyPost($this->orgParams['post_id']) ||
                    //自分が共有範囲指定された投稿か？
                    $this->PostShareUser->isShareWithMe($this->orgParams['post_id']) ||
                    //自分のサークルが共有範囲指定された投稿か？
                    $this->PostShareCircle->isMyCirclePost($this->orgParams['post_id'])
                ) {
                    $p_list = $this->orgParams['post_id'];
                }
            }
            //特定ゴール指定
            elseif ($this->orgParams['goal_id']) {
                //アクションのみの場合
                if ($this->orgParams['type'] == self::TYPE_ACTION) {
                    $p_list = $this->getGoalPostList($this->orgParams['goal_id'], self::TYPE_ACTION, "modified", "desc",
                                                     $start, $end);
                }
            }
            //投稿主指定
            elseif ($this->orgParams['author_id']) {
                //アクションのみの場合
                if ($this->orgParams['type'] == self::TYPE_ACTION) {
                    $p_list = $this->getGoalPostList(null, self::TYPE_ACTION, "modified", "desc", $start, $end);
                }
            }
            //ゴールのみの場合
            elseif ($this->orgParams['filter_goal']) {
                $p_list = $this->getAllExistGoalPostList($start, $end);
            }
            // ユーザーID指定
            elseif ($this->orgParams['user_id']) {
                // 自分個人に共有された投稿
                $p_list = array_merge($p_list,
                                      $this->PostShareUser->getShareWithMeList(
                                          $start, $end, "PostShareUser.modified", "desc", 1000,
                                          ['user_id' => $this->orgParams['user_id']]));

                // 自分が閲覧可能なサークルへの投稿一覧
                // （公開サークルへの投稿 + 自分が所属している秘密サークルへの投稿）
                $p_list = array_merge($p_list,
                                      $this->PostShareCircle->getAccessibleCirclePostList(
                                          $start, $end, "PostShareCircle.modified", "desc", 1000,
                                          ['user_id' => $this->orgParams['user_id']]));
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
            if ($this->orgParams['type'] == self::TYPE_ACTION) {
                $post_options['order'] = ['ActionResult.id' => 'desc'];
                $post_options['contain'] = ['ActionResult'];
            }
            if ($this->orgParams['type'] == self::TYPE_NORMAL) {
                $post_options['conditions']['Post.type'] = self::TYPE_NORMAL;
            }
            $post_list = $this->find('list', $post_options);
        }

        //投稿を既読に
        $this->PostRead->red($post_list);

        $options = [
            'conditions' => [
                'Post.id' => $post_list,
            ],
            'order'      => [
                'Post.created' => 'desc'
            ],
            'contain'    => [
                'User'            => [
                    'fields' => $this->User->profileFields
                ],
                'MyPostLike'      => [
                    'conditions' => [
                        'MyPostLike.user_id' => $this->my_uid,
                        'MyPostLike.team_id' => $this->current_team_id,
                    ],
                ],
                'Circle',
                'Comment'         => [
                    'conditions'    => ['Comment.team_id' => $this->current_team_id],
                    'order'         => [
                        'Comment.created' => 'desc'
                    ],
                    'limit'         => 3,
                    'User'          => ['fields' => $this->User->profileFields],
                    'MyCommentLike' => [
                        'conditions' => [
                            'MyCommentLike.user_id' => $this->my_uid,
                            'MyCommentLike.team_id' => $this->current_team_id,
                        ]
                    ],
                ],
                'CommentId',
                'PostShareCircle' => [
                    'fields' => [
                        "PostShareCircle.id",
                        "PostShareCircle.circle_id",
                    ]
                ],
                'PostShareUser'   => [
                    'fields' => [
                        "PostShareUser.id",
                        "PostShareUser.user_id",
                    ]
                ],
                'Goal'            => [
                    'fields'  => [
                        'name',
                        'photo_file_name',
                        'id',
                    ],
                    'Purpose' => [
                        'fields' => [
                            'name'
                        ]
                    ]
                ],
                'KeyResult'       => [
                    'fields' => [
                        'id',
                        'name',
                    ],
                ],
                'ActionResult'    => [
                    'fields'    => [
                        'id',
                        'note',
                        'name',
                        'photo1_file_name',
                        'photo2_file_name',
                        'photo3_file_name',
                        'photo4_file_name',
                        'photo5_file_name',
                    ],
                    'KeyResult' => [
                        'fields' => [
                            'id',
                            'name',
                        ],
                    ],
                ]
            ],
        ];

        if ($this->orgParams['circle_id']) {
            $options['order'] = [
                'Post.modified' => 'desc'
            ];
        }

        if (!empty($this->orgParams['post_id'])) {
            //単独の場合はコメントの件数上限外す
            unset($options['contain']['Comment']['limit']);
        }

        if ($this->orgParams['type'] == self::TYPE_ACTION) {
            $options['order'] = ['ActionResult.id' => 'desc'];
        }

        $res = $this->find('all', $options);
        //コメントを逆順に
        foreach ($res as $key => $val) {
            if (!empty($val['Comment'])) {
                $res[$key]['Comment'] = array_reverse($res[$key]['Comment']);
            }
        }

        //コメントを既読に
        if (!empty($res)) {
            /** @noinspection PhpDeprecationInspection */
            $comment_list = Hash::extract($res, '{n}.Comment.{n}.id');
            $this->Comment->CommentRead->red($comment_list);
        }

        //１件のサークル名をランダムで取得
        $res = $this->getRandomShareCircleNames($res);
        //１件のユーザ名をランダムで取得
        $res = $this->getRandomShareUserNames($res);
        //シェアモードの特定
        $res = $this->getShareMode($res);
        //シェアメッセージの特定
        $res = $this->getShareMessages($res);
        //未読件数を取得
        $res = $this->getCommentMyUnreadCount($res);
        return $res;
    }

    public function isGoalPost($post_id)
    {
        $post = $this->find('first', ['conditions' => ['Post.id' => $post_id], 'fields' => ['Post.goal_id']]);
        if (!isset($post['Post']['goal_id']) || !$post['Post']['goal_id']) {
            return false;
        }
        return true;
    }

    public function getAllExistGoalPostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $options = [
            'conditions' => [
                'NOT'                      => [
                    'goal_id' => null,
                ],
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

    public function getGoalPostList($goal_id = null, $type = self::TYPE_ACTION, $order = "modified", $order_direction = "desc", $start = null, $end = null)
    {
        $options = [
            'conditions' => [
                'type'    => $type,
                'team_id' => $this->current_team_id,
            ],
            'order'      => [$order => $order_direction],
            'fields'     => ['id'],
        ];
        if ($start && $end) {
            $options['conditions']['modified BETWEEN ? AND ?'] = [$start, $end];
        }
        if ($goal_id) {
            $options['conditions']['goal_id'] = $goal_id;
        }
        if ($this->orgParams['author_id']) {
            $options['conditions']['user_id'] = $this->orgParams['author_id'];
        }
        $res = $this->find('list', $options);
        return $res;
    }

    function getRandomShareCircleNames($data)
    {
        foreach ($data as $key => $val) {
            if (!empty($val['PostShareCircle'])) {
                $circle_list = [];
                foreach ($val['PostShareCircle'] as $circle) {
                    $circle_list[] = $circle['circle_id'];
                }
                $circle_name = $this->PostShareCircle->Circle->getNameRandom($circle_list);
                $data[$key]['share_circle_name'] = $circle_name;
            }
        }
        return $data;
    }

    function getRandomShareUserNames($data)
    {
        foreach ($data as $key => $val) {
            if (!empty($val['PostShareUser'])) {
                $user_list = [];
                foreach ($val['PostShareUser'] as $user) {
                    $user_list[] = $user['user_id'];
                }
                $user_name = $this->User->getNameRandom($user_list);
                $data[$key]['share_user_name'] = $user_name;
            }
        }
        return $data;
    }

    function getShareMode($data)
    {
        foreach ($data as $key => $val) {
            if (!empty($val['PostShareCircle'])) {
                $data[$key]['share_mode'] = self::SHARE_CIRCLE;
            }
            else {
                if (!empty($val['PostShareUser'])) {
                    $data[$key]['share_mode'] = self::SHARE_PEOPLE;
                }
                else {
                    $data[$key]['share_mode'] = self::SHARE_ONLY_ME;
                }
            }
        }
        return $data;
    }

    function getShareMessages($data)
    {
        foreach ($data as $key => $val) {
            $data[$key]['share_text'] = null;
            switch ($val['share_mode']) {
                case self::SHARE_PEOPLE:
                    if (count($val['PostShareUser']) == 1) {
                        $data[$key]['share_text'] = __d('gl', "%sに共有",
                                                        $data[$key]['share_user_name']);
                    }
                    else {
                        $data[$key]['share_text'] = __d('gl', '%1$sと他%2$s人に共有',
                                                        $data[$key]['share_user_name'],
                                                        count($val['PostShareUser']) - 1);
                    }
                    break;
                case self::SHARE_ONLY_ME:
                    //自分だけ
                    $data[$key]['share_text'] = __d('gl', "自分のみ");
                    break;
                case self::SHARE_CIRCLE:
                    //共有ユーザがいない場合
                    if (count($val['PostShareUser']) == 0) {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = __d('gl', "%sに共有",
                                                            $data[$key]['share_circle_name']);
                        }
                        else {
                            $data[$key]['share_text'] = __d('gl', '%1$s他%2$sサークルに共有',
                                                            $data[$key]['share_circle_name'],
                                                            count($val['PostShareCircle']) - 1);
                        }
                    }
                    //共有ユーザが１人いる場合
                    elseif (count($val['PostShareUser']) == 1) {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = __d('gl', '%1$sと%2$sに共有',
                                                            $data[$key]['share_circle_name'],
                                                            $data[$key]['share_user_name']);
                        }
                        else {
                            $data[$key]['share_text'] = __d('gl', '%1$sと%2$s他%3$sサークルに共有',
                                                            $data[$key]['share_user_name'],
                                                            $data[$key]['share_circle_name'],
                                                            count($val['PostShareCircle']) - 1);
                        }

                    }
                    //共有ユーザが２人以上いる場合
                    else {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = __d('gl', '%1$s,%2$sと他%3$s人に共有',
                                                            $data[$key]['share_circle_name'],
                                                            $data[$key]['share_user_name'],
                                                            count($val['PostShareUser']) - 1);
                        }
                        else {
                            $data[$key]['share_text'] = __d('gl', '%1$sと他%2$s人,%3$s他%4$sサークルに共有',
                                                            $data[$key]['share_user_name'],
                                                            count($val['PostShareUser']) - 1,
                                                            $data[$key]['share_circle_name'],
                                                            count($val['PostShareCircle']) - 1);
                        }
                    }

                    break;
            }
        }
        return $data;
    }

    function getCommentMyUnreadCount($data)
    {
        foreach ($data as $key => $val) {
            if ($val['Post']['comment_count'] > 3) {
                $comment_list = [];
                foreach ($val['CommentId'] as $comment_id) {
                    if ($comment_id['user_id'] != $this->my_uid) {
                        $comment_list[] = $comment_id['id'];
                    }
                }
                //未読件数 = 自分以外のコメント数 - 自分以外のコメントの自分の既読数
                $data[$key]['unread_count'] =
                    count($comment_list) - $this->Comment->CommentRead->countMyRead($comment_list);
            }
        }
        return $data;
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

    /**
     * 投稿の全共有メンバーのリストを返す
     *
     * @param $post_id
     *
     * @return array
     */
    function getShareAllMemberList($post_id)
    {
        error_log("FURU:memberlist1\n",3,"/tmp/hoge.log");

        $post = $this->findById($post_id);
        if (empty($post)) {
            return [];
        }
        error_log("FURU:memberlist2\n",3,"/tmp/hoge.log");
        $share_member_list = [];
        //サークル共有ユーザを追加
        $share_member_list = $share_member_list + $this->PostShareCircle->getShareCircleMemberList($post_id);
        //メンバー共有なら
        $share_member_list = $share_member_list + $this->PostShareUser->getShareUserListByPost($post_id);
        error_log("FURU:memberlist3:$this->my_uid\n",3,"/tmp/hoge.log");
        //Postの主が自分ではないなら追加
        $posted_user_id = viaIsSet($post['Post']['user_id']);
        error_log("FURU:".$this->my_uid.":".$post['Post']['user_id']."\n", 3, "/tmp/hoge.log");
        if($this->my_uid != $posted_user_id){
            $share_member_list[] = $posted_user_id;
        }
        $share_member_list = array_unique($share_member_list);

        //自分自身を除外
        $key = array_search($this->my_uid, $share_member_list);
        error_log("FURU:aaaaa:".print_r($share_member_list,true)."\n", 3, "/tmp/hoge.log");
        error_log("FURU:aaaaa:".$key."\n", 3, "/tmp/hoge.log");
        if ($key !== false) {
            unset($share_member_list[$key]);
        }
        error_log("FURU:bbbbbbb:".print_r($share_member_list,true)."\n", 3, "/tmp/hoge.log");
        return $share_member_list;
    }

    /**
     * @param       $type
     * @param       $goal_id
     * @param null  $uid
     * @param bool  $public
     * @param null  $model_id
     * @param array $share
     * @param int   $share_type
     *
     * @return mixed
     * @throws Exception
     */
    function addGoalPost($type, $goal_id, $uid = null, $public = true, $model_id = null, $share = null, $share_type = PostShareCircle::SHARE_TYPE_SHARED)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }

        $data = [
            'user_id' => $uid,
            'team_id' => $this->current_team_id,
            'type'    => $type,
            'goal_id' => $goal_id,
        ];

        switch ($type) {
            case self::TYPE_ACTION:
                $data['action_result_id'] = $model_id;
                break;
            case self::TYPE_KR_COMPLETE:
                $data['key_result_id'] = $model_id;
                break;
        }
        $res = $this->save($data);
        if ($res) {
            if ($public && $team_all_circle_id = $this->Circle->getTeamAllCircleId()) {
                return $this->PostShareCircle->add($this->getLastInsertID(), [$team_all_circle_id]);
            }
            if ($share) {
                return $this->doShare($this->getLastInsertID(), $share, $share_type);
            }
        }
        return $res;
    }

    function doShare($post_id, $share, $share_type = PostShareCircle::SHARE_TYPE_SHARED)
    {
        if (!$share) {
            return false;
        }
        $share = explode(",", $share);
        $public = false;
        //TODO 近々、ここは「チーム全体」をサークル化する為、この処理はいずれ削除する。
        foreach ($share as $key => $val) {
            if (stristr($val, 'public')) {
                $public = true;
                unset($share[$key]);
            }
        }
        //TODO ここまで
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
        if ($public && $team_all_circle_id = $this->Circle->getTeamAllCircleId()) {
            $circles[] = $team_all_circle_id;
        }
        //共有ユーザ保存
        $this->PostShareUser->add($post_id, $users, null, $share_type);
        //共有サークル保存
        $this->PostShareCircle->add($post_id, $circles, null, $share_type);
        if ($share_type == PostShareCircle::SHARE_TYPE_SHARED) {
            //共有サークル指定されてた場合の未読件数更新
            $this->User->CircleMember->incrementUnreadCount($circles);
            //共有サークル指定されてた場合、更新日時更新
            $this->User->CircleMember->updateModified($circles);
            $this->PostShareCircle->Circle->updateModified($circles);
        }
        return true;
    }

    /**
     * Description : Added a new method for insertion of new public circles in the post table
     *
     * @param      $circle_id
     * @param null $uid
     *
     * @return mixed
     * @throws Exception
     */
    function createCirclePost($circle_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }

        $data = [
            'user_id'   => $uid,
            'team_id'   => $this->current_team_id,
            'type'      => self::TYPE_CREATE_CIRCLE,
            'circle_id' => $circle_id,
        ];
        $res = $this->save($data);
        if ($team_all_circle_id = $this->Circle->getTeamAllCircleId()) {
            $this->PostShareCircle->add($this->getLastInsertID(), [$team_all_circle_id]);
        }
        return $res;
    }

    /**
     * 投稿数のカウントを返却
     *
     * @param mixed $user_id ユーザーIDもしくは'me'を指定する。
     * @param null  $start_date
     * @param null  $end_date
     *
     * @return int
     */
    function getCount($user_id = 'me', $start_date = null, $end_date = null)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
                'type'    => self::TYPE_NORMAL
            ]
        ];
        // ユーザーIDに'me'が指定された場合は、自分のIDをセットする
        if ($user_id == 'me') {
            $options['conditions']['user_id'] = $this->my_uid;
        }
        elseif (is_numeric($user_id)) {
            $options['conditions']['user_id'] = $user_id;
        }

        //期間で絞り込む
        if ($start_date) {
            $options['conditions']['modified >'] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['modified <'] = $end_date;
        }
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * @param      $post_ids
     * @param bool $only_one
     *
     * @return array|null
     */
    public function getForRed($post_ids, $only_one = false)
    {
        $options = [
            'conditions' => [
                'Post.id'      => $post_ids,
                'Post.team_id' => $this->current_team_id
            ],
            'fields'     => [
                'Post.id'
            ],
            'order'      => [
                'Post.created' => 'desc'
            ],
            'contain'    => [
                'Comment' => [
                    'conditions' => ['Comment.team_id' => $this->current_team_id],
                    'order'      => ['Comment.created' => 'desc'],
                    'limit'      => 3,
                    'fields'     => ['Comment.id']
                ],
            ],
        ];
        if ($only_one) {
            //単独の場合はコメントの件数上限外す
            unset($options['contain']['Comment']['limit']);
        }
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * execコマンドにて既読処理を行う
     *
     * @param          $post_id
     * @param bool|int $only_one
     */
    public function execRedPostComment($post_id, $only_one = 0)
    {
        $method_name = "red_post_comment";
        $set_web_env = "";
        $nohup = "nohup ";
        $php = "/usr/bin/php ";
        $cake_cmd = $php . APP . "Console" . DS . "cake.php";
        $cake_app = " -app " . APP;
        $cmd = " post {$method_name}";
        $cmd .= " -u " . $this->my_uid;
        $cmd .= " -t " . $this->current_team_id;
        $cmd .= " -p " . base64_encode(serialize($post_id));
        $cmd .= " -o " . $only_one;
        $cmd_end = " > /dev/null &";
        $all_cmd = $set_web_env . $nohup . $cake_cmd . $cake_app . $cmd . $cmd_end;
        exec($all_cmd);
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
                'SUM(post_like_count) as sum_like',
            ],
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
                'type'    => [self::TYPE_NORMAL, self::TYPE_ACTION],
            ]
        ];
        //期間で絞り込む
        if ($start_date) {
            $options['conditions']['modified >'] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['modified <'] = $end_date;
        }
        $res = $this->find('first', $options);
        return $res ? $res[0]['sum_like'] : 0;
    }

}
