<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AttachedFile', 'Model');
App::uses('CircleMember', 'Model');
App::uses('ActionResultFile', 'Model');
App::uses('Circle', 'Model');
App::uses('Comment', 'Model');
App::uses('MessageFile', 'Model');
App::uses('CommentFile', 'Model');
App::uses('CommentLike', 'Model');
App::uses('CommentRead', 'Model');
App::uses('CommentMention', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('Post', 'Model');
App::uses('User', 'Model');
App::import('Service', 'UploadService');
App::import('Service', 'CommentService');
App::import('Model/Entity', 'CommentEntity');

use Mockery as mock;
use Goalous\Exception as GlException;
use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 18/10/17
 * Time: 17:52
 */
class CommentServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.post_file',
        'app.action_result_file',
        'app.message_file',
        'app.comment',
        'app.comment_file',
        'app.comment_like',
        'app.comment_read',
        'app.comment_mention',
        'app.attached_file',
        'app.circle',
        'app.post_share_circle',
        'app.post_share_user',
        'app.circle_member',
        'app.circle',
        'app.user',
        'app.team',
        'app.local_name',
    );

    public function test_addComment_success()
    {
        $userId = 1;
        $teamId = 1;
        $postId = 1;
        $newBody['body'] = "A new comment";

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $initialCommentCount = $Comment->getCommentCount($postId);

        $newComment = $CommentService->add($newBody, $postId, $userId, $teamId);

        $this->assertEquals($userId, $newComment['user_id']);
        $this->assertEquals($teamId, $newComment['team_id']);
        $this->assertEquals($postId, $newComment['post_id']);
        $this->assertEquals($newBody['body'], $newComment['body']);

        $this->assertEquals(++$initialCommentCount, $Comment->getCommentCount($postId));
    }


    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_addCommentPostNotExist_failed()
    {
        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        $newBody['body'] = "A new comment";

        $CommentService->add($newBody, 12081237, 1, 1);

        $this->fail();
    }


    public function test_saveFileInCommentAdd_success()
    {
        //Mock storage clients
        $bufferClient = mock::mock('BufferStorageClient');
        $bufferClient->shouldReceive('get')->withAnyArgs()
            ->atLeast()->once()
            ->andReturn(new UploadedFile("eyJkYXRhIjoiaGFoYSJ9", "a"));
        $bufferClient->shouldReceive('save')->withAnyArgs()
            ->atLeast()->once()
            ->andReturn("1234567890abcd.12345678");
        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        $assetsClient = mock::mock('AssetsStorageClient');
        $assetsClient->shouldReceive('bulkSave')->withAnyArgs()
            ->atLeast()->once()->andReturn(true);
        $assetsClient->shouldReceive('delete')->withAnyArgs()
            ->atLeast()->once()->andReturn(true);
        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());
        $newBody['body'] = "A new comment " . time();

        $postEntity = $CommentService->add($newBody, 1, 1, 1, [$uuid]);

        $files = $CommentService->getAttachedFiles($postEntity['id']);

        $this->assertNotEmpty($files);
    }

    public function test_deleteComment_success()
    {
        $commentId = 1;

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $CommentService->delete($commentId);

        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        /** @var CommentMention $CommentMention */
        $CommentMention = ClassRegistry::init('CommentMention');

        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $conditions = [
            'conditions' => [
                'comment_id' => $commentId,
                'del_flg' => false
            ]
        ];

        $commentCondition = [
            'conditions' => [
                'Comment.id'      => $commentId,
                'Comment.del_flg' => false
            ]
        ];

        $numAttachedFiles = $AttachedFile->getCountOfAttachedFiles($commentId, AttachedModelType::TYPE_MODEL_COMMENT);

        $this->assertEmpty($CommentFile->find('first', $conditions));
        $this->assertEquals(0, $numAttachedFiles);
        $this->assertEmpty($CommentLike->find('first', $conditions));
        $this->assertEmpty($CommentRead->find('first', $conditions));
        $this->assertEmpty($CommentMention->find('first', $conditions));
        $this->assertEmpty($Comment->find('first', $commentCondition));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteCommentNotExist_failed()
    {
        $commentId = 10909;

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $CommentService->delete($commentId);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteCommentDeleted_failed()
    {
        $commentId = 1;

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $CommentService->delete($commentId);
        $CommentService->delete($commentId);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_editCommentMissing_failed()
    {
        $updateComment['body'] = 'EDITED';

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        $CommentService->edit($updateComment, 183281390);
    }
    
    public function test_editComment_success()
    {
        $updateComment['body'] = 'EDITED';

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        $res = $CommentService->edit($updateComment, 1);
        $this->assertTrue($res instanceof CommentEntity);
        $this->assertEquals($updateComment['body'], $res['body']);
    }
}
