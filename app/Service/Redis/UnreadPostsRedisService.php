<?php
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsData');
App::uses('CircleMember', 'Model');

class UnreadPostsRedisService
{
    /**
     * Add circle badge status to all users in the same circle as author
     *
     * @param int $circleId
     * @param int $postId
     * @param int $postAuthorUserId
     */
    public function addToAllCircleMembers(int $circleId, int $postId, int $postAuthorUserId)
    {
        $UnreadPostsClient = new UnreadPostsClient();
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $members = $CircleMember->getMembersWithNotificationFlg($circleId, true);

        foreach ($members as $member) {
            if ($member['user_id'] === $postAuthorUserId) {
                continue;
            }
            $UnreadPostsKey = new UnreadPostsKey($member['user_id'], $member['team_id']);
            $unreadData = $UnreadPostsClient->read($UnreadPostsKey);
            $unreadData->add($circleId, $postId);
            $UnreadPostsClient->write($UnreadPostsKey, $unreadData);
        }
    }

    /**
     * Remove all unreads in a circle
     *
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     */
    public function removeManyByCircleId(int $userId, int $teamId, int $circleId)
    {
        $unreadPostsKey = new UnreadPostsKey($userId, $teamId);
        $unreadPostsClient = new UnreadPostsClient();

        $data = $unreadPostsClient->read($unreadPostsKey);

        if (empty($data->get())) {
            return;
        }

        $data->removeByCircleId($circleId);

        $unreadPostsClient->write($unreadPostsKey, $data);
    }

    /**
     * Remove multiple unreads by post ids
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $postIds
     */
    public function removeManyByPostIds(int $userId, int $teamId, array $postIds)
    {
        $unreadPostsKey = new UnreadPostsKey($userId, $teamId);
        $unreadPostsClient = new UnreadPostsClient();

        $data = $unreadPostsClient->read($unreadPostsKey);

        if (empty($data->get())) {
            return;
        }

        $data->removeByPostIds($postIds);

        $unreadPostsClient->write($unreadPostsKey, $data);
    }

}