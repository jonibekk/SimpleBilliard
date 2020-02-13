<?php
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsData');
App::uses('CircleMember', 'Model');

/**
 * Class for managing cache about unread post data
 *
 * @deprecated Use UnreadCirclePost instead.
 *
 * Class UnreadPostsRedisService
 */
class UnreadPostsRedisService
{
    /**
     * Remove all unreads in a circle
     *
     * @deprecated Use UnreadCirclePost instead.
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
     * @deprecated Use UnreadCirclePost instead.
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
