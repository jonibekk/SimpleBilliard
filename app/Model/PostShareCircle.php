<?php
App::uses('AppModel', 'Model');

/**
 * PostShareCircle Model
 *
 * @property Post   $Post
 * @property Circle $Circle
 * @property Team   $Team
 */

use Goalous\Enum\DataType\DataType as DataType;

class PostShareCircle extends AppModel
{
    //そのユーザのALLフィード、サークルページ両方に表示される
    const SHARE_TYPE_SHARED = 0;
    //そのユーザのALLフィードのみに表示される。サークルページには表示されない
    const SHARE_TYPE_ONLY_NOTIFY = 1;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post',
        'Circle',
        'Team',
    ];

    public $modelConversionTable = [
        'post_id'    => DataType::INT,
        'circle_id'  => DataType::INT,
        'team_id'    => DataType::INT,
        'share_type' => DataType::INT
    ];

    public function add($post_id, $circles, $team_id = null, $share_type = self::SHARE_TYPE_SHARED)
    {
        if (empty($circles)) {
            return false;
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [];
        foreach ($circles as $circle_id) {
            $data[] = [
                'circle_id'  => $circle_id,
                'post_id'    => $post_id,
                'team_id'    => $team_id,
                'share_type' => $share_type,
            ];
        }
        return $this->saveAll($data);
    }

    public function isMyCirclePost($postId, $userId = null, $teamId = null)
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        $my_circle_list = $this->Circle->CircleMember->getMyCircleList(null, $userId, $teamId);
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';
        $options = [
            'conditions' => [
                'post_id'   => $postId,
                'circle_id' => $my_circle_list,
                'team_id'   => $teamId,
            ],
            'fields'     => ['post_id'],
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $backupPrimaryKey;
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    /**
     * $post_id の投稿が公開サークルに共有されているか確認する
     *
     * @param      $postId
     * @param null $teamId
     *
     * @return bool 公開サークルに共有されている時 true
     */
    public function isShareWithPublicCircle($postId, $teamId = null)
    {
        $teamId = $teamId ?: $this->current_team_id;

        $options = [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
                'PostShareCircle.team_id' => $teamId,
                'Circle.public_flg'       => 1,
            ],
            'contain'    => [
                'Circle',
            ]
        ];
        $res = $this->find('first', $options);
        return $res ? true : false;
    }

    /**
     * Get list of circle IDs where the post is shared
     *
     * @param int $postId
     * @param int $teamId
     *
     * @return array
     */
    public function getShareCircleList(int $postId, int $teamId = null): array
    {
        $teamId = $teamId ?: $this->current_team_id;

        $options = [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
                'PostShareCircle.team_id' => $teamId,
                'PostShareCircle.del_flg' => false
            ],
            'fields'     => [
                'PostShareCircle.circle_id',
            ],
        ];
        $res = $this->useType()->find('all', $options);
        return Hash::extract($res, '{n}.{s}.circle_id') ?: [];
    }

    /**
     * Get list of circles and their members where the post is shared to
     *
     * @param $post_id
     *
     * @return mixed
     */
    public function getShareCirclesAndMembers($post_id)
    {
        $circle_list = $this->getShareCircleList($post_id);
        $res = $this->Circle->getCirclesAndMemberById($circle_list);
        return $res;
    }

    public function getShareCircleMemberList($post_id)
    {
        $circle_list = $this->getShareCircleList($post_id);
        $res = $this->Circle->CircleMember->getMemberList($circle_list, true);
        return $res;
    }

    /**
     * サークルの投稿数を返す
     *
     * @param int   $circle_id
     * @param array $params
     *
     * @return array|null
     */
    public function getPostCountByCircleId($circle_id, $params = [])
    {
        $params = array_merge([
            'start' => null,
            'end'   => null,
        ], $params);

        $options = [
            'conditions' => [
                'PostShareCircle.team_id'   => $this->current_team_id,
                'PostShareCircle.circle_id' => $circle_id,
            ],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["PostShareCircle.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostShareCircle.created <="] = $params['end'];
        }
        return $this->find('count', $options);
    }

    public function getFirstSharedCircleId(int $postId): int
    {
        $options = [
            'conditions' => [
                'post_id' => $postId,
            ],
        ];

        return $this->find('first', $options)['PostShareCircle']['circle_id'];
    }

    /**
     * サークルへの投稿の現在までの既読数合計を返す
     *
     * @param int   $circle_id
     * @param array $params
     *
     * @return bool|int
     */
    public function getTotalPostReadCountByCircleId($circle_id, $params = [])
    {
        $params = array_merge([
            'start' => null,
            'end'   => null,
        ], $params);

        $options = [
            'fields'     => [
                'SUM(Post.post_read_count) as cnt',
            ],
            'conditions' => [
                'PostShareCircle.team_id'   => $this->current_team_id,
                'PostShareCircle.circle_id' => $circle_id,
            ],
            'contain'    => ['Post'],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["PostShareCircle.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostShareCircle.created <="] = $params['end'];
        }
        $res = $this->find('first', $options);
        return isset($res[0]['cnt']) ? intval($res[0]['cnt']) : 0;
    }

    /**
     * サークルへの投稿の現在までのいいね数合計を返す
     *
     * @param int   $circle_id
     * @param array $params
     *
     * @return bool|int
     */
    public function getTotalPostLikeCountByCircleId($circle_id, $params = [])
    {
        $params = array_merge([
            'start' => null,
            'end'   => null,
        ], $params);

        $options = [
            'fields'     => [
                'SUM(Post.post_like_count) as cnt',
            ],
            'conditions' => [
                'PostShareCircle.team_id'   => $this->current_team_id,
                'PostShareCircle.circle_id' => $circle_id,
            ],
            'contain'    => ['Post'],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["PostShareCircle.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostShareCircle.created <="] = $params['end'];
        }
        $res = $this->find('first', $options);
        return isset($res[0]['cnt']) ? intval($res[0]['cnt']) : 0;
    }

    /**
     * サークルへの投稿にいいねしたユーザーのリストを返す
     *
     * @param int   $circle_id
     * @param array $params
     *
     * @return array
     */
    public function getLikeUserListByCircleId($circle_id, $params = [])
    {
        $params = array_merge([
            'start'        => null,
            'end'          => null,
            'like_user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'PostLike.user_id',
                'PostLike.user_id',  // key, value 両方 user_id にする
            ],
            'conditions' => [
                'PostShareCircle.team_id'   => $this->current_team_id,
                'PostShareCircle.circle_id' => $circle_id,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_likes',
                    'alias'      => 'PostLike',
                    'conditions' => [
                        'PostShareCircle.post_id = PostLike.post_id',
                        'PostLike.team_id' => $this->current_team_id,
                        'PostLike.del_flg = 0',
                    ],
                ]
            ]
        ];
        if ($params['start'] !== null) {
            $options['conditions']["PostShareCircle.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostShareCircle.created <="] = $params['end'];
        }
        if ($params['like_user_id'] !== null) {
            $options['conditions']["PostLike.user_id"] = $params['like_user_id'];
        }
        return $this->find('list', $options);
    }

    /**
     * サークルへの投稿にコメントしたユーザーのリストを返す
     *
     * @param int   $circle_id
     * @param array $params
     *
     * @return array
     */
    public function getCommentUserListByCircleId($circle_id, $params = [])
    {
        $params = array_merge([
            'start'           => null,
            'end'             => null,
            'comment_user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'Comment.user_id',
                'Comment.user_id',  // key, value 両方 user_id にする
            ],
            'conditions' => [
                'PostShareCircle.team_id'   => $this->current_team_id,
                'PostShareCircle.circle_id' => $circle_id,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'comments',
                    'alias'      => 'Comment',
                    'conditions' => [
                        'PostShareCircle.post_id = Comment.post_id',
                        'Comment.team_id' => $this->current_team_id,
                        'Comment.del_flg = 0',
                    ],
                ]
            ]
        ];
        if ($params['start'] !== null) {
            $options['conditions']["PostShareCircle.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostShareCircle.created <="] = $params['end'];
        }
        if ($params['comment_user_id'] !== null) {
            $options['conditions']["Comment.user_id"] = $params['comment_user_id'];
        }
        return $this->find('list', $options);
    }

    /**
     * Get list of post IDs in each circles
     *
     * @param array $circleIds
     *
     * @return array
     *              [circle_id => post_id[]]
     */
    public function getListOfPost(array $circleIds): array
    {
        $condition = [
            'conditions' => [
                'PostShareCircle.circle_id' => $circleIds,
                'PostShareCircle.del_flg'   => false,
            ],
            'table'      => 'post_share_circles',
            'alias'      => 'PostShareCircle',
            'fields'     => [
                'PostShareCircle.circle_id',
                'PostShareCircle.post_id'
            ]
        ];

        return $this->queryPostList($condition);
    }

    /**
     * Get list of post IDs in each circles a post is shared to
     *
     * @param array $postIds Post to check circle share destination
     *
     * @return array
     *              [circle_id => post_id[]]
     */
    public function getListOfPostByPostId(array $postIds): array
    {
        $condition = [
            'conditions' => [
                'PostShareCircle.del_flg' => false,
            ],
            'table'      => 'post_share_circles',
            'alias'      => 'PostShareCircle',
            'fields'     => [
                'PostShareCircle.post_id',
                'PostShareCircle.circle_id'
            ],
        ];

        $db = $this->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'PostShareCircle.post_id' => $postIds,
                'PostShareCircle.del_flg' => false,
            ],
            'table'      => 'post_share_circles',
            'alias'      => 'PostShareCircle',
            'fields'     => [
                'PostShareCircle.circle_id',
            ]
        ], $this);
        $subQuery = 'PostShareCircle.circle_id IN (' . $subQuery . ') ';
        $subQueryExpression = $db->expression($subQuery);
        $condition['conditions'][] = $subQueryExpression;

        return $this->queryPostList($condition);
    }

    private function queryPostList(array $condition): array
    {
        $postList = $this->useType()->find('all', $condition);

        $postList = Hash::extract($postList, '{n}.{s}');

        $result = [];

        foreach ($postList as $entry) {
            $circleId = $entry['circle_id'];
            $postId = $entry['post_id'];
            if (isset($result[$circleId]) && in_array($postId, $result[$circleId])) {
                continue;
            }
            $result[$circleId][] = $postId;
        }

        return $result ?: [];
    }
}
