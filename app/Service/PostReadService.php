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
     * Read specified post ids
     *
     * @param array $postIds
     * @param int $userId
     * @param int $teamId
     */
    public function readPosts(array $postIds, int $userId, int $teamId)
    {
        $pdo = $this->createPdoConnection();
        $postIdsToRead = [];
        try {
            $pdo->beginTransaction();

            // Create string of ?,?,?,? ...
            $postIdsIn = implode(',', array_fill(0, count($postIds), '?'));
            $stateSelectRead = $pdo->prepare('select post_id from post_reads where post_id in ('.$postIdsIn.') and user_id = ?');
            $stateSelectRead->execute(array_merge($postIds, [$userId]));
            $alreadyReadPostIds = $stateSelectRead->fetchAll(PDO::FETCH_COLUMN);
            //var_dump($alreadyReadPostIds);
            $postIdsToRead = array_diff($postIds, $alreadyReadPostIds);
//            GoalousLog::info('post id read filtering', [
//                '$postIdsToRead' => $postIdsToRead,
//                '$alreadyReadPostIds' => $alreadyReadPostIds,
//                '$postIds' => $postIds,
//            ]);

            // for each

            //// insert post read (Move to the end)
            $valuesString = implode(',', array_fill(0, count($postIdsToRead), '(?, ?, ?, unix_timestamp(), unix_timestamp())'));
            $query = sprintf('insert into post_reads (post_id, user_id, team_id, created, modified) values %s', $valuesString);
            $stateInsertPostRead = $pdo->prepare($query);
            $place = 1;
            foreach ($postIdsToRead as $postIdToRead) {
                $stateInsertPostRead->bindValue($place++, $postIdToRead, PDO::PARAM_INT);
                $stateInsertPostRead->bindValue($place++, $userId, PDO::PARAM_INT);
                $stateInsertPostRead->bindValue($place++, $teamId, PDO::PARAM_INT);
            }
            $insertResult = $stateInsertPostRead->execute();
//            GoalousLog::info('$insertResult', [$insertResult]);

            // select circle id that will influence
            $valuesString = implode(',', array_fill(0, count($postIdsToRead), '?'));
            $query = sprintf('
            select distinct S.circle_id 
            from post_reads as R 
            inner join post_share_circles as S 
                on R.post_id = S.post_id 
            where 
                R.user_id = ? 
                and R.post_id in (%s)
            ', $valuesString);
            $stateSelectCircleIdOfUpdating = $pdo->prepare($query);
            $stateSelectCircleIdOfUpdating->execute(array_merge([$userId], $postIdsToRead));
            $circleIdsUpdating = $stateSelectCircleIdOfUpdating->fetchAll(PDO::FETCH_COLUMN);
//            GoalousLog::info('$circleIdsUpdating', $circleIdsUpdating);

            //// remove post read cache
            $valuesString = implode(',', array_fill(0, count($postIdsToRead), '?'));
            $query = sprintf('delete from cache_unread_circle_posts where user_id = ? and team_id = ? and post_id in (%s);', $valuesString);
            $stateDeleteCacheUnread = $pdo->prepare($query);
            $stateDeleteCacheUnread->execute(array_merge([$userId], [$teamId], $postIdsToRead));

            //// count unread count for each circle
            ////// fetch count of unread each circle

            $valuesString = implode(',', array_fill(0, count($circleIdsUpdating), '?'));
//            $query = sprintf('
//         select
//            circle_id, count(circle_id) as count
//         from post_reads as R
//         inner join post_share_circles as S
//              on R.post_id = S.post_id
//         where R.user_id = ?
//              and circle_id in (%s)
//         group by circle_id;', $valuesString);
//            $stateSelectCircle = $pdo->prepare($query);

            $valuesString = implode(',', array_fill(0, count($circleIdsUpdating), '?'));
            $query = sprintf('
                select 
                    C.circle_id, count(C.circle_id) as count 
                from cache_unread_circle_posts as U 
                inner join post_share_circles as C 
                    on U.post_id = C.post_id 
                where U.user_id = ? 
                    and U.team_id = ? 
                    and C.circle_id in (%s)
                group by C.circle_id;', $valuesString);
            $stateSelectRestOfUnreadCount = $pdo->prepare($query);
            $stateSelectRestOfUnreadCount->execute(array_merge([$userId], [$teamId], $circleIdsUpdating));
            $circleUnreadCounts = $stateSelectRestOfUnreadCount->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($circleIdsUpdating);
            //var_dump($circleUnreadCounts);

            $mapKeyCircleIdValueUnreadCount = [];
            foreach ($circleIdsUpdating as $circleId) {
                $mapKeyCircleIdValueUnreadCount[$circleId] = 0;
            }
            foreach ($circleUnreadCounts as $circleUnreadCount) {
                $circleId = intval($circleUnreadCount['circle_id']);
                $mapKeyCircleIdValueUnreadCount[$circleId] = intval($circleUnreadCount['count']);
            }

            // Update circle member unread count
            $stateUpdateCircleMemberUnreadCount = $pdo->prepare('
                update circle_members 
                set unread_count = ? 
                where 
                    circle_id = ? 
                    and team_id = ? 
                    and user_id = ?;');
            GoalousLog::info('$postIdsToRead', $postIdsToRead);
            GoalousLog::info('$circleUnreadCounts', $circleUnreadCounts);
            GoalousLog::info('$mapKeyCircleIdValueUnreadCount', [$mapKeyCircleIdValueUnreadCount]);
            foreach ($mapKeyCircleIdValueUnreadCount as $circleId => $unreadCount) {
                $stateUpdateCircleMemberUnreadCount->execute([
                    $unreadCount,
                    $circleId,
                    $teamId,
                    $userId
                ]);
            }

            // Update post read count
            $stateUpdatePostReadCount = $pdo->prepare('
                update posts
                set post_read_count = (select count(id) from post_reads where post_id = :post_id)
                where id = :post_id;');
            foreach ($postIdsToRead as $postId) {
                $stateUpdatePostReadCount->bindValue(':post_id', $postId);
                $stateUpdatePostReadCount->execute();
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            GoalousLog::error('pdo catch error', [
                'message' => $e->getMessage(),
            ]);
            $pdo->rollBack();
        }

        return $postIdsToRead;
    }

    private function createPdoConnection()
    {
        $db = new DATABASE_CONFIG();
        return new PDO('mysql:host='.$db->default['host'].';dbname='.
            $db->default['database'].';charset=utf8',
            $db->default['login'], $db->default['password']);
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
        GoalousLog::info('$postIds', $postIds);

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

        GoalousLog::info('$readPostIds', $readPostIds);
        GoalousLog::info('$unreadPostIds', $unreadPostIds);

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

            GoalousLog::info(__FILE__, [
                'teams.id' => $teamId,
                'users.id' => $userId,
                '$unreadPostIds' => $unreadPostIds,
                '$groupedPostIds' => $groupedPostIds,
            ]);

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
