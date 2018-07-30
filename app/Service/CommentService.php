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

use Goalous\Exception as GlException;

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
    public function checkUserAccessToComment(int $userId, int $commentId): bool
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

        $comments = $Comment->useType()->find('first', $options);

        if (empty($comments)) {
            throw new GlException\GoalousNotFoundException(__("This comment doesn't exist."));
        }

        /** @var int $postId */
        $postId = Hash::extract($comments, '{s}.post_id')[0];

        if (empty($postId)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        return $PostService->checkUserAccessToPost($userId, $postId);
    }
}