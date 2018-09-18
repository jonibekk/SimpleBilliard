<?php
App::import('Service', 'AppService');
App::import('Model/Entity', 'CommentLikeEntity');
App::uses('CommentLike', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/23
 * Time: 10:34
 */

use Goalous\Exception as GlException;

class CommentLikeService extends AppService
{
    /**
     * Add a like to a comment
     *
     * @param int $commentId
     * @param int $userId
     * @param int $teamId
     *
     * @throws Exception
     * @return CommentLikeEntity | null Entity on successful addition
     */
    public function add(int $commentId, int $userId, int $teamId)
    {
        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $newData = [
            'comment_id' => $commentId,
            'user_id'    => $userId,
            'team_id'    => $teamId
        ];

        $condition['conditions'] = $newData;

        if (empty($CommentLike->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $CommentLike->create();
                $newCommentLike = $CommentLike->useType()->useEntity()->save($newData, false);

                $newCommentLike['like_count'] = $CommentLike->updateCommentLikeCount($commentId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__("You already liked it."));
        }

        return $newCommentLike;
    }

    /**
     * Remove like from a comment
     *
     * @param int $commentId
     * @param int $userId
     * @param int $teamId
     *
     * @return int New like count like
     * @throws Exception
     */
    public function delete(int $commentId, int $userId, int $teamId): int
    {
        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        $condition = [
            'conditions' => [
                'comment_id' => $commentId,
                'user_id'    => $userId,
                'team_id'    => $teamId
            ]
        ];

        $existing = $CommentLike->find('first', $condition);

        if (!empty($existing)) {
            try {
                $this->TransactionManager->begin();
                $CommentLike->delete($existing['CommentLike']['id']);
                $newCount = $CommentLike->updateCommentLikeCount($commentId);
                $this->TransactionManager->commit();
            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                throw $e;
            }
        } else {
            throw new GlException\GoalousNotFoundException(__("You did not like it."));
        }

        return $newCount;
    }

}