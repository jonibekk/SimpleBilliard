<?php
App::uses('AppModel', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::uses('UnreadCirclePost', 'Model');
App::import('Model/Entity', 'PostReadEntity');

use Goalous\Enum\DataType\DataType as DataType;

/**
 * PostRead Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostRead extends AppModel
{
    public $actsAs = [
        'SoftDeletable' => [
            'delete' => false,
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
            'counterScope' => ['PostRead.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    /**
     * @param            $post_list
     * @param bool|false $with_comment
     *
     * @return bool|void
     */
    public function red($post_list, $with_comment = false)
    {
        //既読投稿を除外
        $post_list = $this->pickUnreadPosts($post_list);
        //自分の投稿を除外
        $post_list = $this->pickUnMyPosts($post_list);
        $common_data = [
            'user_id' => $this->my_uid,
            'team_id' => $this->current_team_id
        ];
        $post_data = [];
        if (is_array($post_list)) {
            foreach ($post_list as $post_id) {
                $data = array_merge($common_data, ['post_id' => $post_id]);
                $post_data[] = $data;
            }
        }
        if (empty($post_data)) {
            return;
        }
        $res = false;

        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');

        try {
            $res = $this->bulkInsert($post_data, true, ['post_id']);
            foreach ($post_data as $data) {
                $UnreadCirclePostService->deletePostCache($data['post_id']);
            }
        } catch (PDOException $e) {
            // post_id と user_id が重複したデータを登録しようとした場合
            // １件ずつ登録し直して登録可能なものだけ登録する
            foreach ($post_data as $data) {
                $this->create();
                try {
                    $row = $this->save($data);
                    $res = $row ? true : false;
                    $UnreadCirclePostService->deletePostCache($data['post_id']);
                } catch (PDOException $e2) {
                    // 最低１件は例外発生するが無視する
                }
            }
        }
        if ($with_comment) {
            $this->Post->Comment->CommentRead->redAllByPostId($post_list);
        }
        return $res;
    }

    public $modelConversionTable = [
        'post_id' => DataType::INT,
        'user_id' => DataType::INT,
        'team_id' => DataType::INT,
    ];

    /**
     * Filter unread posts from list of postIds
     *
     * @param array | int $postIds
     * @param int         $userId
     * @param int         $teamId
     *
     * @return array
     */
    public function pickUnreadPosts($postIds, int $userId = null, int $teamId = null): array
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        //既読済みのリスト取得
        $options = [
            'conditions' => [
                'post_id' => $postIds,
                'user_id' => $userId,
                'team_id' => $teamId,
            ],
            'fields'     => ['post_id']
        ];
        $read = $this->find('all', $options);
        $read_list = Hash::combine($read, '{n}.PostRead.post_id', '{n}.PostRead.post_id');
        $unread_posts = [];
        if (is_array($postIds)) {
            foreach ($postIds as $post_id) {
                //既読をスキップ
                if (in_array($post_id, $read_list)) {
                    continue;
                }
                $unread_posts[$post_id] = $post_id;
            }
        } elseif (!in_array($postIds, $read_list)) {
            $unread_posts[$postIds] = $postIds;
        }
        return $unread_posts;
    }

    protected function pickUnMyPosts($post_list)
    {
        if (empty($post_list)) {
            return;
        }
        //自分以外の投稿を取得
        $options = [
            'conditions' => [
                'id'      => $post_list,
                'team_id' => $this->current_team_id,
                'NOT'     => [
                    'user_id' => $this->my_uid,
                ]
            ],
            'fields'     => ['id']
        ];
        $un_my_posts = $this->Post->find('all', $options);
        $un_my_posts = Hash::combine($un_my_posts, '{n}.Post.id', '{n}.Post.id');
        return $un_my_posts;
    }

    public function getRedUsers($post_id)
    {
        $options = [
            'conditions' => [
                'PostRead.post_id' => $post_id,
                'PostRead.team_id' => $this->current_team_id,
            ],
            'order'      => [
                'PostRead.created' => 'desc'
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * Update the count reader of the post
     *
     * @param int $postId
     *
     * @return int
     */
    public function updateReadersCount(int $postId): int
    {
        $count = $this->countPostReaders($postId);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $Post->updateAll(['Post.post_read_count' => $count], ['Post.id' => $postId]);

        return $count;
    }

    /**
     * Get actual posts readers
     *
     * @param int $postId
     *
     * @return int
     */
    public function countPostReaders(int $postId): int
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId
            ],
            'fields'     => [
                'id'
            ]
        ];

        return (int)$this->find('count', $condition);
    }

    /**
     * Update the count reader for multiple posts
     *
     * @param array $postsIds
     */
    public function updateReadersCountMultiplePost(array $postsIds)
    {
        /**
         * @var array $postsCounts
         *      [$post_id => count, ...]
         */
        $postsCounts = $this->countPostReadersMultiplePost($postsIds);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        foreach ($postsIds as $postId) {
            $count = Hash::get($postsCounts, $postId, 0);
            $Post->updateAll(['Post.post_read_count' => $count], ['Post.id' => $postId]);
        }
    }

    /**
     * Get actual posts readers from multiple posts
     *
     * @param array $postsIds
     *
     * @return array $posts_counts
     *      [$post_id => count, ...]
     */
    public function countPostReadersMultiplePost(array $postsIds): array
    {
        $condition = [
            'conditions' => [
                'post_id' => $postsIds
            ],
            'fields'     => [
                'post_id',
                'COUNT(post_id) AS sum'
            ],
            'group'      => [
                'post_id'
            ]
        ];

        $groupedCounts = $this->useReset()->find('all', $condition);

        $return = [];

        foreach ($groupedCounts as $groupedCount) {
            $postId = $groupedCount['PostRead']['post_id'];
            $count = $groupedCount['0']['sum'];
            $return[$postId] = $count;
        }

        return $return;
    }

    /**
     * Filter unread posts from given postId array
     *
     * @param array $postIds
     * @param int   $circleId
     * @param int   $userId
     * @param bool  $filterCreatedTime Only allow unread posts created after user is joined to the circle
     *
     * @return array
     */
    public function filterUnreadPost(array $postIds, int $circleId, int $userId, bool $filterCreatedTime = false): array
    {
        $condition = [
            'conditions' => [
                'Post.id'        => $postIds,
                'Post.del_flg'   => false,
            ],
            'table'      => 'posts',
            'alias'      => 'Post',
        ];

        if ($filterCreatedTime) {
            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');

            $circleMember = $CircleMember->getCircleMember($circleId, $userId);

            $createdTimeLimit = $circleMember['created'];

            $condition['conditions']['Post.created > '] = $createdTimeLimit;
        }

        $db = $this->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'PostRead.post_id' => $postIds,
                'PostRead.user_id' => $userId,
                'PostRead.del_flg' => false,
            ],
            'table'      => 'post_reads',
            'alias'      => 'PostRead',
            'fields'     => [
                'PostRead.post_id',
            ]
        ], $this);
        $subQuery = 'Post.id NOT IN (' . $subQuery . ') ';
        $subQueryExpression = $db->expression($subQuery);
        $condition['conditions'][] = $subQueryExpression;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $res = $Post->useType()->find('all', $condition);

        $result = Hash::extract($res, '{n}.Post.id');

        return $result ?: [];
    }
}
