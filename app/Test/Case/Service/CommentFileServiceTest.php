<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CommentFileService');
App::uses('Comment', 'Model');
App::uses('CommentFile', 'Model');
App::uses('AttachedFile', 'Model');
App::import('Model/Entity', 'CommentFile');

use Goalous\Enum as Enum;

/**
 * @property CommentFileService $CommentFileService
 * @property CommentFile $CommentFile
 * @property AttachedFile $AttachedFile
 */
class CommentFileServiceTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.comment',
        'app.comment_file',
        'app.attached_file',
    );


    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CommentFileService = ClassRegistry::init('CommentFileService');
        $this->CommentFile = ClassRegistry::init('CommentFile');
        $this->AttachedFile = ClassRegistry::init('AttachedFile');
    }


    public function test_softDeleteAllFiles_success()
    {
        $commentId = 1;
        $userId = 1;
        $teamId = 1;

        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');
        /** @var CommentFileService $CommentFileService */
        $CommentFileService = ClassRegistry::init('CommentFileService');

        $this->createCommentFile($commentId, $userId, $teamId, 3);

        $result = $CommentFile->getAllCommentFiles($commentId);
        $this->assertCount(4, $result);

        $CommentFileService->softDeleteAllFiles($commentId);

        $result = $CommentFile->getAllCommentFiles($commentId);

        $this->assertEmpty($result);
    }

    public function test_deleteAllByAttachedFileIds()
    {
        // Empty
        $this->CommentFileService->deleteAllByAttachedFileIds([]);
        // Not exist attached file ids
        $this->CommentFileService->deleteAllByAttachedFileIds([9999]);

        // Exist single attached file id
        $commentId = 1;
        $files = $this->CommentFile->getAllCommentFiles($commentId);
        $attachedFileIds = [];
        foreach($files as $file) {
            $attachedFileIds[] = $file['attached_file_id'];
        }
        $this->CommentFileService->deleteAllByAttachedFileIds($attachedFileIds);
        $commentFiles = $this->CommentFile->find('all', ['conditions' => ['comment_id' => $commentId]]);
        $this->assertEmpty($commentFiles);
        $attachedFiles = $this->AttachedFile->find('all', ['conditions' => ['id' => $attachedFileIds]]);
        $this->assertEmpty($attachedFiles);


        $userId = 1;
        $teamId = 1;
        $saveAttachedFiles = [
            [
                'user_id'               => $userId,
                'team_id'               => $teamId,
                'attached_file_name'    => 'test_file_for_self.txt',
                'file_type'             => 2,
                'file_ext'              => '.txt',
                'file_size'             => 100,
                'model_type'            => 0,
            ],
            [
                'user_id'               => $userId,
                'team_id'               => $teamId,
                'attached_file_name'    => 'test_file_for_self.txt',
                'file_type'             => 2,
                'file_ext'              => '.pdf',
                'file_size'             => 100,
                'model_type'            => 0,
            ],

        ];
        $attachedFileIds = [];
        foreach ($saveAttachedFiles as $saveData) {
            $this->AttachedFile->save($saveData, false);
            $attachedFileIds[] = $this->AttachedFile->getLastInsertID();
        }
        $commentId = 3;
        $this->CommentFile->saveAll([
            [
                'comment_id'       => $commentId,
                'attached_file_id' => $attachedFileIds[0],
                'team_id'          => $teamId,
                'index_num'        => 0,
            ],
            [
                'comment_id'       => $commentId,
                'attached_file_id' => $attachedFileIds[1],
                'team_id'          => $teamId,
                'index_num'        => 1,
            ]
        ], ['validate' => false]);

        $this->CommentFileService->deleteAllByAttachedFileIds($attachedFileIds);
        $commentFiles = $this->CommentFile->find('all', ['conditions' => ['comment_id' => $commentId]]);
        $this->assertEmpty($commentFiles);
        $attachedFiles = $this->AttachedFile->find('all', ['conditions' => ['id' => $attachedFileIds]]);
        $this->assertEmpty($attachedFiles);
    }

}
