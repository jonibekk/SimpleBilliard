<?php

App::import('Service', 'AppService');
App::import('Service', 'CircleMemberService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('PlainCircle', 'Model');
App::uses('UnreadCirclePost', 'Model');
App::uses('TeamMember', 'Model');

class UnreadCirclePostService extends AppService
{
    /**
     * Return all unread circle post information of an user in a team.
     *
     * @param int $teamId
     * @param int $userId
     *
     * @return array
     *              [circle_id => [post_id, post_id,... ]]
     */
    public function getGrouped(int $teamId, int $userId): array
    {
        $groupedPostIds = [];

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $circleMembers = $CircleMember->getCirclesWithNotificationFlg($teamId, $userId, true);

        /** @var CircleMemberEntity $circleMember */
        foreach ($circleMembers as $circleMember) {
            $circleId = $circleMember['circle_id'];
            $postIds = $UnreadCirclePost->getPostIdsInCircle($circleId, $userId);
            if (empty($postIds)) {
                continue;
            }
            $groupedPostIds[$circleMember['circle_id']] = $postIds;
        }

        return $groupedPostIds;
    }

    /**
     * Add unread entries for all member of a circle of the new post.
     *
     * @param int $teamId
     * @param int $circleId
     * @param int $postId
     * @param int $excludedUserId
     */
    public function addUnread(int $teamId, int $circleId, int $postId, int $excludedUserId)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userIds = $CircleMember->getAllMemberUserIds($circleId);
        $userIds = array_diff($userIds, [$excludedUserId]);
        $userIds = $TeamMember->filterActiveMembers($userIds, $teamId);

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        try {
            $this->TransactionManager->begin();
            $UnreadCirclePost->addMany($teamId, $circleId, $userIds, $postId);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Error in adding unread for circle members.', [
                'message'   => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
                'circle_id' => $circleId,
                'post_id'   => $postId
            ]);
        }
    }

    /**
     * Delete all unread information in all circles in a team that an user is joined to
     *
     * @param int $teamId
     * @param int $userId
     */
    public function deleteUserCacheInTeam(int $teamId, int $userId): void
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        try {
            $this->TransactionManager->begin();

            $UnreadCirclePost->deleteByTeamUser($teamId, $userId);

            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Error in deleting all user unread in a team.', [
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTraceAsString(),
                'team_id' => $teamId,
                'user_id' => $userId
            ]);
        }

    }

    /**
     * Delete all cache for a team
     *
     * @param int $teamId
     */
    public function deleteAllInTeam(int $teamId): void
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        try {
            $this->TransactionManager->begin();

            $UnreadCirclePost->deleteAllByTeam($teamId);

            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Error in deleting all unread in a team.', [
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTraceAsString(),
                'team_id' => $teamId
            ]);
        }
    }
}
