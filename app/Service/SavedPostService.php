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
     * @param             $cursor
     * @param             $limit
     *
     * @return array
     */
    function findByUserId(int $teamId, int $userId, $cursor, $limit): array
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        $posts = $SavedPost->findByUserId($teamId, $userId, $cursor, $limit);
        return $posts;
    }
}
