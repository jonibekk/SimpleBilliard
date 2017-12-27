<?php
App::uses('AppModel', 'Model');

/**
 * SavedPost Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
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
     * @param int      $teamId
     * @param int      $userId
     * @param int|null $cursor
     * @param int      $limit
     * @param string   $direction "old" or "new"
     *
     * @return array
     */
    function findByUserId(int $teamId, int $userId, $cursor, int $limit, string $direction = self::DIRECTION_OLD): array
    {
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
                        'Post.type'         => Post::TYPE_NORMAL,
                        'Post.del_flg'      => false,
                    ]
                ],
            ],
            'limit'      => $limit,
        ];

        if ($cursor) {
            if ($direction == self::DIRECTION_OLD) {
                $options['conditions']['SavedPost.id <'] = $cursor;
            } elseif ($direction == self::DIRECTION_NEW) {
                $options['conditions']['SavedPost.id >'] = $cursor;
            }
        }

        $res = $this->find('all', $options);
        return $res;
    }

}
