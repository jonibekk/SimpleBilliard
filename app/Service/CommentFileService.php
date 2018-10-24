<?php
App::import('Service', 'AppService');
App::import('Model/Entity', 'CommentFileEntity');
App::uses('CommentFile', 'Model');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 18/10/17
 * Time: 15:24
 */
class CommentFileService extends AppService
{
    /**
     * Add a new post_files entry
     *
     * @param int $commentId
     * @param int $attachedFileId
     * @param int $teamId
     * @param int $indexNum
     *
     * @return CommentFileEntity
     * @throws Exception
     */
    public function add(int $commentId, int $attachedFileId, int $teamId, int $indexNum): CommentFileEntity
    {
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');

        $newData = [
            'comment_id'       => $commentId,
            'attached_file_id' => $attachedFileId,
            'team_id'          => $teamId,
            'index_num'        => $indexNum,
            'created'          => GoalousDateTime::now()->getTimestamp()
        ];

        try {
            $this->TransactionManager->begin();
            $CommentFile->create();
            $result = $CommentFile->useType()->useEntity()->save($newData, false);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error($errorMessage = 'Failed saving comment_files', [
                'comments.id'      => $commentId,
                'team.id'          => $teamId,
                'attached_file.id' => $attachedFileId,
            ]);
            throw new RuntimeException('Error on adding comment_files: ' . $errorMessage);
        }
        return $result;
    }
}