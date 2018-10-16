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

    /**
     * Check whether the user can view the several comments
     *
     * @param int  $userId
     * @param int  $commentsIds
     *
     * @throws Exception
     */
    public function checkUserAccessToMultipleComment(int $userId, array $commentsIds)
    {
         /** @var Comment $Comment */
         $Comment = ClassRegistry::init('Comment');

         /** @var PostService $PostService */
         $PostService = ClassRegistry::init('PostService');
 
         $options = [
             'conditions' => [
                 'id' => $commentsIds
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
        $postsIds = Hash::extract($comments, '{s}.post_id');

        if (empty($postsIds)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        return $PostService->checkUserAccessToMultiplePost($userId, $postsIds);
    }

    /**
     * Method to save a comment
     *
     * @param array   $commentBody = ['body' => '',
     *                                'post_id' => '',
     *                                ];
     * @param int     $userId
     * @param string[]  $fileIDs
     *
     * @return CommentEntity of saved comment
     * @throws Execption
     */
    public function addComment(
        array $commentBody, 
        int $userId, 
        array $fileIDs = []
    ): CommentEntity{
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        if (empty($commentBody['body'])) {
            GoalousLog::error('Error on adding post: Invalid argument', [
                'users.id'      => $userId,
                'post_id'       => $postId,
                'commentData'   => $commentBody
            ]);
            throw new InvalidArgumentException('Error on adding comment: Invalid argument');
        } 

        try {
            $this->TransactionManager->begin();
            $Comment->create();

            $commentBody['user_id'] = $userId;

            /** @var CommentEntity $savedComment */
            $savedComment = $Comment->useType()->useEntity()->save($commentBody, false);

            if (empty($savedComment)) {
                GoalousLog::error('Error on adding comment: dailed comment save', [
                    'user.id'       => $userId,
                    'commentData'   => $commentBody,
                    'post.id'       => $postId
                ]);
                throw new RuntimeException('Error on adding post: dailed comment save');
            }

            $commentId = $savedComment['id'];
            $commentCreated = $savedComment['created'];

            //Saved attached files
            if (!empty($fileIDs)) {
                $this->saveFiles($commentId, $userId, $fileIDs);
            }

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
           throw $e; 
        }

        return $savedComment;
    }
}
