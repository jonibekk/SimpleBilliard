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
App::uses('Post', 'Model');

/**
 * Class SavedPostService
 */

use Goalous\Exception as GlException;

class SavedPostService extends AppService
{
    /**
     * Find saved posts.
     *
     * @param int         $teamId
     * @param int         $userId
     * @param array       $conditions
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
     *
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
            'all'    => $normalPostCount + $actionPostCount
        ];
    }

    /**
     * Save post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who added the like
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return SavedPostEntity
     */
    public function add(int $postId, int $userId, int $teamId): SavedPostEntity
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId
            ],
            'fields'     => [
                'id'
            ]
        ];

        //Check whether save is already exist from the user
        if (empty($SavedPost->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $SavedPost->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var SavedPostEntity $result */
                $result = $SavedPost->useType()->useEntity()->save($newData, false);

                $this->TransactionManager->commit();
                return $result;
            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__('This item is already saved.'));
        }
    }

    /**
     * Delete a saved post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who removed the saved post
     *
     * @return bool
     * @throws Exception
     */
    public function delete(int $postId, int $userId): bool
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId
            ],
            'fields'     => [
                'id',
            ]
        ];

        $existing = $SavedPost->find('first', $condition);

        if (!empty($existing)) {
            try {
                $this->TransactionManager->begin();
                $SavedPost->delete($existing['SavedPost']['id']);
                $this->TransactionManager->commit();
                return true;
            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousNotFoundException(__("This item doesn't exist."));
        }
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
            'fields'     => [
                'SavedPost.id'
            ],
            'table'      => 'saved_posts',
            'alias'      => 'SavedPost',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle.post_id = SavedPost.post_id',
                        'PostShareCircle.team_id'   => $teamId,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
        ];

        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        try {
            $this->TransactionManager->begin();

            $resultFind = $SavedPost->find('all', $condition);
            $savedPostIds = Hash::extract($resultFind, '{n}.SavedPost.id');
            $resultDelete = $SavedPost->deleteAll(['id' => $savedPostIds]);

            if (!$resultDelete) {
                throw new RuntimeException("Failed to delete saved post for user $userId in circle $circleId");
            }
            $this->TransactionManager->commit();
            return true;
        } catch (Exception $exception) {
            GoalousLog::error("Failed to delete saved post for user $userId in circle $circleId", $exception->getTrace());
            $this->TransactionManager->rollback();
            throw $exception;
        }
    }
}
