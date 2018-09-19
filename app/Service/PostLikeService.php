<?php
App::import('Service', 'AppService');
App::uses('PostLike', 'Model');
App::uses('Post', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/20
 * Time: 12:24
 */

use Goalous\Exception as GlException;

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
    public function add(int $postId, int $userId, int $teamId)
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId
            ],
            'fields'     => [
                'id'
            ]
        ];

        //Check whether like is already exist from the user
        if (empty($PostLike->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $PostLike->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var PostLikeEntity $result */
                $result = $PostLike->useType()->useEntity()->save($newData, false);

                $result['like_count'] = $PostLike->updateLikeCount($postId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__("You already liked it."));
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
    public function delete(int $postId, int $userId): int
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId
            ],
            'fields'     => [
                'id',
            ]
        ];

        $existing = $PostLike->find('first', $condition);

        if (!empty($existing)) {
            try {
                $this->TransactionManager->begin();
                $PostLike->delete($existing['PostLike']['id']);
                $count = $PostLike->updateLikeCount($postId);
                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            //If like not exist, throw error
            throw new GlException\GoalousNotFoundException(__("You did not like it."));
        }

        return $count;
    }

}