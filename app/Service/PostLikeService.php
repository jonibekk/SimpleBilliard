<?php
App::import('Service', 'AppService');
App::uses('PostLike', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/20
 * Time: 12:24
 */

class PostLikeService extends AppService
{

    /**
     * Add a like to a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who added the like
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return PostLikeEntity | null Null for failed addition
     */
    public function addPostLike(int $postId, int $userId, int $teamId)
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $this->TransactionManager->begin();
        try {
            $condition = [
                'conditions' => [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ]
            ];
            //Check whether like is already exist from the user
            if (empty($PostLike->find('first', $condition))) {
                $PostLike->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var PostLikeEntity $result */
                $result = $PostLike->useType()->useEntity()->save($newData, false);
            }
            $count = $PostLike->updateLikeCount($postId) ?? 0;

            if (empty($result)) {
                $result = new PostLikeEntity();
            }

            $result['like_count'] = $count;

            $this->TransactionManager->commit();

        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            throw $e;
        }
        return $result;
    }

    /**
     * Delete a like from a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who removed the like
     *
     * @return int New like count
     * @throws Exception
     */
    public function deletePostLike(int $postId, int $userId): int
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');
        try {
            $condition = [
                'conditions' => [
                    'post_id' => $postId,
                    'user_id' => $userId
                ]
            ];

            $existing = $PostLike->useType()->useEntity()->find('first', $condition);

            if (!empty($existing)) {
                $this->TransactionManager->begin();
                $PostLike->delete($existing['id']);
                $count = $PostLike->updateLikeCount($postId);
                $this->TransactionManager->commit();
            }
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            throw $e;
        }

        return $count ?? 0;
    }


}