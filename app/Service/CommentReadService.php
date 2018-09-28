<?php
App::import('Service', 'AppService');
App::uses('CommentRead', 'Model');
App::uses('Comment', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/25
 */

use Goalous\Exception as GlException;

class CommentReadService extends AppService
{
    /**
     * Add user read a comment
     *
     * @param int $commentId Target comment's ID
     * @param int $userId User ID who who reads the comment
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return CommentReadEntity
     */
    public function add(int $commentId, int $userId, int $teamId): CommentReadEntity
    {
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        $condition = [
            'conditions' => [
                'comment_id' => $commentId,
                'user_id' => $userId,
                'team_id' => $teamId
            ],
            'fields'     => [
                'id'
            ]
        ];

        //Check whether user read that comment already
        if (empty($CommentRead->find('first', $condition))) {
            try {
                $this->TransactionManager->begin();
                $CommentRead->create();
                $newData = [
                    'comment_id' => $commentId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                /** @var CommentReadEntity $result */
                $result = $CommentRead->useType()->useEntity()->save($newData, false);

                $CommentRead->updateReadersCount($commentId);

                $this->TransactionManager->commit();

            } catch (Exception $e) {
                $this->TransactionManager->rollback();
                GoalousLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()), $e->getTrace());
                throw $e;
            }
        } else {
            throw new GlException\GoalousConflictException(__("You already read this comment."));
        }

        return $result;
    }

    /**
     * Add multiple readers of the comment
     *
     * @param int $commentsIds Target comment's ID
     * @param int $userId User ID who who reads the comment
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return array | null
     */
    public function multipleAdd(array $commentsIds, int $userId, int $teamId)
    {
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        $query = [
            'conditions' => [
                'CommentRead.comment_id'   => $commentsIds,
                'CommentRead.user_id'   => $userId,
            ],
            'fields'     => 'CommentRead.comment_id'
        ];
        $CommentAlreadyReads = $CommentRead->find('all', $query);

        $CommentAlreadyReads = Hash::extract($CommentAlreadyReads, "{n}.CommentRead.comment_id");
        $newReads = array_diff($commentsIds, $CommentAlreadyReads);

        if(!empty($newReads)){
            try {
                $this->TransactionManager->begin();
                $CommentRead->create();
                $newData = array();
                foreach($newReads as $new){
                    $data = [
                        'comment_id' => $new,
                        'user_id' => $userId,
                        'team_id' => $teamId
                    ];
                    array_push($newData, $data);
                }  

                /** @var CommentReadEntity $result */
                $CommentRead->bulkInsert($newData);

                $CommentRead->updateReadersCountMultipleComments($newReads);

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
