<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostService');
App::import('Service', 'UploadService');
App::uses('PostFile', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('Post', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostDraft', 'Model');
App::uses('TestVideoTrait', 'Test/Trait');
App::uses('TestPostDraftTrait', 'Test/Trait');
App::import('Model/Entity', 'PostEntity');

use Goalous\Enum as Enum;
use Mockery as mock;

/**
 */
class PostServiceTest extends GoalousTestCase
{
    use TestVideoTrait, TestPostDraftTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.post',
        'app.post_file',
        'app.attached_file',
        'app.circle',
        'app.post_share_circle',
        'app.post_share_user',
        'app.circle_member',
        'app.circle',
        'app.video',
        'app.video_stream',
        'app.post_resource',
        'app.post_draft',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.post_shared_log'
    ];

    /**
     * @var Post
     */
    private $Post;

    /**
     * @var PostService
     */
    private $PostService;

    /**
     * @var PostFile
     */
    private $PostFile;

    /**
     * @var AttachedFile
     */
    private $AttachedFile;

    /**
     * @var PostShareCircle
     */
    private $PostShareCircle;

    /**
     * @var PostShareUser
     */
    private $PostShareUser;

    /**
     * @var CircleMember
     */
    private $CircleMember;

    /**
     * @var Circle
     */
    private $Circle;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostService = ClassRegistry::init('PostService');
        $this->Post = ClassRegistry::init('Post');
        $this->PostFile = ClassRegistry::init('PostFile');
        $this->AttachedFile = ClassRegistry::init('AttachedFile');
        $this->PostShareCircle = ClassRegistry::init('PostShareCircle');
        $this->PostShareUser = ClassRegistry::init('PostShareUser');
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->Circle = ClassRegistry::init('Circle');
        $this->Video = ClassRegistry::init('Video');
        $this->VideoStream = ClassRegistry::init('VideoStream');
        $this->PostResource = ClassRegistry::init('PostResource');
        $this->PostDraft = ClassRegistry::init('PostDraft');
    }

    function test_addNormal_simpleText()
    {
        $body = sprintf('body text %s', time());
        $postData = [
            'Post' => [
                'body' => $body,
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertTrue(is_numeric($post['id']));
        $this->assertEquals(Post::TYPE_NORMAL, $post['type']);
        $this->assertEquals($body, $post['body']);
        $this->assertEquals($userId, $post['user_id']);
        $this->assertEquals($teamId, $post['team_id']);
    }

    function test_addNormal_AttachFile_success()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $mock = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(true));
        $body = sprintf('body text %s', time());
        $postData = [
            'Post'    => [
                'body' => $body,
            ],
            'file_id' => ['file_test_not_exists']
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertEquals(Post::TYPE_NORMAL, $post['type']);
        $this->assertEquals($body, $post['body']);
        $this->assertEquals($userId, $post['user_id']);
        $this->assertEquals($teamId, $post['team_id']);

        // Could not test attached_files, post_files record because process is mocked
    }

    /**
     * this is testing of NOT DB rollback
     */
    function test_addNormal_exception_not_rollback()
    {
        $mock = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(false));
        $body = sprintf('body text %s', time());
        $postData = [
            'Post'    => [
                'body' => $body,
            ],
            'file_id' => ['file_test_not_exists']
        ];

        $countBefore = $this->Post->find('count');
        $exceptionCount = 0;
        try {
            $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        } catch (Exception $e) {
            $exceptionCount++;
            // exception is expected not do anything
        }
        $countAfter = $this->Post->find('count');
        $this->assertSame(1, $exceptionCount);
        $this->assertSame($countAfter, $countBefore + 1);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed saving related files
     */
    function test_addNormal_AttachFile_error()
    {
        $mock = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(false));

        $postData = [
            'Post'    => [
                'body' => 'test',
            ],
            'file_id' => ['file_test_not_exists']
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    function test_addNormal_withSharing_public()
    {
        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public'
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertNotEmpty($post);

        $postId = $post['id'];

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        // assert shared to public circle
        $sharedCircleAll = $PostShareCircle->find('all', [
            'conditions' => [
                'post_id' => $postId,
            ]
        ]);
        $this->assertCount(1, $sharedCircleAll);
        $this->assertEquals(3, $sharedCircleAll[0]['PostShareCircle']['circle_id']);
    }

    function test_addNormal_withSharing_public_circles()
    {
        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_1,circle_2',
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertNotEmpty($post);

        $postId = $post['id'];

        // assert shared to public circle
        $sharedCircleAll = $this->PostShareCircle->find('all', [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
            ]
        ]);
        $this->assertCount(3, $sharedCircleAll);
        $this->assertEquals(3, $sharedCircleAll[0]['PostShareCircle']['circle_id']);
        $this->assertEquals(1, $sharedCircleAll[1]['PostShareCircle']['circle_id']);
        $this->assertEquals(2, $sharedCircleAll[2]['PostShareCircle']['circle_id']);
    }

    function test_addNormal_withSharing_public_with_user()
    {
        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,user_2',
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertNotEmpty($post);

        $postId = $post['id'];

        // assert shared to public circle
        $sharedCircleAll = $this->PostShareCircle->find('all', [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
            ]
        ]);
        $this->assertCount(1, $sharedCircleAll);
        $this->assertEquals(3, $sharedCircleAll[0]['PostShareCircle']['circle_id']);

        // assert shared to user
        $sharedUserAll = $this->PostShareUser->find('all', [
            'conditions' => [
                'PostShareUser.post_id' => $postId,
            ]
        ]);
        $this->assertCount(1, $sharedUserAll);
        $this->assertEquals(2, $sharedUserAll[0]['PostShareUser']['user_id']);
    }

    function test_addNormal_withSharing_public_with_circle_with_user()
    {
        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_1,user_2',
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertNotEmpty($post);

        $postId = $post['id'];

        // assert shared to public circle
        $sharedCircleAll = $this->PostShareCircle->find('all', [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
            ]
        ]);
        $this->assertCount(2, $sharedCircleAll);
        $this->assertEquals(3, $sharedCircleAll[0]['PostShareCircle']['circle_id']);
        $this->assertEquals(1, $sharedCircleAll[1]['PostShareCircle']['circle_id']);

        // assert shared to user
        $sharedUserAll = $this->PostShareUser->find('all', [
            'conditions' => [
                'PostShareUser.post_id' => $postId,
            ]
        ]);
        $this->assertCount(1, $sharedUserAll);
        $this->assertEquals(2, $sharedUserAll[0]['PostShareUser']['user_id']);
    }

    function test_addNormal_withSharing_secret_circle()
    {
        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'circle_4',
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
        $this->assertNotEmpty($post);

        $postId = $post['id'];

        // assert shared to public circle
        $sharedCircleAll = $this->PostShareCircle->find('all', [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
            ]
        ]);
        $this->assertCount(1, $sharedCircleAll);
        $this->assertEquals(4, $sharedCircleAll[0]['PostShareCircle']['circle_id']);
    }

    function test_addNormal_updateModified()
    {
        $userId = 1;
        $teamId = 1;
        $circleId = 1;
        $userIdToCheckUnreadCount = 2;
        $conditions = [
            'conditions' => [
                'CircleMember.circle_id' => $circleId,
                'CircleMember.team_id'   => $teamId,
                'CircleMember.user_id'   => $userIdToCheckUnreadCount,
            ]
        ];

        $circleMembersBefore = $this->CircleMember->find('all', $conditions);
        $circleBefore = $this->Circle->getById($circleId);

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_1,user_2',
            ],
        ];
        $post = $this->PostService->addNormal($postData, $userId, $teamId);
        $this->assertNotEmpty($post);

        // assert if circle_members.unread_count is incremented
        $circleMembersAfter = $this->CircleMember->find('all', $conditions);
        $circleAfter = $this->Circle->getById($circleId);
        $this->assertEquals(
            $circleMembersBefore[0]['CircleMember']['unread_count'],
            $circleMembersAfter[0]['CircleMember']['unread_count'] - 1
        );

        // assert if circles.modified is updated
        $this->assertTrue($circleAfter['modified'] > $circleBefore['modified']);
    }

    function test_addNormal_with_resource_video()
    {
        $userId = 1;
        $teamId = 1;

        list($video, $videoStreams) = $this->createVideoSet($userId, $teamId, 'a',
            Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_1,user_2',
            ],
        ];
        $postResources = [
            $videoStreams,
        ];
        $post = $this->PostService->addNormal($postData, $userId, $teamId, $postResources);
        $this->assertNotEmpty($post);

        $postId = $post['id'];
        $postResource = $this->PostResource->findByPostId($postId);
        $this->assertEquals(Enum\Model\Post\PostResourceType::VIDEO_STREAM,
            $postResource['PostResource']['resource_type']);
    }

    function test_addNormal_with_resource_video_with_AttachFile()
    {
        $mock = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(true));

        $userId = 1;
        $teamId = 1;

        list($video, $videoStreams) = $this->createVideoSet($userId, $teamId, 'a',
            Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());

        $postData = [
            'Post'    => [
                'body'  => 'test',
                'share' => 'public,circle_1,user_2',
            ],
            'file_id' => ['file_test_not_exists'],
        ];
        $postResources = [
            $videoStreams,
        ];
        $post = $this->PostService->addNormal($postData, $userId, $teamId, $postResources);
        $this->assertNotEmpty($post);

        $postId = $post['id'];
        $postResource = $this->PostResource->findByPostId($postId);
        $this->assertEquals(Enum\Model\Post\PostResourceType::VIDEO_STREAM,
            $postResource['PostResource']['resource_type']);
    }

    /**
     * this is testing of DB rollbacked
     */
    function test_addNormal_exception_rollback()
    {
        $mock = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(false));
        $body = sprintf('body text %s', time());
        $postData = [
            'Post'    => [
                'body' => $body,
            ],
            'file_id' => ['file_test_not_exists']
        ];

        $countBefore = $this->Post->find('count');
        $this->PostService->addNormalWithTransaction($postData, $userId = 1, $teamId = 1);
        $countAfter = $this->Post->find('count');

        $this->assertSame($countAfter, $countBefore);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed saving post share users
     */
    function test_addNormal_PostShareUser_error()
    {
        $mock = $this->getMockForModel('PostShareUser', array('add'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('add')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,user_2',
            ],
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed saving post share circles
     */
    function test_addNormal_PostShareCircle_error()
    {
        $mock = $this->getMockForModel('PostShareCircle', array('add'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('add')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_2',
            ],
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed increment unread count
     */
    function test_addNormal_incrementUnreadCount_error()
    {
        $mock = $this->getMockForModel('CircleMember', array('incrementUnreadCount'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('incrementUnreadCount')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_2',
            ],
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed update modified of circle member
     */
    function test_addNormal_CircleMember_updateModified_error()
    {
        $mock = $this->getMockForModel('CircleMember', array('updateModified'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('updateModified')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_2',
            ],
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage Error on adding post: failed update modified of circles
     */
    function test_addNormal_Circle_updateModified_error()
    {
        $mock = $this->getMockForModel('Circle', array('updateModified'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('updateModified')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_2',
            ],
        ];
        $this->PostService->addNormal($postData, $userId = 1, $teamId = 1);
    }

    function test_addNormalWithTransaction_Circle_updateModified_error()
    {
        $mock = $this->getMockForModel('Circle', array('updateModified'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('updateModified')
             ->will($this->returnValue(false));

        $postData = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_2',
            ],
        ];

        $countBefore = $this->Post->find('count');
        $post = $this->PostService->addNormalWithTransaction($postData, $userId = 1, $teamId = 1);
        $countAfter = $this->Post->find('count');
        $this->assertFalse($post);
        $this->assertSame($countBefore, $countAfter);
    }

    function test_addNormalFromPostDraft()
    {
        $bodyText = sprintf('body text: %s', time());
        $videoStreamId = 11;
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource(
            $userId = 1,
            $teamId = 1,
            $videoStream = ['id' => $videoStreamId],
            $bodyText);

        $countBefore = $this->Post->find('count');
        $post = $this->PostService->addNormalFromPostDraft($postDraft);
        $countAfter = $this->Post->find('count');
        $createdPostId = $post['id'];

        $this->assertSame($bodyText, $post['body']);
        $this->assertTrue(is_numeric($createdPostId));
        $this->assertSame($countAfter, $countBefore + 1);

        // assert that draft post used is deleted
        $postDraftUsed = $this->PostDraft->find('first', [
            'conditions' => [
                'PostDraft.id'      => $postDraft['id'],
                'PostDraft.del_flg' => true,
            ],
        ]);

        // assert used post_draft deleted
        $postDraftUsed = reset($postDraftUsed);
        $this->assertTrue($postDraftUsed['del_flg']);
        $this->assertEquals($postDraft['id'], $postDraftUsed['id']);
        $this->assertEquals($createdPostId, $postDraftUsed['post_id']);

        // assert post_resource updated
        $postResourceUpdated = $this->PostResource->getById($postResource['id']);
        $this->assertEquals($createdPostId, $postResourceUpdated['post_id']);
    }

    function test_addNormalFromPostDraft_rollback()
    {
        $mock = $this->getMockForModel('PostDraft', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('save')
             ->will($this->returnValue(false));

        $bodyText = sprintf('body text: %s', time());
        $videoStreamId = 11;
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource(
            $userId = 1,
            $teamId = 1,
            $videoStream = ['id' => $videoStreamId],
            $bodyText);

        $countPostBefore = $this->Post->find('count');
        $post = $this->PostService->addNormalFromPostDraft($postDraft);
        $countPostAfter = $this->Post->find('count');

        $this->assertFalse($post);
        $this->assertSame($countPostBefore, $countPostAfter);
    }

    public function test_userHasAccessToPublicPost_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $result = $PostService->checkUserAccessToPost(4, 1);
        $result1 = $PostService->checkUserAccessToPost(4, 1, true);

        $this->assertTrue($result);
        $this->assertFalse($result1);
    }

    public function test_userHasAccessToSecretPost_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $result = $PostService->checkUserAccessToPost(2, 7, true);
        $result1 = $PostService->checkUserAccessToPost(2, 7);

        $this->assertTrue($result);
        $this->assertTrue($result1);
    }

    public function test_userHasAccessToSecretPost_failed()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $result = $PostService->checkUserAccessToPost(4, 7);

        $this->assertFalse($result);
    }

    public function test_userHasAccessToJoinedPost_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $result = $PostService->checkUserAccessToPost(1, 1);

        $this->assertTrue($result);
    }

    public function test_softDeletePost_success()
    {
        $postId = 1;

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $PostService->softDelete($postId);

        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');

        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        /** @var PostMention $PostMention */
        $PostMention = ClassRegistry::init('PostMention');

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        /** @var PostShareUser $PostShareUser */
        $PostShareUser = ClassRegistry::init('PostShareUser');

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $conditions = [
            'conditions' => [
                'post_id' => $postId,
                'del_flg' => false
            ]
        ];

        $postCondition = [
            'conditions' => [
                'Post.id'      => $postId,
                'Post.del_flg' => false
            ]
        ];

        $this->assertEmpty($PostDraft->find('first', $conditions));
        $this->assertEmpty($PostFile->find('first', $conditions));
        $this->assertEmpty($PostLike->find('first', $conditions));
        $this->assertEmpty($PostMention->find('first', $conditions));
        $this->assertEmpty($PostRead->find('first', $conditions));
        $this->assertEmpty($PostResource->find('first', $conditions));
        $this->assertEmpty($PostShareCircle->find('first', $conditions));
        $this->assertEmpty($PostShareUser->find('first', $conditions));
        $this->assertEmpty($Post->find('first', $postCondition));
    }

    public function test_saveFileInPostAdd_success()
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

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());
        $newPostData = [
            'body' => sprintf('body text %s', time()),
            'type' => 1
        ];

        $postEntity = $PostService->addCirclePost($newPostData, 1, 1, 1, [$uuid]);

        $files = $PostService->getAttachedFiles($postEntity['id']);

        $this->assertNotEmpty($files);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_editPostMissing_failed()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $newBody = 'EDITED';

        $PostService->editPost($newBody, 183281390);

        $this->fail();
    }

    public function test_editPost_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $newBody = 'EDITED';

        $res = $PostService->editPost($newBody, 1);

        $this->assertTrue($res instanceof PostEntity);
        $this->assertEquals($newBody, $res['body']);
    }
}
