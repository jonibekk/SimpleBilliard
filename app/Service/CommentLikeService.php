<?php
App::import('Service', 'AppService');
App::uses('CommentLike', 'Model');
App::uses('Model/Entity', 'CommentLikeEntity');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/23
 * Time: 10:34
 */
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
    public function addCommentLike(int $commentId, int $userId, int $teamId)
    {
        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        $newData = [
            'comment_id' => $commentId,
            'user_id'    => $userId,
            'team_id'    => $teamId
        ];

        try {
            $this->TransactionManager->begin();
            $condition['conditions'] = $newData;

            if (empty($CommentLike->find('first', $condition))) {

                $CommentLike->create();
                $res = $CommentLike->useType()->useEntity()->save($newData, false);

                $newCount = $CommentLike->updateCommentLikeCount($commentId);

                $this->TransactionManager->commit();

                $res['like_count'] = $newCount;

                return $res;
            }

        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        return null;
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
    public function removeCommentLike(int $commentId, int $userId, int $teamId): int
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

        if (empty($existing)) {
            return false;
        }
        try {
            $this->TransactionManager->begin();
            $CommentLike->delete($existing['CommentLike']['id']);
            $newCount = $CommentLike->updateCommentLikeCount($commentId);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        return $newCount ?? 0;
    }

}