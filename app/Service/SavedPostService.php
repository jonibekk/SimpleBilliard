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

}
