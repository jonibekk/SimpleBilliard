<?php
App::import('Service', 'AppService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('UnreadCirclePost', 'Model');
App::import('Service/Redis', 'UnreadPostsRedisService');

use Goalous\Exception as GlException;

class PostReadService extends AppService
{
    /**
     * Function of post read.
     * Doing 4 things.
     * - Delete record from cache_unread_circle_posts table
     * - Decreasing circle_members.unread_count
     * - Insert post_reads table
     * - Increasing posts.post_read_count
     * To make reading post process more atomic, this function is not using CakePHP2 model class.
     *
     * @param array $postIds
     * @param int $userId
     * @param int $teamId
     * @return array
     */
    public function readPosts(array $postIds, int $userId, int $teamId)
    {
        $pdo = $this->createPdoConnection();
        $postIdsToRead = [];
        try {
            $pdo->beginTransaction();

            $rawString = function (int $count, string $str) {
                return implode(',', array_fill(0, $count, $str));
            };

            // Select post_id that have already read.
            $query = sprintf(
                'select post_id from post_reads where post_id in (%s) and user_id = ?',
                $rawString(count($postIds), '?')
            );
            $stateSelectRead = $pdo->prepare($query);
            $stateSelectRead->execute(array_merge($postIds, [$userId]));
            $alreadyReadPostIds = $stateSelectRead->fetchAll(PDO::FETCH_COLUMN);
            $postIdsToRead = array_diff($postIds, $alreadyReadPostIds);
            if (empty($postIdsToRead)) {
                return [];
            }

            // Select circle id that will influence
            $query = sprintf('
            select distinct S.circle_id 
            from posts as P 
            inner join post_share_circles as S 
                on P.id = S.post_id 
            where P.id in (%s)
            ', $rawString(count($postIdsToRead), '?'));
            $stateSelectCircleIdOfUpdating = $pdo->prepare($query);
            $stateSelectCircleIdOfUpdating->execute($postIdsToRead);
            $circleIdsUpdating = $stateSelectCircleIdOfUpdating->fetchAll(PDO::FETCH_COLUMN);

            // Select from cache_unread_circle_posts existing
            $query = sprintf(
                'select post_id from cache_unread_circle_posts where user_id = ? and team_id = ? and post_id in (%s);',
                $rawString(count($postIdsToRead), '?')
            );
            $stateSelectRead = $pdo->prepare($query);
            $stateSelectRead->execute(array_merge([$userId, $teamId], $postIdsToRead));
            $postIdsCached = $stateSelectRead->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($postIdsCached)) {
                // Delete record from cache_unread_circle_posts table
                $query = sprintf(
                    'delete from cache_unread_circle_posts where user_id = ? and team_id = ? and post_id in (%s);',
                    $rawString(count($postIdsCached), '?')
                );
                $stateDeleteCacheUnread = $pdo->prepare($query);
                $stateDeleteCacheUnread->execute(array_merge([$userId], [$teamId], $postIdsCached));
                $countDeletedCache = $stateDeleteCacheUnread->rowCount();
                if ($countDeletedCache !== count($postIdsCached)) {
                    throw new RuntimeException(sprintf(
                        'Unexpected cache_unread_circle_posts record deleted amount. expected : %d, actual: %d',
                        count($postIdsCached),
                        $countDeletedCache
                    ));
                }
            }

            if ($circleIdsUpdating) {
                // Get the unread count in updating circles (Counting from cache_unread_circle_posts table)
                $query = sprintf('
                select 
                    C.circle_id, count(C.circle_id) as count 
                from cache_unread_circle_posts as U 
                inner join post_share_circles as C 
                    on U.post_id = C.post_id 
                where U.user_id = ? 
                    and U.team_id = ? 
                    and C.circle_id in (%s)
                group by C.circle_id;',
                    $rawString(count($circleIdsUpdating), '?')
                );
                $stateSelectRestOfUnreadCount = $pdo->prepare($query);
                $stateSelectRestOfUnreadCount->execute(array_merge([$userId], [$teamId], $circleIdsUpdating));
                $circleUnreadCounts = $stateSelectRestOfUnreadCount->fetchAll(PDO::FETCH_ASSOC);

                // Prepare array of circle id with unread count valued.
                $mapKeyCircleIdValueUnreadCount = [
                    // 'circles.id' => unread count
                ];
                // Because the mysql count() doesn't return 0 if there is no record, set default value as 0.
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
                foreach ($mapKeyCircleIdValueUnreadCount as $circleId => $unreadCount) {
                    $stateUpdateCircleMemberUnreadCount->execute([
                        $unreadCount,
                        $circleId,
                        $teamId,
                        $userId
                    ]);
                }
            }

            // Insert post read
            $query = sprintf(
                'insert into post_reads (post_id, user_id, team_id, created, modified) values %s',
                $rawString(count($postIdsToRead), '(?, ?, ?, unix_timestamp(), unix_timestamp())')
            );
            $stateInsertPostRead = $pdo->prepare($query);
            $place = 1;
            foreach ($postIdsToRead as $postIdToRead) {
                $stateInsertPostRead->bindValue($place++, $postIdToRead, PDO::PARAM_INT);
                $stateInsertPostRead->bindValue($place++, $userId, PDO::PARAM_INT);
                $stateInsertPostRead->bindValue($place++, $teamId, PDO::PARAM_INT);
            }
            $stateInsertPostRead->execute();
            $countInsertRecord = $stateInsertPostRead->rowCount();
            if (count($postIdsToRead) !== $stateInsertPostRead->rowCount()) {
                throw new RuntimeException(sprintf(
                    'Unexpected post_reads record insert amount. expected: %d, actual: %d',
                    count($postIdsToRead),
                    $countInsertRecord
                ));
            }

            // Update post read count of post
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
            $pdo->rollBack();
            GoalousLog::error('Failed read post', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'post.ids' => $postIds,
                'users.id' => $userId,
                'teams.id' => $teamId
            ]);
            throw new RuntimeException('Failed read post');
        }

        return $postIdsToRead;
    }

    private function createPdoConnection()
    {
        $db = new DATABASE_CONFIG();
        $pdo = new PDO('mysql:host='.$db->default['host'].';dbname='.
            $db->default['database'].';charset=utf8',
            $db->default['login'], $db->default['password']);
        // Make throwing exception when pdo failed on query.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * Add multiple
     * @deprecated use readPosts()
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
        return $this->readPosts($postIds, $userId, $teamId);
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
