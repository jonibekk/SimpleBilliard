<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AttachedFile', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::uses('Comment', 'Model');
App::uses('CommentFile', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('Post', 'Model');
App::uses('User', 'Model');
App::import('Service', 'UploadService');
App::import('Service', 'CommentService');
App::import('Model/Entity', 'CommentEntity');

use Mockery as mock;
use Goalous\Exception as GlException;

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
        'app.comment',
        'app.comment_file',
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
        $newBody = "A new comment";

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $this->assertEquals(0, $Comment->getCommentCount($postId));

        $newComment = $CommentService->add($newBody, $postId, $userId, $teamId);

        $this->assertEquals($userId, $newComment['user_id']);
        $this->assertEquals($teamId, $newComment['team_id']);
        $this->assertEquals($postId, $newComment['post_id']);
        $this->assertEquals($newBody, $newComment['body']);

        $this->assertEquals(1, $Comment->getCommentCount($postId));
    }


    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_addCommentPostNotExist_failed()
    {
        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $CommentService->add("test", 12081237, 1, 1);

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
        $assetsClient->shouldReceive('save')->withAnyArgs()
            ->atLeast()->once()->andReturn(true);
        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());
        $commentBody = 'body text ' . time();

        $postEntity = $CommentService->add($commentBody, 1, 1, 1, [$uuid]);

        $files = $CommentService->getAttachedFiles($postEntity['id']);

        $this->assertNotEmpty($files);
    }
}