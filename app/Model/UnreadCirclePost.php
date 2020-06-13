<?php

App::uses('AppModel', 'Model');
App::import('Model/Entity', 'UnreadCirclePostEntity');

use Goalous\Enum\DataType\DataType as DataType;

class UnreadCirclePost extends AppModel
{
    /**
     * Manual setting of table name
     *
     * @var string
     */
    public $useTable = "cache_unread_circle_posts";

    public $modelConversionTable = [
        'circle_id' => DataType::INT,
        'post_id'   => DataType::INT,
        'user_id'   => DataType::INT,

    ];

    /**
     * Calculate number of entry of unread posts by an user in a circle
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return int
     */
    public function countUserUnreadInCircle(int $circleId, int $userId): int
    {
        $option = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId
            ]
        ];

        return (int)$this->find('count', $option) ?? 0;
    }

    /**
     * Calculate number of entry of unread posts by an user in a team
     *
     * @param int $teamId
     * @param int $userId
     *
     * @return int
     */
    public function countUserUnreadInTeam(int $teamId, int $userId): int
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId
            ]
        ];

        return (int)$this->find('count', $option) ?? 0;
    }

    /**
     * Calculate number of users who have not read a post
     *
     * @param int $circleId
     * @param int $postId
     *
     * @return int
     */
    public function countPostUnread(int $circleId, int $postId): int
    {
        $option = [
            'conditions' => [
                'circle_id' => $circleId,
                'post_id'   => $postId
            ]
        ];

        return (int)$this->find('count', $option) ?? 0;
    }

    /**
     * Get list of post ids of user in a circle
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return int[]
     */
    public function getPostIdsInCircle(int $circleId, int $userId): array
    {
        $option = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId
            ],
            'fields'     => [
                'post_id'
            ]
        ];

        $result = $this->useType()->find('all', $option);
        return Hash::extract($result, '{n}.UnreadCirclePost.post_id');
    }

    /**
     * Get all cache of a post
     *
     * @param int $postId
     *
     * @return UnreadCirclePostEntity[]
     */
    public function getPostCache(int $postId): array
    {
        $option = [
            'conditions' => [
                'post_id' => $postId,
            ],
        ];

        return $this->useType()->useEntity()->find('all', $option);
    }

    /**
     * @param int $teamId
     * @param int $userId
     * @return array
     */
    public function getPostsCacheByTeamIdAndUserId(int $teamId, int $userId): array
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId,
            ],
            'limit'      => 8,
            'order'      => [
                'id' => 'desc'
            ],
        ];

        return $this->find('all', $option);
    }

    /**
     * Add single new unread information
     *
     * @param int $teamId
     * @param int $circleId
     * @param int $postId
     * @param int $userId
     *
     * @throws Exception
     */
    public function add(int $teamId, int $circleId, int $userId, int $postId): void
    {
        $this->addMany($teamId, $circleId, [$userId], $postId);
    }

    /**
     * Add unread post for many users
     *
     * @param int   $teamId
     * @param int   $circleId
     * @param int[] $userIds
     * @param int   $postId
     */
    public function addMany(int $teamId, int $circleId, array $userIds, int $postId): void
    {
        $newData = [];
        $timestamp = GoalousDateTime::now()->getTimestamp();

        foreach ($userIds as $userId) {
            $newData[] = [
                'team_id'   => $teamId,
                'circle_id' => $circleId,
                'user_id'   => $userId,
                'post_id'   => $postId,
                'created'   => $timestamp
            ];
        }

        if (!empty($newData)) {
            $this->saveMany($newData, ['validate' => false]);
        }
    }

    /**
     * Delete single entry
     *
     * @param int $circleId
     * @param int $postId
     * @param int $userId
     */
    public function deleteSinglePost(int $circleId, int $postId, int $userId): void
    {
        $this->deleteManyPosts($circleId, [$postId], $userId);
    }

    /**
     * Delete single entry
     *
     * @param int   $circleId
     * @param int[] $postIds
     * @param int   $userId
     */
    public function deleteManyPosts(int $circleId, array $postIds, int $userId): void
    {
        $condition = [
            'circle_id' => $circleId,
            'post_id'   => $postIds,
            'user_id'   => $userId
        ];

        $this->deleteAll($condition);
    }

    /**
     * Delete unread information of an user in a circle
     *
     * @param int $circleId
     * @param int $userId
     */
    public function deleteCircleUser(int $circleId, int $userId): void
    {
        $condition = [
            'circle_id' => $circleId,
            'user_id'   => $userId
        ];

        $this->deleteAll($condition);
    }

    /**
     * Delete unread information of an user in a team
     *
     * @param int $teamId
     * @param int $userId
     */
    public function deleteByTeamUser(int $teamId, int $userId): void
    {
        $condition = [
            'team_id' => $teamId,
            'user_id' => $userId
        ];

        $this->deleteAll($condition);
    }

    /**
     * Delete all unread information of a post in a circle
     *
     * @param int $postId
     */
    public function deleteAllByPost(int $postId): void
    {
        $condition = [
            'post_id' => $postId
        ];

        $this->deleteAll($condition);
    }

    /**
     * Delete all unread information of a circle
     *
     * @param int $circleId
     */
    public function deleteAllByCircle(int $circleId): void
    {
        $this->deleteAll(['circle_id' => $circleId]);
    }

    /**
     * Delete all unread information of multiple circles
     *
     * @param int $teamId
     */
    public function deleteAllByTeam(int $teamId): void
    {
        $this->deleteAll(['team_id' => $teamId]);
    }
}
