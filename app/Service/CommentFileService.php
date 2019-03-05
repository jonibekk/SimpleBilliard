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

    /**
     * Soft delete all files attached to a comment
     *
     * @param int $commentId
     *
     * @throws Exception
     */
    public function softDeleteAllFiles(int $commentId)
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');

        $commentFiles = $CommentFile->getAllCommentFiles($commentId);

        if (empty($commentFiles)) return;

        $commentFileIds = [];
        $attachedFileIds = [];

        foreach ($commentFiles as $commentFile) {
            $commentFileIds[] = $commentFile['id'];
            $attachedFileIds[] = $commentFile['attached_file_id'];
        }

        try {
            $this->TransactionManager->begin();

            $result = $CommentFile->softDeleteAll(['CommentFile.id' => $commentFileIds], false) &&
                $AttachedFile->softDeleteAll(['AttachedFile.id' => $attachedFileIds], false);

            if (!$result) {
                throw new RuntimeException("Failed to delete comment files for comment $commentId");
            }
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

    }
}