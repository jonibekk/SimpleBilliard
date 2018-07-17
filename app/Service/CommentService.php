<?php
App::import('Service', 'AppService');
App::import('Service', 'PostService');
App::uses('Comment', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/17
 * Time: 16:50
 */
class CommentService extends AppService
{
    /**
     * Check whether user has access to the post where the comment belongs in
     *
     * @param int $userId
     * @param int $commentId
     *
     * @return bool
     */
    public function checkUserHasAccessToPost(int $userId, int $commentId): bool
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $options = [
            'conditions' => [
                'id' => $commentId
            ],
            'fields'     => [
                'post_id'
            ]
        ];

        /** @var int $postId */
        $postId = Hash::extract($Comment->useType()->find('first', $options), '{s}.post_id')[0];

        if (empty($postId)) {
            throw new RuntimeException("Post ID can't be empty");
        }

        return $PostService->checkUserAccessToPost($userId, $postId);
    }
}