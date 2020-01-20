<?php
App::import('Service', 'AppService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('UnreadCirclePost', 'Model');
App::import('Service/Redis', 'UnreadPostsRedisService');

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

                $this->updateCircleUnreadInformation($teamId, $userId, [$postId]);

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
     * @param int[] $postIds Target post's ID
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

                $this->updateCircleUnreadInformation($teamId, $userId, $unreadPostIds);

                $this->TransactionManager->commit();
            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }

            /** @var UnreadPostsRedisService $UnreadPostsRedisService */
            $UnreadPostsRedisService = ClassRegistry::init('UnreadPostsRedisService');
            $UnreadPostsRedisService->removeManyByPostIds($userId, $teamId, $unreadPostIds);
        }

        return $unreadPostIds;
    }

    /**
     * Update unread information in each circle_member where the user is joined to
     *
     * @param int   $teamId
     * @param int   $userId
     * @param int[] $unreadPostIds Array of ids of unread posts
     *
     * @throws Exception
     */
    public function updateCircleUnreadInformation(int $teamId, int $userId, array $unreadPostIds)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        try {
            $this->TransactionManager->begin();

            $groupedPostIds = $this->groupPostByCircle($teamId, $unreadPostIds);

            foreach ($groupedPostIds as $circleId => $postIds) {
                $UnreadCirclePost->deleteManyPosts($circleId, $postIds, $userId);

                $unreadCount = $UnreadCirclePost->countUserUnreadInCircle($circleId, $userId);

                $CircleMember->updateUnreadCount($circleId, $unreadCount, $userId, $teamId);
            }
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to update unread count", [
                'user.id' => $userId,
                'team_id' => $teamId,
                'post.id' => $unreadPostIds
            ]);
            throw $exception;
        }
    }

    /**
     * Group post ids to their respective circles.
     * This is to handle old spec where a post can be shared to multiple circles.
     *
     * @param int   $teamId
     * @param int[] $postIds
     *
     * @return array Grouped post ids
     *               [circle_id => [post_id, post_id, ...]]
     */
    private function groupPostByCircle(int $teamId, array $postIds): array
    {
        $groupedPostIds = [];

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        foreach ($postIds as $postId) {
            $circleIds = $PostShareCircle->getShareCircleList($postId, $teamId);

            foreach ($circleIds as $circleId) {
                $groupedPostIds[$circleId][] = $postId;
            }
        }

        return $groupedPostIds;
    }
}
