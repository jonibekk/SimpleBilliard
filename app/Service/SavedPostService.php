<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Topic', 'Model');
App::uses('SavedPost', 'Model');
App::uses('TopicMember', 'Model');
App::uses('User', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('Topic', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Class SavedPostService
 */
class SavedPostService extends AppService
{
    /**
     * Find saved posts.
     *
     * @param int $teamId
     * @param int $userId
     * @param array $conditions
     * @param             $cursor
     * @param             $limit
     *
     * @return array
     */
    function search(int $teamId, int $userId, array $conditions, $cursor, $limit): array
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        $posts = $SavedPost->search($teamId, $userId, $conditions, $cursor, $limit);
        return $posts;
    }

    /**
     * Gett count info each type (all, actions, normal posts)
     * @param int $teamId
     * @param int $userId
     *
     * @return array
     */
    function countSavedPostEachType(int $teamId, int $userId): array
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        $normalPostCount = $SavedPost->countByType($teamId, $userId, Post::TYPE_NORMAL);
        $actionPostCount = $SavedPost->countByType($teamId, $userId, Post::TYPE_ACTION);
        return [
            'normal' => $normalPostCount,
            'action' => $actionPostCount,
            'all' => $normalPostCount + $actionPostCount
        ];
    }

    /**
     * Delete all saved post for posts in given circle
     *
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     *
     * @return bool TRUE on successful deletion
     * @throws Exception
     */
    public function deleteAllInCircle(int $userId, int $teamId, int $circleId): bool
    {
        $condition = [
            'conditions' => [
                'SavedPost.user_id' => $userId
            ],
            'table' => 'saved_posts',
            'alias' => 'SavedPost',
            'joins' => [
                [
                    'type' => 'INNER',
                    'table' => 'post_share_circles',
                    'alias' => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle.post_id = SavedPost.post_id',
                        'PostShareCircle.team_id' => $teamId,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
        ];

        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        try {
            $this->TransactionManager->begin();

            $res = $SavedPost->deleteAll($condition);

            if (!$res) {
                throw new RuntimeException("Failed to delete saved post for user $userId in circle $circleId");
            }
        } catch (Exception $exception) {
            GoalousLog::error("Failed to delete saved post for user $userId in circle $circleId", $exception->getTrace());
            $this->TransactionManager->rollback();
            throw $exception;
        }
    }
}
