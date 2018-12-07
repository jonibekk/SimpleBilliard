<?php
App::import('Service', 'AppService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('CircleMember', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/19
 */

use Goalous\Exception as GlException;

class PostReadService extends AppService
{
    /**
     * Add user read a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who who reads the post
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return PostReadEntity
     */
    public function add(int $postId, int $userId, int $teamId): PostReadEntity
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

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

        //Check whether user read that post already
        if (empty($PostRead->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $PostRead->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var PostReadEntity $result */
                $result = $PostRead->useType()->useEntity()->save($newData, false);

                $PostRead->updateReadersCount($postId);

                $this->updateCircleUnreadCount([$postId], $userId, $teamId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__("You already read this post."));
        }

        return $result;
    }

    /**
     * Add multiple
     *
     * @param array $postIds Target post's ID
     * @param int   $userId  User ID who who reads the post
     * @param int   $teamId  The team ID where this happens
     *
     * @throws Exception
     * @return array | null
     */
    public function multipleAdd(array $postIds, int $userId, int $teamId)
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $query = [
            'conditions' => [
                'PostRead.post_id' => $postIds,
                'PostRead.user_id' => $userId,
            ],
            'fields'     => 'PostRead.post_id'
        ];
        $readPosts = $PostRead->find('all', $query);

        $readPostIds = Hash::extract($readPosts, "{n}.PostRead.post_id");
        $unreadPostIds = array_diff($postIds, $readPostIds);

        if (!empty($unreadPostIds)) {
            try {
                $this->TransactionManager->begin();
                $PostRead->create();
                $newData = [];
                foreach ($unreadPostIds as $unreadPostId) {
                    $data = [
                        'post_id' => $unreadPostId,
                        'user_id' => $userId,
                        'team_id' => $teamId
                    ];
                    array_push($newData, $data);
                }

                /** @var PostReadEntity $result */
                $PostRead->useType()->useEntity()->bulkInsert($newData);

                $PostRead->updateReadersCountMultiplePost($unreadPostIds);

                $this->updateCircleUnreadCount($unreadPostIds, $userId, $teamId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        }

        return $unreadPostIds;
    }

    /**
     * Update unread count in each circle_member where the user is joined to
     *
     * @param array $postIds
     * @param int   $userId
     * @param int   $teamId
     *
     * @throws Exception
     */
    public function updateCircleUnreadCount(array $postIds, int $userId, int $teamId)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        try {
            $this->TransactionManager->begin();

            $unreadCountList = $this->countUnreadInCircles($postIds, $userId);

            foreach ($unreadCountList as $circleId => $unreadCount) {
                $CircleMember->updateUnreadCount($circleId, $unreadCount, $userId, $teamId);
            }
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to update unread count", [
                'user.id' => $userId,
                'team_id' => $teamId,
                'post.id' => $postIds
            ]);
            throw $exception;
        }
    }

    /**
     * Count unread count in each circle
     *
     * @param array $postIds
     * @param int   $userId
     *
     * @return array
     *              [circle_id => unread_count]
     */
    private function countUnreadInCircles(array $postIds, int $userId): array
    {
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $postList = $PostShareCircle->getListOfPostByPostId($postIds);

        $result = [];

        foreach ($postList as $circleId => $sharedPostList) {
            $unreadPostList = $PostRead->filterUnreadPost($postIds, $circleId, $userId, true);
            $result[$circleId] = count($unreadPostList);
        }

        return $result;
    }
}
