<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'SavedPostEntity');

/**
 * SavedPost Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */

use Goalous\Enum\DataType\DataType as DataType;

class SavedPost extends AppModel
{
    const DIRECTION_OLD = "old";
    const DIRECTION_NEW = "new";

    public $actsAs = [
        'SoftDeletable' => [
            'delete' => false,
        ],
    ];

    /**
     * Check whether argument user saved item each target post
     *
     * @param array $postIds
     * @param int   $userId
     *
     * @return array
     * Example
     *  Precondition: user(id:1) has already saved post(id:2)
     *  Argument $postIds = [1,2,5]
     *  Result: [1 => false, 2 => true, 5 => false]
     */
    public function isSavedEachPost(array $postIds, int $userId): array
    {
        if (empty($postIds)) {
            return [];
        }

        $options = [
            'fields'     => 'post_id',
            'conditions' => [
                'post_id' => $postIds,
                'user_id' => $userId,
            ],
        ];
        $res = $this->find('all', $options);

        $default = array_fill_keys($postIds, false);
        if (empty($res)) {
            return $default;
        }

        $res = array_fill_keys(Hash::extract($res, '{n}.SavedPost.post_id'), true);
        return $res + $default;
    }

    /**
     * @param int $postId
     * @param int $userId
     *
     * @return array|null
     */
    public function getUnique(int $postId, int $userId)
    {
        $options = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
            ],
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'SavedPost');
    }

    /**
     * Find saved posts for paging
     * Except automatic post(eg. 「The goal/circle ** was created.」)
     *
     * @param int    $teamId
     * @param int    $userId
     * @param array  $conditions
     * @param int    $cursor
     * @param int    $limit
     * @param string $direction "old" or "new"
     *
     * @return array
     */
    function search(
        int $teamId,
        int $userId,
        array $conditions,
        int $cursor,
        int $limit,
        string $direction = self::DIRECTION_OLD
    ): array {
        if (empty($conditions['type'])) {
            $postTypes = [Post::TYPE_NORMAL, Post::TYPE_ACTION];
        } else {
            $postTypes = $conditions['type'];
        }

        $options = [
            'conditions' => [
                'SavedPost.user_id' => $userId,
                'SavedPost.team_id' => $teamId,
            ],
            'fields'     => [
                'SavedPost.id',
                'SavedPost.post_id',
                'SavedPost.created',
                'Post.id',
                'Post.user_id',
                'Post.type',
                'Post.body',
                'Post.site_info',
                'Post.site_photo_file_name',
                'ActionResult.id',
                'ActionResult.user_id',
                'ActionResult.name',
                'ActionResult.goal_id',
                'ActionResult.key_result_id',
            ],
            'order'      => [
                'SavedPost.id' => 'DESC'
            ],
            'joins'      => [
                [
                    'table'      => 'posts',
                    'alias'      => 'Post',
                    'type'       => 'INNER',
                    'conditions' => [
                        'SavedPost.team_id' => $teamId,
                        'SavedPost.post_id = Post.id',
                        'Post.type'         => $postTypes,
                        'Post.del_flg'      => false,
                    ]
                ],
                [
                    'table'      => 'action_results',
                    'alias'      => 'ActionResult',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Post.action_result_id = ActionResult.id',
                        'ActionResult.del_flg' => false,
                    ]
                ],
            ],
            'limit'      => $limit,
        ];

        if ($cursor > 0) {
            if ($direction == self::DIRECTION_OLD) {
                $options['conditions']['SavedPost.id <'] = $cursor;
            } elseif ($direction == self::DIRECTION_NEW) {
                $options['conditions']['SavedPost.id >'] = $cursor;
            }
        }

        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * Count saved posts for paging
     * Except automatic post(eg. 「The goal/circle ** was created.」)
     *
     * @param int $teamId
     * @param int $userId
     * @param int $type
     *
     * @return int
     */
    function countByType(int $teamId, int $userId, int $type): int
    {
        $options = [
            'conditions' => [
                'SavedPost.user_id' => $userId,
                'SavedPost.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'table'      => 'posts',
                    'alias'      => 'Post',
                    'type'       => 'INNER',
                    'conditions' => [
                        'SavedPost.team_id' => $teamId,
                        'SavedPost.post_id = Post.id',
                        'Post.type'         => $type,
                        'Post.del_flg'      => false,
                    ]
                ],
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * Delete all circle posts
     * Except automatic post(eg. 「The goal/circle ** was created.」)
     *
     * @param int $teamId
     * @param int $circleId
     * @param int $userId
     *
     * @return bool
     */
    function deleteAllCirclePosts(int $teamId, int $circleId, int $userId): bool
    {
        $sql =
<<<SQL
    DELETE sp FROM saved_posts sp
    INNER JOIN post_share_circles psc ON
    sp.post_id = psc.post_id
    AND psc.circle_id = $circleId
    AND psc.team_id = $teamId
    WHERE sp.user_id = $userId;
SQL;
        $res = $this->query($sql);
        return $res !== false;
    }

    /**
     * Get User information who save that post
     * @param int $postId
     * @param int $userId
     *
     * @return SavedPostEntity
     */
    public function getUserSavedPost(int $postId, int $user_id){
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        $options = [
            'fields'     => 'post_id',
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $user_id,
            ],
        ];
        $res = $SavedPost->useType()->useEntity()->find('all', $options);

        return $res;
    }

    public $modelConversionTable = [
        'user_id' => DataType::INT,
        'post_id' => DataType::INT,
        'team_id' => DataType::INT
    ];
}
