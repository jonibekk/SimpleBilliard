<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CommentFileService');
App::uses('Comment', 'Model');
App::uses('CommentFile', 'Model');
App::uses('AttachedFile', 'Model');
App::import('Model/Entity', 'CommentFile');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/03/04
 * Time: 16:26
 */

use Goalous\Enum as Enum;

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


}