<?php
App::import('Service', 'AppService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/19
 */

use Goalous\Exception as GlException;

class PostReadService extends AppService
{
    /**
     * Add user read a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who who reads the post
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return PostReadEntity
     */
    public function add(int $postId, int $userId, int $teamId)
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

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

        //Check whether user read that post already
        if (empty($PostRead->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $PostRead->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var PostReadEntity $result */
                $result = $PostRead->useType()->useEntity()->save($newData, false);

                $PostRead->updateReadersCount($postId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__("You already read this post."));
        }

        return $result;
    }

    /**
     * Add multiple 
     *
     * @param int $postIds Target post's ID
     * @param int $userId User ID who who reads the post
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return array | null
     */
    public function multipleAdd(array $postIds, int $userId, int $teamId)
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $query = [
            'conditions' => [
                'PostRead.post_id'   => $postIds,
                'PostRead.user_id'   => $userId,
            ],
            'fields'     => 'PostRead.post_id'
        ];
        $PostAlreadyReadArray = $PostRead->find('all', $query);

        $PostAlreadyReadArray = Hash::extract($PostAlreadyReadArray, "{n}.PostRead.post_id");
        $newReads = array_diff($postIds, $PostAlreadyReadArray);

        if(!empty($newReads)){
            try {
                $this->TransactionManager->begin();
                $PostRead->create();
                $newData = array();
                foreach($newReads as $new){
                    $data = [
                        'post_id' => $new,
                        'user_id' => $userId,
                        'team_id' => $teamId
                    ];
                    array_push($newData, $data);
                }  

                /** @var PostReadEntity $result */
                $PostRead->useType()->useEntity()->bulkInsert($newData);

                $PostRead->updateReadersCountMultiplePost($newReads);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } 

        return $newReads;
    }
}
