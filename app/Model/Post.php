<?php
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('TextExHelper', 'View/Helper');
App::uses('View', 'View');
App::uses('PostShareCircle', 'Model');
App::uses('AppModel', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Translation', 'Model');
App::uses('Comment', 'Model');
App::import('Service', 'PostResourceService');
App::import('Service', 'PostService');
App::import('Service', 'ExperimentService');
App::import('Lib/DataExtender', 'CommentExtender');
App::import('Lib/DataExtender', 'PostExtender');
App::import('Model/Entity', 'PostEntity');
App::import('Model', 'HavingMentionTrait');

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
 * @property AttachedFile    $AttachedFile
 * @property PostFile        $PostFile
 * @property PostSharedLog   $PostSharedLog
 * @property SavedPost       $SavedPost
 */

use Goalous\Enum\DataType\DataType as DataType;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Post\PostResourceType as PostResourceType;

class Post extends AppModel
{
    use HavingMentionTrait;
    public $bodyProperty = 'body';
    /**
     * post type
     */
    const TYPE_NORMAL = 1;
    const TYPE_CREATE_GOAL = 2;
    const TYPE_ACTION = 3;
    const TYPE_BADGE = 4; // unused now
    const TYPE_KR_COMPLETE = 5;
    const TYPE_GOAL_COMPLETE = 6;
    const TYPE_CREATE_CIRCLE = 7;
    const TYPE_MESSAGE = 8; // unused now?
    const SHARE_PEOPLE = 2;
    const SHARE_ONLY_ME = 3;
    const SHARE_CIRCLE = 4;
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
    public $orgParams = [
        'author_id'     => null,
        'circle_id'     => null,
        'user_id'       => null,
        'post_id'       => null,
        'goal_id'       => null,
        'key_result_id' => null,
        'filter_goal'   => null,
        'type'          => null,
    ];
    public $uses = [
        'AttachedFile'
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
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo2'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo3'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo4'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo5'          => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'site_photo'      => [
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'body'            => [
            'maxLength' => ['rule' => ['maxLength', 10000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]
    ];
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

    //The Associations below have been created with all possible keys, those that are not needed can be removed
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
        ],
        'PostFile',
        'SavedPost',
    ];

    public $modelConversionTable = [
        'user_id'          => DataType::INT,
        'team_id'          => DataType::INT,
        'comment_count'    => DataType::INT,
        'post_like_count'  => DataType::INT,
        'post_read_count'  => DataType::INT,
        'important_flg'    => DataType::BOOL,
        'goal_id'          => DataType::INT,
        'circle_id'        => DataType::INT,
        'action_result_id' => DataType::INT,
        'key_result_id'    => DataType::INT,
        'site_info'        => DataType::JSON
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        $this->_setTypeMessage();
    }

    function _setTypeMessage()
    {
        self::$TYPE_MESSAGE[self::TYPE_CREATE_GOAL] = __("Created a new Goal.");
        self::$TYPE_MESSAGE[self::TYPE_CREATE_CIRCLE] = __("Created a new circle.");
    }

    public function beforeValidate($options = [])
    {
        parent::beforeValidate($options);

        // OGP 画像が存在する場合、画像の形式をチェックして
        // 通常の画像形式でない場合はデフォルトの画像を表示するようにする
        // （validate の段階でチェックすると投稿エラーになってしまうため）
        if (isset($this->data['Post']['site_photo']['type'])) {
            if (isset($this->validate['site_photo']['image_type']['rule'][1])) {
                $image_types = $this->validate['site_photo']['image_type']['rule'][1];
                if (!in_array($this->data['Post']['site_photo']['type'], $image_types)) {
                    // 画像形式が許容されていない場合、画像が存在しないものとする
                    $this->data['Post']['site_photo'] = null;
                }
            }
        }
        return true;
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
        $g_list = array_merge($g_list, $this->Goal->GoalMember->getCollaboGoalList($this->my_uid, true));
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
                'PostFile'      => [
                    'order'        => ['PostFile.index_num asc'],
                    'AttachedFile' => [
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ]
                ]
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    public function getPhotoPath($user_arr)
    {
        $upload = new UploadHelper(new View());
        return $upload->uploadUrl($user_arr, 'User.photo', ['style' => 'medium']);
    }

    /**
     * @param int  $page
     * @param int  $limit
     * @param null $start
     * @param null $end
     * @param null $params
     * @param bool $contains_message
     *
     * @return array|null
     * @deprecated
     * [Important]
     * Don't use this method when new implementation
     * this is too chaos and has too much a role
     * e.g. read post/comment. but as a major principle, one method has one role.
     */
    public function get($page = 1, $limit = 20, $start = null, $end = null, $params = null, $contains_message = false)
    {

        if (!$start) {
            $start = strtotime("-1 month", REQUEST_TIMESTAMP);
        } elseif (!is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$end) {
            $end = REQUEST_TIMESTAMP;
        } elseif (!is_numeric($end)) {
            $end = strtotime($end);
        }
        if (isset($params['named']['page']) || !empty($params['named']['page'])) {
            $page = $params['named']['page'];
            unset($params['named']['page']);
        }
        // このパラメータが指定された場合、post_time_before より前の投稿のみを読み込む
        // 実際の値は投稿の並び順によって created か modified になる
        $post_time_before = null;
        if (isset($params['named']['post_time_before']) && !empty($params['named']['post_time_before'])) {
            $post_time_before = $params['named']['post_time_before'];
        }
        $org_param_exists = false;
        if ($params) {
            foreach ($this->orgParams as $key => $val) {
                if (array_key_exists($key, $params)) {
                    $org_param_exists = true;
                    $this->orgParams[$key] = $params[$key];
                } elseif (isset($params['named']) && array_key_exists($key, $params['named'])) {
                    $org_param_exists = true;
                    $this->orgParams[$key] = $params['named'][$key];
                }
            }
        }
        $logUserId = 174;
        if ($this->orgParams['author_id'] == $logUserId){
            GoalousLog::warning(print_r($page, true));
            GoalousLog::warning(print_r($limit, true));
            GoalousLog::warning(print_r($start, true));
            GoalousLog::warning(print_r($end, true));
            GoalousLog::warning(print_r($params, true));
            GoalousLog::warning(print_r($contains_message, true));
        }

        $post_filter_conditions = [
            'OR'                           => [],
            'Post.created BETWEEN ? AND ?' => [$start, $end],
        ];
        /**
         * @var DboSource $db
         */
        $db = $this->getDataSource();
        $single_post_id = null;

        //独自パラメータ指定なし
        if (!$org_param_exists) {
            // TODO: `show_for_all_feed_flg`  must be deleted for Goalous feature
            // Originally, actions and circle posts should not be displayed as mix on top page
            /** @var ExperimentService $ExperimentService */
            $ExperimentService = ClassRegistry::init('ExperimentService');
            $showForAllFeedFlg = $ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_ON,
                $this->current_team_id);

            if ($showForAllFeedFlg) {
                //自分の投稿
                $post_filter_conditions['OR'][] = $this->getConditionGetMyPostList();
                //自分が共有範囲指定された投稿
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterPostIdShareWithMe($db, $start,
                            $end) . ')');
                //自分のサークルが共有範囲指定された投稿
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterMyCirclePostId($db, $start, $end) . ')');
            }

            //関連ゴール
            $post_filter_conditions['OR'][] =
                $db->expression('Post.id IN (' . $this->getSubQueryFilterRelatedGoalPost($db, $start, $end,
                        [self::TYPE_KR_COMPLETE]) . ')');

            //すべてのユーザが閲覧可能なゴール投稿
            $post_filter_conditions['OR'][] = $this->getConditionAllGoalPostId([
                    self::TYPE_CREATE_GOAL,
                    self::TYPE_ACTION,
                    self::TYPE_GOAL_COMPLETE,
                ]
            );

        } //パラメータ指定あり
        else {
            //サークル指定
            if ($this->orgParams['circle_id']) {
                //サークル所属チェック
                $is_secret = $this->Circle->isSecret($this->orgParams['circle_id']);
                $is_exists_circle = $this->Circle->isBelongCurrentTeam($this->orgParams['circle_id'],
                    $this->current_team_id);
                $is_belong_circle_member = $this->User->CircleMember->isBelong($this->orgParams['circle_id']);
                if (!$is_exists_circle || ($is_secret && !$is_belong_circle_member)) {
                    throw new RuntimeException(__("The circle dosen't exist or you don't have permission."));
                }
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterMyCirclePostId($db, $start, $end,
                            $this->orgParams['circle_id'],
                            PostShareCircle::SHARE_TYPE_SHARED) . ')');

            } //単独投稿指定
            elseif ($this->orgParams['post_id']) {
                //アクセス可能かチェック
                if (
                    //ゴール投稿か？であれば参照可能
                    $this->isGoalPost($this->orgParams['post_id']) ||
                    //自分の投稿か？であれば参照可能
                    $this->isMyPost($this->orgParams['post_id']) ||
                    // 公開サークルに共有された投稿か？であれば参照可能
                    $this->PostShareCircle->isShareWithPublicCircle($this->orgParams['post_id']) ||
                    //自分が共有範囲指定された投稿か？であれば参照可能
                    $this->PostShareUser->isShareWithMe($this->orgParams['post_id']) ||
                    //自分のサークルが共有範囲指定された投稿か？であれば参照可能
                    $this->PostShareCircle->isMyCirclePost($this->orgParams['post_id'])
                ) {
                    $single_post_id = $this->orgParams['post_id'];
                }
            } //特定のKR指定
            elseif ($this->orgParams['key_result_id']) {
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterKrPostList($db,
                            $this->orgParams['key_result_id'],
                            $this->orgParams['author_id'] ? $this->orgParams['author_id'] : null,
                            self::TYPE_ACTION,
                            $start, $end) . ')');
            } //特定ゴール指定
            elseif ($this->orgParams['goal_id']) {
                //アクションのみの場合
                if ($this->orgParams['type'] == self::TYPE_ACTION) {
                    $post_filter_conditions['OR'][] =
                        $db->expression('Post.id IN (' . $this->getSubQueryFilterGoalPostList($db,
                                $this->orgParams['goal_id'],
                                self::TYPE_ACTION, $start,
                                $end) . ')');

                }
            } //投稿主指定
            elseif ($this->orgParams['author_id']) {
                //アクションのみの場合
                if ($this->orgParams['type'] == self::TYPE_ACTION) {
                    $post_filter_conditions['OR'][] =
                        $db->expression('Post.id IN (' . $this->getSubQueryFilterGoalPostList($db, null,
                                self::TYPE_ACTION, $start,
                                $end) . ')');
                }
            } //ゴールのみの場合
            elseif ($this->orgParams['filter_goal']) {
                $post_filter_conditions['OR'][] = $this->getConditionAllGoalPostId([
                        self::TYPE_CREATE_GOAL,
                        self::TYPE_KR_COMPLETE,
                        self::TYPE_ACTION,
                        self::TYPE_GOAL_COMPLETE,
                    ]
                );
            } // ユーザーID指定
            elseif ($this->orgParams['user_id']) {
                // 自分個人に共有された投稿
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterPostIdShareWithMe($db, $start, $end,
                            ['user_id' => $this->orgParams['user_id']]) . ')');

                // 自分が閲覧可能なサークルへの投稿一覧
                // （公開サークルへの投稿 + 自分が所属している秘密サークルへの投稿）
                //getSubQueryFilterAccessibleCirclePostList
                $post_filter_conditions['OR'][] =
                    $db->expression('Post.id IN (' . $this->getSubQueryFilterAccessibleCirclePostList($db, $start, $end,
                            ['user_id' => $this->orgParams['user_id']]) . ')');

                // 自分自身の user_id が指定された場合は、自分の投稿を含める
                if ($this->my_uid == $this->orgParams['user_id']) {
                    $post_filter_conditions['OR'][] = $this->getConditionGetMyPostList();
                }
            }
        }

        if (!empty($this->orgParams['post_id'])) {
            //単独投稿指定の場合はそのまま
            $post_list = $single_post_id;
        } else {
            //単独投稿以外は再度、件数、オーダーの条件を入れ取得
            $post_options = [
                'conditions' => $post_filter_conditions,
                'limit'      => $limit,
                'page'       => $page,
                'order'      => [
                    'Post.created' => 'desc'
                ],
            ];
            if ($this->orgParams['type'] == self::TYPE_ACTION) {
                $post_options['order'] = ['ActionResult.id' => 'desc'];
                $post_options['contain'] = ['ActionResult'];
            }
            if ($this->orgParams['type'] == self::TYPE_NORMAL) {
                $post_options['conditions']['Post.type'] = self::TYPE_NORMAL;
            }
            if ($contains_message === false) {
                $post_options['conditions']['NOT']['Post.type'] = self::TYPE_MESSAGE;
            }

            // 独自パラメータ無しの場合（ホームフィードの場合）
            if (!$org_param_exists) {
                $post_options['order'] = ['Post.created' => 'desc'];
            }
            // 読み込む投稿の更新時間が指定されている場合
            if ($post_time_before) {
                // [Hotfix]https://jira.goalous.com/browse/GL-6888
                // This condition conflicts `Post.created BETWEEN {$start} and {$end}, so the bug that past posts cant' get has been occurred.
                // In addition, $past_time_before value is not appropriate
                // We shouldn't use this condition.
//                $order_col = key($post_options['order']);
//                $post_options['conditions']["$order_col <="] = $post_time_before;
            }
            $post_list = $this->find('list', $post_options);
            if ($this->orgParams['author_id'] == $logUserId){
                GoalousLog::warning('SQL', $this->getDataSource()->getLog());
                GoalousLog::warning(print_r($post_list, true));
            }

        }
        //投稿を既読に
        // But Not read the post display from user's page
        // https://jira.goalous.com/browse/GL-8709
        $isOpeningUserPagePostList = !empty($this->orgParams['user_id'])
            && $this->orgParams['type'] === self::TYPE_NORMAL;
        if (!$isOpeningUserPagePostList) {
            $this->PostRead->red($post_list);
        }

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
                    'CommentFile'   => [
                        'order'        => ['CommentFile.index_num asc'],
                        'AttachedFile' => [
                            'User' => [
                                'fields' => $this->User->profileFields
                            ]
                        ]
                    ]
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
                    'fields'    => [
                        'name',
                        'photo_file_name',
                        'id',
                        'end_date',
                        'completed'
                    ],
                    'User'      => [
                        'fields'     => $this->User->profileFields,
                        'TeamMember' => [
                            'fields'     => [
                                'coach_user_id',
                            ],
                            'conditions' => [
                                'coach_user_id' => $this->my_uid,
                            ]
                        ],
                    ],
                    'MyCollabo' => [
                        'conditions' => [
                            'MyCollabo.type'    => GoalMember::TYPE_COLLABORATOR,
                            'MyCollabo.user_id' => $this->my_uid,
                        ],
                        'fields'     => [
                            'MyCollabo.id',
                            'MyCollabo.role',
                            'MyCollabo.description',
                        ],
                    ],
                    'MyFollow'  => [
                        'conditions' => [
                            'MyFollow.user_id' => $this->my_uid,
                        ],
                        'fields'     => [
                            'MyFollow.id',
                        ],
                    ]
                ],
                'KeyResult'       => [
                    'fields' => [
                        'id',
                        'name',
                        'end_date'
                    ],
                ],
                'ActionResult'    => [
                    'fields'           => [
                        'id',
                        'note',
                        'name',
                        'photo1_file_name',
                        'photo2_file_name',
                        'photo3_file_name',
                        'photo4_file_name',
                        'photo4_file_name',
                        'photo5_file_name',
                        'key_result_before_value',
                        'key_result_change_value',
                        'key_result_target_value'
                    ],
                    'KeyResult'        => [
                        'fields' => [
                            'id',
                            'name',
                            'target_value',
                            'value_unit'
                        ],
                    ],
                    'ActionResultFile' => [
                        'order'        => ['ActionResultFile.index_num asc'],
                        'AttachedFile' => [
                            'User' => [
                                'fields' => $this->User->profileFields
                            ]
                        ]
                    ],
                    'KrProgressLog'    => [
                        'fields' => [
                            'value_unit',
                            'before_value',
                            'change_value',
                            'target_value',
                        ]
                    ]
                ],
                'PostFile'        => [
                    'order'        => ['PostFile.index_num asc'],
                    'AttachedFile' => [
                        'User' => [
                            'fields' => $this->User->profileFields
                        ]
                    ]
                ]
            ],
        ];

        if (is_array($post_list)) {
            $options['conditions'][] = ['Post.created BETWEEN ? AND ?' => [$start, $end]];
        }

        // note: changed sorting from Post.modified to created DESC, so that only latest posts can be shown on top
        // if someone comment on post will not effect
        if ($this->orgParams['circle_id']) {
            $options['order'] = [
                'Post.created' => 'desc'
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
        if ($this->orgParams['author_id'] == $logUserId){
            GoalousLog::warning(print_r($res, true));
            GoalousLog::warning('SQL', $this->getDataSource()->getLog());
        }


        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');
        /** @var PostExtender $PostExtender */
        $PostExtender = ClassRegistry::init('PostExtender');

        foreach ($res as $key => $val) {
            //コメントを逆順に
            if (!empty($val['Comment'])) {
                $res[$key]['Comment'] = array_reverse($res[$key]['Comment']);
            }
            try {
                //Extend translation language in post
                $res[$key]['Post'] = $PostExtender->extend($res[$key]['Post'], $this->my_uid, $this->current_team_id,
                    [PostExtender::EXTEND_TRANSLATION_LANGUAGE]);
                //Extend translation language in comment
                $res[$key]['Comment'] = $CommentExtender->extendMulti($res[$key]['Comment'], $this->my_uid,
                    $this->current_team_id, [CommentExtender::EXTEND_TRANSLATION_LANGUAGE]);
            } catch (Exception $exception) {
                GoalousLog::error("Failed extending translation information in posts.", [
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                    'user_id' => $this->my_uid,
                    'team_id' => $this->current_team_id
                ]);
            }
        }

        //コメントを既読に
        if (!empty($res)) {
            /** @noinspection PhpDeprecationInspection */
            $comment_list = Hash::extract($res, '{n}.Comment.{n}.id');
            //新たにコメントを既読にした
            if ($comment_new_read = $this->Comment->CommentRead->red($comment_list)) {
                foreach ($res as $p_k => $p_v) {
                    if (isset($p_v['Comment'])) {
                        foreach ($p_v['Comment'] as $c_k => $c_v) {
                            if (in_array($c_v['id'], $comment_new_read)) {
                                $res[$p_k]['Comment'][$c_k]['comment_read_count']++;
                            }
                        }
                    }
                }
            }
        }

        //１件のサークル名をランダムで取得
        $res = $this->getRandomShareCircleNames($res, $this->current_team_id);
        //１件のユーザ名をランダムで取得
        $res = $this->getRandomShareUserNames($res);
        //シェアモードの特定
        $res = $this->getShareMode($res);
        //シェアメッセージの特定
        $res = $this->getShareMessages($res);
        //未読件数を取得
        $res = $this->getCommentMyUnreadCount($res);

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        // get post resources
        $postIds = Hash::extract($res, '{n}.Post.id') ?? [];
        $postResources = $PostResource->getResourcesByPostId($postIds);
        foreach ($res as $key => $post) {
            $res[$key] = am($post, [
                'PostResources' => $postResources[$post['Post']['id']] ?? [],
            ]);
            $res[$key]['hasVideoResource'] = false;
            foreach ($res[$key]['PostResources'] as $resource) {
                if ($resource['resource_type'] === PostResourceType::VIDEO_STREAM) {
                    $res[$key]['hasVideoResource'] = true;
                    break;
                }
            }
        }

        //Set whether login user saved favorite post
        $res = $this->setIsSavedItemEachPost($res, $this->my_uid);

        if ($this->orgParams['author_id'] == $logUserId){
            GoalousLog::warning(print_r($res, true));
        }
        return $res;
    }

    public function getConditionGetMyPostList()
    {
        return ['Post.user_id' => $this->my_uid];
    }

    /**
     * 自分に共有された投稿のID一覧を返す
     *
     * @param DboSource $db
     * @param           $start
     * @param           $end
     * @param array     $params
     *                 'user_id' : 指定すると投稿者で絞る
     *
     * @return string|null
     */
    public function getSubQueryFilterPostIdShareWithMe(DboSource $db, $start, $end, array $params = [])
    {
        // パラメータデフォルト
        $params = array_merge(['user_id' => null], $params);
        $query = [
            'fields'     => ['PostShareUser.post_id'],
            'table'      => $db->fullTableName($this->PostShareUser),
            'alias'      => 'PostShareUser',
            'conditions' => [
                'PostShareUser.user_id'                 => $this->my_uid,
                'PostShareUser.team_id'                 => $this->current_team_id,
                'PostShareUser.created BETWEEN ? AND ?' => [$start, $end],
            ],
        ];
        if ($params['user_id'] !== null) {
            $query['conditions']['Post.user_id'] = $params['user_id'];
            $query['joins'][] = [
                'type'       => 'LEFT',
                'table'      => $db->fullTableName($this),
                'alias'      => 'Post',
                'conditions' => '`PostShareUser`.`post_id`=`Post`.`id`',
            ];
        }
        $res = $db->buildStatement($query, $this);
        return $res;
    }

    public function getSubQueryFilterMyCirclePostId(
        DboSource $db,
        $start,
        $end,
        $my_circle_list = null,
        $share_type = null
    ) {
        if (!$my_circle_list) {
            $my_circle_list = $this->Circle->CircleMember->getMyCircleList(true);
        }

        $query = [
            'fields'     => ['PostShareCircle.post_id'],
            'table'      => $db->fullTableName($this->PostShareCircle),
            'alias'      => 'PostShareCircle',
            'conditions' => [
                'PostShareCircle.circle_id'               => $my_circle_list,
                'PostShareCircle.team_id'                 => $this->current_team_id,
                'PostShareCircle.created BETWEEN ? AND ?' => [$start, $end],
            ],
        ];
        if ($share_type !== null) {
            $query['conditions']['PostShareCircle.share_type'] = $share_type;
        }
        if (!is_array($my_circle_list) && $this->Circle->isTeamAllCircle($my_circle_list)) {
            $query['conditions']['NOT'] = [
                'type' => [
                    self::TYPE_ACTION,
                    self::TYPE_CREATE_GOAL,
                    self::TYPE_GOAL_COMPLETE,
                    self::TYPE_KR_COMPLETE
                ]
            ];
        }
        $res = $db->buildStatement($query, $this);
        return $res;
    }

    /**
     * @param DboSource  $db
     * @param            $start
     * @param            $end
     * @param array      $post_types
     *
     * @return string
     */
    public function getSubQueryFilterRelatedGoalPost(DboSource $db, $start, $end, $post_types)
    {
        $related_goal_ids = $this->Goal->getRelatedGoals();

        $query = [
            'fields'     => ['Post.id'],
            'table'      => $db->fullTableName($this->Goal),
            'alias'      => 'Goal',
            'conditions' => [
                'Goal.team_id' => $this->current_team_id,
                'Goal.id'      => $related_goal_ids,
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => $db->fullTableName($this),
                    'alias'      => 'Post',
                    'conditions' => [
                        '`Goal`.`id`=`Post`.`goal_id`',
                        'Post.created BETWEEN ? AND ?' => [$start, $end],
                        'Post.type'                    => $post_types,
                    ],
                ]
            ]
        ];
        $res = $db->buildStatement($query, $this);

        return $res;
    }

    /**
     * @param $post_types
     *
     * @return array
     */
    public function getConditionAllGoalPostId($post_types)
    {
        $res = [
            'NOT'       => [
                'Post.goal_id' => null,
            ],
            'Post.type' => $post_types
        ];
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

    /**
     * @param      $postId
     * @param null $userId
     * @param null $teamId
     *
     * @return bool
     */
    public function isMyPost($postId, $userId = null, $teamId = null)
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        $options = [
            'conditions' => [
                'id'      => $postId,
                'team_id' => $teamId,
                'user_id' => $userId,
            ]
        ];
        $res = $this->find('list', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    /**
     * Check whether the post is owned by the user
     *
     * @param int $postId
     * @param int $userId
     *
     * @return bool True if owned
     */
    public function isPostOwned(int $postId, int $userId): bool
    {
        $options = [
            'conditions' => [
                'id'      => $postId,
                'user_id' => $userId,
            ]
        ];
        $res = $this->find('list', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    public function getSubQueryFilterKrPostList(
        DboSource $db,
        $key_result_id,
        $user_id = null,
        $type,
        $start = null,
        $end = null
    ) {
        $query = [
            'fields'     => ['Post.id'],
            'table'      => $db->fullTableName($this),
            'alias'      => 'Post',
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => $db->fullTableName($this->ActionResult),
                    'alias'      => 'ActionResult',
                    'conditions' => '`ActionResult`.`id`=`Post`.`action_result_id`',
                ],
            ],
            'conditions' => [
                'Post.type'                  => $type,
                'Post.team_id'               => $this->current_team_id,
                'ActionResult.key_result_id' => $key_result_id,
            ],
        ];

        // 仕様上アクションの投稿日時は必ずゴールの期間内になるためこの条件は必要無いが、
        // MySQLで投稿テーブルを日付でパーティショニングしてるため、検索条件に投稿日時を追加している。
        // これが無いと投稿データをフルスキャンしてしまう。
        if ($start !== null && $end !== null) {
            $query['conditions']['Post.created BETWEEN ? AND ?'] = [$start, $end];
        }
        if ($user_id) {
            $query['conditions']['Post.user_id'] = $user_id;
        }
        $res = $db->buildStatement($query, $this);
        return $res;
    }

    public function getSubQueryFilterGoalPostList(
        DboSource $db,
        $goal_id = null,
        $type = self::TYPE_ACTION,
        $start = null,
        $end = null
    ) {
        $query = [
            'fields'     => ['Post.id'],
            'table'      => $db->fullTableName($this),
            'alias'      => 'Post',
            'conditions' => [
                'Post.type'    => $type,
                'Post.team_id' => $this->current_team_id,
            ],
        ];
        // 仕様上アクションの投稿日時は必ずゴールの期間内になるためこの条件は必要無いが、
        // MySQLで投稿テーブルを日付でパーティショニングしてるため、検索条件に投稿日時を追加している。
        // これが無いと投稿データをフルスキャンしてしまう。
        if ($start && $end) {
            $query['conditions']['Post.created BETWEEN ? AND ?'] = [$start, $end];
        }
        if ($goal_id) {
            $query['conditions']['Post.goal_id'] = $goal_id;
        }
        if ($this->orgParams['author_id']) {
            $query['conditions']['Post.user_id'] = $this->orgParams['author_id'];
        }
        $res = $db->buildStatement($query, $this);
        return $res;
    }

    /**
     * 自分の閲覧可能な投稿のID一覧を返す
     * （公開サークルへの投稿 + 自分が所属している秘密サークルへの投稿）
     *
     * @param DboSource $db
     * @param           $start
     * @param           $end
     * @param array     $params
     *                 'user_id' : 指定すると投稿者IDで絞る
     *
     * @return string|null
     */
    public function getSubQueryFilterAccessibleCirclePostList(DboSource $db, $start, $end, array $params = [])
    {
        // パラメータデフォルト
        $params = array_merge(['user_id' => null], $params);

        $my_circle_list = $this->Circle->CircleMember->getMyCircleList();
        $query = [
            'fields'     => ['PostShareCircle.post_id'],
            'table'      => $db->fullTableName($this->PostShareCircle),
            'alias'      => 'PostShareCircle',
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => $db->fullTableName($this->Circle),
                    'alias'      => 'Circle',
                    'conditions' => '`PostShareCircle`.`circle_id`=`Circle`.`id`',
                ],
            ],
            'conditions' => [
                'OR'                                       => [
                    'PostShareCircle.circle_id' => $my_circle_list,
                    'Circle.public_flg'         => 1
                ],
                'PostShareCircle.team_id'                  => $this->current_team_id,
                'PostShareCircle.modified BETWEEN ? AND ?' => [$start, $end],
            ],
        ];

        if ($params['user_id'] !== null) {
            $query['joins'][] = [
                'type'       => 'LEFT',
                'table'      => $db->fullTableName($this),
                'alias'      => 'Post',
                'conditions' => '`PostShareCircle`.`post_id`=`Post`.`id`',
            ];
            $query['conditions']['Post.user_id'] = $params['user_id'];
        }
        $res = $db->buildStatement($query, $this);
        return $res;
    }

    function getRandomShareCircleNames($data, $teamId = null)
    {
        foreach ($data as $key => $val) {
            if (!empty($val['PostShareCircle'])) {
                $circle_list = [];
                foreach ($val['PostShareCircle'] as $circle) {
                    $circle_list[] = $circle['circle_id'];
                }
                $circle_name = $this->PostShareCircle->Circle->getNameRandom($circle_list, $teamId);
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
            } else {
                if (!empty($val['PostShareUser'])) {
                    $data[$key]['share_mode'] = self::SHARE_PEOPLE;
                } else {
                    $data[$key]['share_mode'] = self::SHARE_ONLY_ME;
                }
            }
        }
        return $data;
    }

    function getShareMessages($data, bool $isPostPublished = true)
    {
        foreach ($data as $key => $val) {
            $data[$key]['share_text'] = null;
            switch ($val['share_mode']) {
                case self::SHARE_PEOPLE:
                    if (count($val['PostShareUser']) == 1) {
                        $data[$key]['share_text'] = sprintf(
                            $this->getShareMessageDefinitions('people', $isPostPublished),
                            $data[$key]['share_user_name']);
                    } else {
                        $data[$key]['share_text'] = sprintf(
                            $this->getShareMessageDefinitions('peoples', $isPostPublished),
                            $data[$key]['share_user_name'],
                            count($val['PostShareUser']) - 1);
                    }
                    break;
                case self::SHARE_ONLY_ME:
                    //自分だけ
                    $data[$key]['share_text'] = $this->getShareMessageDefinitions('self', $isPostPublished);
                    break;
                case self::SHARE_CIRCLE:
                    //共有ユーザがいない場合
                    if (count($val['PostShareUser']) == 0) {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circle', $isPostPublished),
                                $data[$key]['share_circle_name']);
                        } else {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circles', $isPostPublished),
                                $data[$key]['share_circle_name'],
                                count($val['PostShareCircle']) - 1);
                        }
                    } //共有ユーザが１人いる場合
                    elseif (count($val['PostShareUser']) == 1) {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circle_with_people', $isPostPublished),
                                $data[$key]['share_circle_name'],
                                $data[$key]['share_user_name']);
                        } else {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circles_with_people', $isPostPublished),
                                $data[$key]['share_user_name'],
                                $data[$key]['share_circle_name'],
                                count($val['PostShareCircle']) - 1);
                        }

                    } //共有ユーザが２人以上いる場合
                    else {
                        if (count($val['PostShareCircle']) == 1) {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circle_with_peoples', $isPostPublished),
                                $data[$key]['share_circle_name'],
                                $data[$key]['share_user_name'],
                                count($val['PostShareUser']) - 1);
                        } else {
                            $data[$key]['share_text'] = sprintf(
                                $this->getShareMessageDefinitions('circles_with_peoples', $isPostPublished),
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

    /**
     * Return share message by share type
     *
     * @param string $shareType
     * @param bool   $isPostPublished
     *
     * @return string
     */
    private function getShareMessageDefinitions(string $shareType, bool $isPostPublished): string
    {
        if ($isPostPublished) {
            switch ($shareType) {
                case 'people':
                    return __('Shared %s');
                case 'peoples':
                    return __('Shared %1$s and %2$s others');
                case 'self':
                    return __('Only you');
                case 'circle':
                    return __('Shared %s');
                case 'circles':
                    return __('Shared %1$s and %2$s circle(s)');
                case 'circle_with_people':
                    return __('Shared %1$s and %2$s');
                case 'circles_with_people':
                    return __('Shared %1$s, %2$s and %3$s others');
                case 'circle_with_peoples':
                    return __('Shared %1$s, %2$s and %3$s others');
                case 'circles_with_peoples':
                    return __('Shared %1$s and %2$s others,%3$s and %4$s circle(s)');
                default:
                    return 'Shared %s';
            }
        }
        // post is still in draft
        switch ($shareType) {
            case 'people':
                return '%s';
            case 'peoples':
                return __('%1$s and %2$s others');
            case 'self':
                return __('Only you');
            case 'circle':
                return '%s';
            case 'circles':
                return __('%1$s and %2$s circle(s)');
            case 'circle_with_people':
                return __('%1$s and %2$s');
            case 'circles_with_people':
                return __('%1$s, %2$s and %3$s others');
            case 'circle_with_peoples':
                return __('%1$s, %2$s and %3$s others');
            case 'circles_with_peoples':
                return __('%1$s and %2$s others,%3$s and %4$s circle(s)');
            default:
                return '%s';
        }
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

    /**
     * Set whether loginUser save favorite item each post
     *
     * @param array $data
     * @param int   $userId
     *
     * @return array
     */
    function setIsSavedItemEachPost(array $data, int $userId)
    {
        $postIds = Hash::extract($data, '{n}.Post.id');
        $isSavedEachPost = $this->SavedPost->isSavedEachPost($postIds, $userId);
        foreach ($data as $key => $val) {
            $postId = Hash::get($val, 'Post.id');
            $data[$key]['Post']['is_saved_item'] = $isSavedEachPost[$postId];
        }
        return $data;
    }

    /**
     * 投稿の編集
     *
     * @param $data
     *
     * @return bool
     */
    function postEdit($data)
    {
        $this->begin();
        $results = [];

        // 投稿データ保存
        $results[] = $this->save($data);
        //post_share_users,post_share_circlesの更新
        $results[] = $this->PostShareUser->updateAll(['PostShareUser.modified' => REQUEST_TIMESTAMP],
            ['PostShareUser.post_id' => $data['Post']['id']]);
        $results[] = $this->PostShareCircle->updateAll(['PostShareCircle.modified' => REQUEST_TIMESTAMP],
            ['PostShareCircle.post_id' => $data['Post']['id']]);

        // ファイルが添付されている場合
        if ((isset($data['file_id']) && is_array($data['file_id'])) ||
            (isset($data['deleted_file_id']) && is_array($data['deleted_file_id']))
        ) {
            $results[] = $this->PostFile->AttachedFile->updateRelatedFiles(
                $data['Post']['id'],
                AttachedFile::TYPE_MODEL_POST,
                isset($data['file_id']) ? $data['file_id'] : [],
                isset($data['deleted_file_id']) ? $data['deleted_file_id'] : []);
        }

        // Delete translations
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        $Translation->eraseAllTranslations(TranslationContentType::CIRCLE_POST(), $data['Post']['id']);
        $this->clearLanguage($data['Post']['id']);

        // どこかでエラーが発生した場合は rollback
        foreach ($results as $r) {
            if (!$r) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();

        // 添付ファイルが存在する場合は一時データを削除
        if (isset($data['file_id']) && is_array($data['file_id'])) {
            $Redis = ClassRegistry::init('GlRedis');
            foreach ($data['file_id'] as $hash) {
                if (!is_numeric($hash)) {
                    $Redis->delPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
                }
            }
        }

        return true;
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
        $post = $this->findById($post_id);
        if (empty($post)) {
            return [];
        }
        $share_member_list = [];
        //サークル共有ユーザを追加
        $share_member_list = $share_member_list + $this->PostShareCircle->getShareCircleMemberList($post_id);
        //メンバー共有なら
        $share_member_list = $share_member_list + $this->PostShareUser->getShareUserListByPost($post_id);
        //Postの主が自分ではないなら追加
        $posted_user_id = Hash::get($post, 'Post.user_id');
        if ($this->my_uid != $posted_user_id) {
            $share_member_list[] = $posted_user_id;
        }
        $share_member_list = array_unique($share_member_list);

        //自分自身を除外
        $key = array_search($this->my_uid, $share_member_list);
        if ($key !== false) {
            unset($share_member_list[$key]);
        }
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
    function addGoalPost(
        $type,
        $goal_id,
        $uid = null,
        $public = true,
        $model_id = null,
        $share = null,
        $share_type = PostShareCircle::SHARE_TYPE_SHARED
    ) {
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
        list($users, $circles) = $this->distributeShareToUserAndCircle($share);
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
     * Return list of users.id and circles.id to share
     *
     * @param array    $shares string of post targets to share
     *                         e.g. 'public,circle_1,user_2'
     * @param int|null $teamId
     *                         if null is passed, teamId is solved from $this->current_team_id
     *
     * @return array list($userIds, $circleIds)
     */
    function distributeShareToUserAndCircle(array $shares, $teamId = null): array
    {
        $users = [];
        $circles = [];
        foreach ($shares as $val) {
            if (stristr($val, 'public')) {
                $circles[] = $this->Circle->getTeamAllCircleId($teamId);
                continue;
            }
            // user case
            if (stristr($val, 'user_')) {
                $users[] = str_replace('user_', '', $val);
            } // circle case
            elseif (stristr($val, 'circle_')) {
                $circles[] = str_replace('circle_', '', $val);
            }
        }
        return [$users, $circles];
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
     * @param mixed  $userId ユーザーIDもしくは'me'を指定する。
     * @param null   $startTimestamp
     * @param null   $endTimestamp
     * @param string $date_col
     *
     * @return int
     */
    function getCount($userId = 'me', $startTimestamp = null, $endTimestamp = null, $date_col = 'created')
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
                'type'    => self::TYPE_NORMAL
            ]
        ];
        // ユーザーIDに'me'が指定された場合は、自分のIDをセットする
        if ($userId == 'me') {
            $options['conditions']['user_id'] = $this->my_uid;
        } elseif ($userId) {
            $options['conditions']['user_id'] = $userId;
        }

        //期間で絞り込む
        if ($startTimestamp) {
            $options['conditions']["$date_col >="] = $startTimestamp;
        }
        if ($endTimestamp) {
            $options['conditions']["$date_col <="] = $endTimestamp;
        }
        $res = (int)$this->find('count', $options);
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
        if (ENV_NAME == 'local') {
            $php = 'php ';
        } else {
            $php = '/opt/phpbrew/php/php-' . phpversion() . '/bin/php ';
        }
        $cake_cmd = $php . APP . "Console" . DS . "cake.php";
        $cake_app = " -app " . APP;
        $cmd = " Operation.post {$method_name}";
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
                'SUM(post_like_count) as sum_like',
            ],
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $this->current_team_id,
                'type'    => [self::TYPE_NORMAL, self::TYPE_ACTION],
            ]
        ];
        //期間で絞り込む
        if ($startTimestamp) {
            $options['conditions']['created >'] = $startTimestamp;
        }
        if ($endTimestamp) {
            $options['conditions']['created <'] = $endTimestamp;
        }
        $res = $this->find('first', $options);
        return $res ? $res[0]['sum_like'] : 0;
    }

    function getFilesOnCircle(
        $circle_id,
        $page = 1,
        $limit = null,
        $start = null,
        $end = null,
        $file_type = null
    ) {
        $one_month = 60 * 60 * 24 * 31;
        $limit = $limit ? $limit : FILE_LIST_PAGE_NUMBER;
        $start = $start ? $start : REQUEST_TIMESTAMP - $one_month;
        $end = $end ? $end : REQUEST_TIMESTAMP;

        //PostFile,CommentFile,ActionResultFileからfile_idをまず集める
        /**
         * @var AttachedFile $AttachedFile
         */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        $p_ids = $this->PostShareCircle->find('list', [
            'conditions' => [
                'circle_id'                => $circle_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'fields'     => ['post_id', 'post_id']
        ]);
        $c_ids = $this->Comment->find('list', [
            'conditions' => ['post_id' => $p_ids],
            'fields'     => ['id', 'id']
        ]);

        $p_file_ids = $AttachedFile->PostFile->find('list', [
            'conditions' => ['post_id' => $p_ids],
            'fields'     => ['attached_file_id', 'attached_file_id']
        ]);
        $c_file_ids = $AttachedFile->CommentFile->find('list', [
            'conditions' => ['comment_id' => $c_ids],
            'fields'     => ['attached_file_id', 'attached_file_id']
        ]);
        $file_ids = $p_file_ids + $c_file_ids;
        $options = [
            'conditions' => [
                'AttachedFile.id'                    => $file_ids,
                'AttachedFile.display_file_list_flg' => true,
            ],
            'order'      => ['AttachedFile.created desc'],
            'limit'      => $limit,
            'page'       => $page,
            'contain'    => [
                'User'        => [
                    'fields' => $this->User->profileFields,
                ],
                'PostFile'    => [
                    'fields' => ['PostFile.post_id']
                ],
                'CommentFile' => [
                    'fields'  => ['CommentFile.comment_id'],
                    'Comment' => [
                        'fields' => ['Comment.post_id'],
                    ]
                ],
            ]
        ];
        if ($file_type) {
            $options['conditions']['AttachedFile.file_type'] = $AttachedFile->getFileTypeId($file_type);
        }

        $files = $AttachedFile->find('all', $options);
        return $files;
    }

    /**
     * $action_result_id に紐づく投稿を取得
     *
     * @param $action_result_id
     *
     * @return array|null
     */
    public function getByActionResultId($action_result_id)
    {
        $options = [
            'conditions' => [
                'team_id'          => $this->current_team_id,
                'action_result_id' => $action_result_id,
                'type'             => self::TYPE_ACTION,
            ]
        ];
        return $this->find('first', $options);
    }

    /**
     * 投稿したユニークユーザー数を返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getUniqueUserCount($params = [])
    {
        $params = array_merge([
            'start'   => null,
            'end'     => null,
            'user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'COUNT(DISTINCT user_id) as cnt',
            ],
            'conditions' => [
                'Post.team_id' => $this->current_team_id,
                'Post.type'    => Post::TYPE_NORMAL,
            ],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["Post.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Post.created <="] = $params['end'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']["Post.user_id"] = $params['user_id'];
        }
        $row = $this->find('first', $options);

        $count = 0;
        if (isset($row[0]['cnt'])) {
            $count = $row[0]['cnt'];
        }
        return $count;
    }

    /**
     * ユーザー別の投稿数ランキングを返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getPostCountUserRanking($params = [])
    {
        $params = array_merge([
            'limit'   => null,
            'start'   => null,
            'end'     => null,
            'user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'Post.user_id',
                'COUNT(*) as cnt',
            ],
            'conditions' => [
                'Post.team_id' => $this->current_team_id,
                'Post.type'    => self::TYPE_NORMAL,
            ],
            'group'      => ['Post.user_id'],
            'order'      => ['cnt' => 'DESC'],
            'limit'      => $params['limit'],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["Post.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["Post.created <="] = $params['end'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']['Post.user_id'] = $params['user_id'];
        }
        $rows = $this->find('all', $options);
        $ranking = [];
        foreach ($rows as $v) {
            $ranking[$v['Post']['user_id']] = $v[0]['cnt'];
        }
        return $ranking;
    }

    /**
     * ID指定で複数の投稿を返す
     *
     * @param array $post_ids
     * @param array $params
     *
     * @return array|null
     */
    public function getPostsById($post_ids, $params = [])
    {
        $params = array_merge([
            'include_action' => null,
            'include_user'   => null
        ],
            $params);

        $options = [
            'conditions' => [
                'Post.team_id' => $this->current_team_id,
                'Post.id'      => $post_ids,
            ],
            'contain'    => []
        ];
        if ($params['include_action']) {
            $options['contain'][] = 'ActionResult';
        }
        if ($params['include_user']) {
            $options['contain'][] = 'User';
        }
        return $this->find('all', $options);
    }

    public function isPostedCircleForSetupBy($userId)
    {
        $user = $this->User->getById($userId);
        // for error log in https://goalous.slack.com/archives/C0LV38PC6/p1497843894088450
        // TODO: I dont't know the cause of above error. So, logging it.
        if (empty($user)) {
            $this->log(sprintf("failed to find user! targetUserId: %s, teamId: %s, loggedIn user: %s",
                $userId, $this->current_team_id, $this->my_uid));
            $this->log(Debugger::trace());
        }
        $options = [
            'conditions' => [
                'Post.user_id'    => $userId,
                'Post.type'       => self::TYPE_NORMAL,
                'Post.modified >' => $user['created']
            ],
            'fields'     => ['Post.id']
        ];

        return (bool)$this->findWithoutTeamId('all', $options);
    }

    /**
     * @override
     *
     * @param array $data
     * @param bool  $filterKey
     *
     * @return array
     */
    public function create($data = [], $filterKey = false)
    {
        parent::create($data, $filterKey);

        // Posts tables date column default value defined as '0' due to mysql partition.
        // create() method does not set modified and created column value on current timestamp.
        // If we do not overwrite value, modified and created value set to 0.
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();
        $this->data[$this->alias]['modified'] = $currentTimeStamp;
        $this->data[$this->alias]['created'] = $currentTimeStamp;

        return $this->data;
    }

    /**
     * Get the like count of a post
     *
     * @param int $postId
     *
     * @return int
     */
    public function getLikeCount(int $postId): int
    {
        $condition = [
            'conditions' => [
                'id' => $postId
            ],
            'fields'     => [
                'post_like_count'
            ]
        ];

        return $this->find('first', $condition)['Post']['post_like_count'];
    }

    /**
     * Update the comment count of a post
     *
     * @param int $postId
     * @param int $newCommentCount
     *
     * @return bool
     */
    public function updateCommentCount(int $postId, int $newCommentCount): bool
    {
        $newData = [
            'Post.comment_count' => $newCommentCount,
            'Post.modified'      => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'Post.id' => $postId
        ];

        return $this->updateAll($newData, $condition);
    }

    /**
     * Get post type
     *
     * @param int $postId
     *
     * @return int
     */
    public function getPostType(int $postId): int
    {
        $condition = [
            'conditions' => [
                'Post.id' => $postId
            ],
            'fields'     => [
                'Post.type'
            ]
        ];

        return (int)$this->find('first', $condition)['Post']['type'];
    }

    /**
     * Update language of the post
     *
     * @param int    $postId
     * @param string $language
     *
     * @throws Exception
     */
    public function updateLanguage(int $postId, string $language)
    {
        $this->id = $postId;

        $newData = [
            'language' => $language
        ];

        $this->save($newData, false);
    }

    /**
     * Delete language of a post
     *
     * @param int $postId
     *
     * @throws Exception
     */
    public function clearLanguage(int $postId)
    {
        $this->id = $postId;

        $newData = [
            'language' => null
        ];

        $this->save($newData, false);
    }

    /**
     * Get post entity by comment id
     *
     * @param int $commentId
     *
     * @return array
     */
    public function getByCommentId(int $commentId): array
    {
        $condition = [
            'table' => 'posts',
            'alias' => 'Post',
            'joins' => [
                [
                    'table'      => 'comments',
                    'alias'      => 'Comment',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Post.id = Comment.post_id',
                        'Comment.id' => $commentId,
                    ]
                ],
            ]
        ];

        return $this->useType()->find('first', $condition) ?: [];
    }
}
