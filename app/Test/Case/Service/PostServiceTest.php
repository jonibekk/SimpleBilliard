<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostService');
App::import('Service', 'UploadService');
App::import('Service', 'CircleService');
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
App::uses('UnreadCirclePost', 'Model');
App::import('Model/Entity', 'PostEntity');
App::import('Service/Request/Resource', 'PostResourceRequest');

use Goalous\Enum as Enum;
use Mockery as mock;
use Goalous\Exception as GlException;
use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

/**
 * @property CircleService CircleService
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
        'app.team',
        'app.team_member',
        'app.user',
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
        'app.post_shared_log',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.saved_post',
        'app.search_post_file',
        'app.action_result',
        'app.action_result_file',
        'app.key_result',
        'app.goal',
        'app.post_file',
        'app.comment_file',
        'app.team_translation_language',
        'app.team_translation_status',
        'app.mst_translation_language',
        'app.translation',
        'app.cache_unread_circle_post',
        'app.kr_progress_log',
        'app.kr_watchlist',
        'app.watchlist'
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
        $this->CircleService = ClassRegistry::init('CircleService');
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
        $mock = $this->getMockForModel('AttachedFile', ['saveRelatedFiles']);
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
        $mock = $this->getMockForModel('AttachedFile', ['saveRelatedFiles']);
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
        $mock = $this->getMockForModel('AttachedFile', ['saveRelatedFiles']);
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

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $updatedCircle = $Circle->getById(1);

        $this->assertTrue($updatedCircle['latest_post_created'] > 100);
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

        list($video, $videoStreams) = $this->createVideoSet(
            $userId,
            $teamId,
            'a',
            Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE()
        );

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
        $this->assertEquals(
            Enum\Model\Post\PostResourceType::VIDEO_STREAM,
            $postResource['PostResource']['resource_type']
        );
    }

    function test_addNormal_with_resource_video_with_AttachFile()
    {
        $mock = $this->getMockForModel('AttachedFile', ['saveRelatedFiles']);
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('saveRelatedFiles')
             ->will($this->returnValue(true));

        $userId = 1;
        $teamId = 1;

        list($video, $videoStreams) = $this->createVideoSet(
            $userId,
            $teamId,
            'a',
            Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE()
        );

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
        $this->assertEquals(
            Enum\Model\Post\PostResourceType::VIDEO_STREAM,
            $postResource['PostResource']['resource_type']
        );
    }

    /**
     * this is testing of DB rollbacked
     */
    function test_addNormal_exception_rollback()
    {
        $mock = $this->getMockForModel('AttachedFile', ['saveRelatedFiles']);
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
        $mock = $this->getMockForModel('PostShareUser', ['add']);
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
        $mock = $this->getMockForModel('PostShareCircle', ['add']);
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
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Error on adding post: failed increment unread count
     */
    function test_addNormal_incrementUnreadCount_error()
    {
        $mock = $this->getMockForModel('CircleMember', ['incrementUnreadCount']);
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
        $mock = $this->getMockForModel('CircleMember', ['updateModified']);
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
        $mock = $this->getMockForModel('Circle', ['updateModified']);
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
        $mock = $this->getMockForModel('Circle', ['updateModified']);
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
            $bodyText
        );

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
        $mock = $this->getMockForModel('PostDraft', ['save']);
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
            $bodyText
        );

        $countPostBefore = $this->Post->find('count');
        $post = $this->PostService->addNormalFromPostDraft($postDraft);
        $countPostAfter = $this->Post->find('count');

        $this->assertFalse($post);
        $this->assertSame($countPostBefore, $countPostAfter);
    }

    public function test_userHasAccessToPublicPost_success()
    {
        $result = $this->PostService->checkUserAccessToCirclePost(4, 1);
        $result1 = $this->PostService->checkUserAccessToCirclePost(4, 1, true);

        $this->assertTrue($result);
        $this->assertFalse($result1);
    }

    public function test_userHasAccessToSecretPost_success()
    {
        $result = $this->PostService->checkUserAccessToCirclePost(2, 7, true);
        $result1 = $this->PostService->checkUserAccessToCirclePost(2, 7);

        $this->assertTrue($result);
        $this->assertTrue($result1);
    }

    public function test_userHasAccessToSecretPost_failed()
    {
        $result = $this->PostService->checkUserAccessToCirclePost(4, 7);

        $this->assertFalse($result);
    }

    public function test_userHasAccessToJoinedPost_success()
    {
        $result = $this->PostService->checkUserAccessToCirclePost(1, 1);

        $this->assertTrue($result);
    }

    public function test_softDeletePost_success()
    {
        $postId = 1;

        $this->PostService->softDelete($postId);

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

        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

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

        $numAttachedFiles = $AttachedFile->getCountOfAttachedFiles($postId, AttachedModelType::TYPE_MODEL_POST);

        $this->assertEmpty($PostDraft->find('first', $conditions));
        $this->assertEquals(0, $numAttachedFiles);
        $this->assertEmpty($PostFile->find('first', $conditions));
        $this->assertEmpty($PostLike->find('first', $conditions));
        $this->assertEmpty($PostMention->find('first', $conditions));
        $this->assertEmpty($PostRead->find('first', $conditions));
        $this->assertEmpty($PostResource->find('first', $conditions));
        $this->assertEmpty($PostShareCircle->find('first', $conditions));
        $this->assertEmpty($PostShareUser->find('first', $conditions));
        $this->assertEmpty($Post->find('first', $postCondition));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_softDeletePostNotExist_failed()
    {
        $postId = 10909;
        $this->PostService->softDelete($postId);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_softDeletePostDeleted_failed()
    {
        $postId = 1;

        $this->PostService->softDelete($postId);
        $this->PostService->softDelete($postId);
    }

    public function test_saveFileInPostAdd_success()
    {
        //Mock storage clients
        $bufferClient = mock::mock('BufferStorageClient');
        $bufferClient->shouldReceive('bulkGet')->withAnyArgs()
                     ->atLeast()->once()
                     ->andReturn([new UploadedFile("eyJkYXRhIjoiaGFoYSJ9", "a")]);
        $bufferClient->shouldReceive('save')->withAnyArgs()
                     ->atLeast()->once()
                     ->andReturn("1234567890abcd.12345678");
        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        $assetsClient = mock::mock('AssetsStorageClient');
        $assetsClient->shouldReceive('bulkSave')->withAnyArgs()
                     ->atLeast()->once()->andReturn(true);
        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());
        $newPostData = [
            'body' => sprintf('body text %s', time()),
            'type' => 1
        ];

        $postEntity = $this->PostService->addCirclePost($newPostData, 1, 1, 1, [
            ["file_uuid" => $uuid],
        ]);

        $files = $this->PostService->getNormalAttachedFiles($postEntity['id']);

        $this->assertNotEmpty($files);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_editPostMissing_failed()
    {
        $updatePost['body'] = 'EDITED';

        $this->PostService->editPost($updatePost, 183281390, 1, 1, []);
    }

    public function test_editPost_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var Video $Video */
        $Video = ClassRegistry::init('Video');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $postId = 1;

        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        $oldPost = $Post->getEntity($postId);

        $updatePost['body'] = 'EDITED';

        $res = $this->PostService->editPost($updatePost, $postId, $userId, $teamId, []);

        $this->assertTrue($res instanceof PostEntity);
        $this->assertNotEquals($Post->getEntity($postId)['body'], $oldPost['body']);
        $this->assertEquals($updatePost['body'], $res['body']);

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        $newBody['body'] = 'EDITED 2';

        $resources = [
            ['id' => $newFiles[0]['attached_file_id'], 'resource_type' => Enum\Model\Post\PostResourceType::IMAGE],
            ['id' => $newVideos[0]['id'], 'resource_type' => Enum\Model\Post\PostResourceType::VIDEO_STREAM]
        ];

        $editedPost = $this->PostService->editPost($newBody, $newPostId, $userId, $teamId, $resources);

        $this->assertEquals($newBody['body'], $editedPost['body']);
        $this->assertEquals(2, $PostResource->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertNotEmpty($PostFile->find('first', ['conditions' => ['id' => $newFiles[0]['id']]]));
        $this->assertNotEmpty($AttachedFile->find('first',
            ['conditions' => ['id' => $newFiles[0]['attached_file_id']]]));
        $this->assertNotEmpty($VideoStream->find('first', ['conditions' => ['id' => $newVideos[0]['id']]]));
        $this->assertNotEmpty($Video->find('first', ['conditions' => ['id' => $newVideos[0]['video_id']]]));
    }

    public function test_checkUserAccessToMultiplePost_failure()
    {
        /* Circle post and it was created by login user */
        $userId = 1;
        $teamId = 1;

        $postUserId = 1;
        $circleId = 1;
        $post = $this->PostService->addCirclePost(['body' => 'test', 'type' => Post::TYPE_NORMAL], $circleId,
            $postUserId, $teamId);
        $postIds = [$post['id']];

        $msg = "";
        try {
            $this->PostService->checkUserAccessToMultiplePost($userId, $postIds);
        } catch (GlException\GoalousNotFoundException $e) {
            $msg = $e->getMessage();
        }
        $this->assertEmpty($msg);

        /* Other member's circle post and login user belongs to the circle */
        $postUserId = 2;
        $circleId = 1;
        $post = $this->PostService->addCirclePost(['body' => 'test', 'type' => Post::TYPE_NORMAL], $circleId,
            $postUserId, $teamId);
        $postIds[] = $post['id'];

        $msg = "";
        try {
            $this->PostService->checkUserAccessToMultiplePost($userId, $postIds);
        } catch (GlException\GoalousNotFoundException $e) {
            $msg = $e->getMessage();
        }
        $this->assertEmpty($msg);

        /* Action post */
        $postData = [
            'Post' => ['body' => 'test', 'type' => Post::TYPE_ACTION]
        ];
        $post = $this->PostService->addNormal($postData, $postUserId, $teamId);
        $postIds[] = $post['id'];

        $msg = "";
        try {
            $this->PostService->checkUserAccessToMultiplePost($userId, $postIds);
        } catch (GlException\GoalousNotFoundException $e) {
            $msg = $e->getMessage();
        }
        $this->assertEmpty($msg);

        /* Other member's circle post and login user doesn't belong to the circle */
        $circleId = 3;
        $post = $this->PostService->addCirclePost(['body' => 'test', 'type' => Post::TYPE_NORMAL], $circleId,
            $postUserId, $teamId);
        $postIds[] = $post['id'];

        $msg = "";
        try {
            $this->PostService->checkUserAccessToMultiplePost($userId, $postIds);
        } catch (GlException\GoalousNotFoundException $e) {
            $msg = $e->getMessage();
        }
        $this->assertEmpty($msg);

        /* Login user doesn't belong to secret circle */
        $circleId = 4;
        $postUserId = 1;
        $userId = 9999;
        $post = $this->PostService->addCirclePost(['body' => 'test', 'type' => Post::TYPE_NORMAL], $circleId,
            $postUserId, $teamId);
        $postIds = [$post['id']];

        $msg = "";
        try {
            $this->PostService->checkUserAccessToMultiplePost($userId, $postIds);
        } catch (GlException\GoalousNotFoundException $e) {
            $msg = $e->getMessage();
        }
        $this->assertNotEmpty($msg);
    }

    public function test_checkUserAccessToMultiplePost_success()
    {
        $postsIds = [1, 101];
        $userId = 1;

        $this->PostService->checkUserAccessToMultiplePost($userId, $postsIds);
    }

    public function test_addCirclePost_success()
    {
        $userId = 1;
        $teamId = 1;
        $circleId = 1;
        $body = "A new post";
        $type = 1;

        $newData['body'] = $body;
        $newData['type'] = $type;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        $initialCircle = $Circle->getById($circleId);

        $newPost = $this->PostService->addCirclePost($newData, $circleId, $userId, $teamId);

        $this->assertEquals($body, $newPost['body']);
        $this->assertEquals($userId, $newPost['user_id']);
        $this->assertEquals($teamId, $newPost['team_id']);

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        $PostShareCircle->current_team_id = $teamId;

        $this->assertCount(1, $PostShareCircle->getShareCircleList($newPost['id']));

        $updatedCircle = $Circle->getById($circleId);

        $this->assertLessThan($updatedCircle['latest_post_created'], $initialCircle['latest_post_created']);
        $this->assertLessThan($updatedCircle['modified'], $initialCircle['modified']);

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $count = $UnreadCirclePost->countPostUnread($circleId, $newPost['id']);
        $this->assertEquals(2, $count);
    }

    public function test_get()
    {
        $extensions = [
            PostExtender::EXTEND_USER,
            PostExtender::EXTEND_RELATED_TYPE,
            PostExtender::EXTEND_COMMENTS,
            PostExtender::EXTEND_POST_SHARE_CIRCLE,
            PostExtender::EXTEND_POST_SHARE_USER,
            PostExtender::EXTEND_POST_RESOURCES,
            PostExtender::EXTEND_LIKE,
            PostExtender::EXTEND_SAVED,
            PostExtender::EXTEND_READ,
        ];

        $req = new PostResourceRequest(1, 1, 1, true);
        $ret = $this->PostService->get($req, $extensions);

        $this->assertTrue(is_array($ret));
        $this->assertEquals($ret['id'], $req->getId());
        $this->assertEquals($ret['del_flg'], false);
        $this->assertTrue(is_array($ret['user']));
        $this->assertEquals($ret['is_liked'], false);
        $this->assertEquals($ret['is_read'], false);
        $this->assertEquals($ret['is_saved'], false);
        $this->assertEquals($ret['resources'], []);
        $this->assertEquals(count($ret['comments']['data']), 2);
        $this->assertEquals($ret['comments']['data'][0]['post_id'], $req->getId());
        $this->assertEquals($ret['comments']['count'], 2);
        $this->assertEquals($ret['comments']['cursor'], null);

        $extensions = [
            PostExtender::EXTEND_ALL
        ];
        $ret = $this->PostService->get($req, $extensions);

        $this->assertTrue(is_array($ret));
        $this->assertEquals($ret['id'], $req->getId());
        $this->assertEquals($ret['del_flg'], false);
        $this->assertTrue(is_array($ret['user']));
        $this->assertEquals($ret['is_liked'], false);
        $this->assertEquals($ret['is_read'], false);
        $this->assertEquals($ret['is_saved'], false);
        $this->assertEquals($ret['resources'], []);
        $this->assertEquals(count($ret['comments']['data']), 2);
        $this->assertEquals($ret['comments']['data'][0]['post_id'], $req->getId());
        $this->assertEquals($ret['comments']['count'], 2);
        $this->assertEquals($ret['comments']['cursor'], null);

        $req->setId(7);
        $req->setUserId(99);
        $ret = $this->PostService->get($req, $extensions);
        $this->assertEquals($ret, []);

        $req->setCheckPermission(false);
        $ret = $this->PostService->get($req, $extensions);
        $this->assertNotEmpty($ret);

        $req->setId(8);
        $req->setUserId(2);
        $req->setCheckPermission(true);
        $ret = $this->PostService->get($req, $extensions);
        $this->assertTrue(is_array($ret));
        $this->assertEquals(count($ret['attached_files']), 1);
        $this->assertEquals($ret['attached_files'][0]['id'], 2);

        $this->assertEquals($ret['action_result']['id'], 1);
        GoalousLog::info("ret", $ret['key_result']);
        $this->assertEquals($ret['key_result']['id'], $ret['action_result']['key_result_id']);
        $this->assertEquals($ret['goal']['id'], $ret['action_result']['goal_id']);
    }

    public function test_editPostWithRemovedResource_success()
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var Video $Video */
        $Video = ClassRegistry::init('Video');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        $newBody['body'] = 'EDITED 1';

        $resources = [
            ['id' => $newFiles[0]['attached_file_id'], 'resource_type' => Enum\Model\Post\PostResourceType::IMAGE]
        ];

        $editedPost = $this->PostService->editPost($newBody, $newPostId, $userId, $teamId, $resources);

        $this->assertEquals($newBody['body'], $editedPost['body']);
        $this->assertEquals(1, $PostResource->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertNotEmpty($PostFile->find('first', ['conditions' => ['id' => $newFiles[0]['id']]]));
        $this->assertNotEmpty($AttachedFile->find('first',
            ['conditions' => ['id' => $newFiles[0]['attached_file_id']]]));
        $this->assertEmpty($VideoStream->find('first', ['conditions' => ['id' => $newVideos[0]['id']]]));
        $this->assertEmpty($Video->find('first', ['conditions' => ['id' => $newVideos[0]['video_id']]]));

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        $newBody['body'] = 'EDITED 2';

        $resources = [
            ['id' => $newVideos[0]['id'], 'resource_type' => Enum\Model\Post\PostResourceType::VIDEO_STREAM]
        ];

        $editedPost = $this->PostService->editPost($newBody, $newPostId, $userId, $teamId, $resources);

        $this->assertEquals($newBody['body'], $editedPost['body']);
        $this->assertEquals(1, $PostResource->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertNotEmpty($VideoStream->find('first', ['conditions' => ['id' => $newVideos[0]['id']]]));
        $this->assertNotEmpty($Video->find('first', ['conditions' => ['id' => $newVideos[0]['video_id']]]));
        $this->assertEmpty($PostFile->find('first', ['conditions' => ['id' => $newFiles[0]['id']]]));
        $this->assertEmpty($AttachedFile->find('first', ['conditions' => ['id' => $newFiles[0]['attached_file_id']]]));

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        $newBody['body'] = 'EDITED 3';

        $resources = [];

        $editedPost = $this->PostService->editPost($newBody, $newPostId, $userId, $teamId, $resources);

        $this->assertEquals($newBody['body'], $editedPost['body']);
        $this->assertEquals(0, $PostResource->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertEmpty($VideoStream->find('first', ['conditions' => ['id' => $newVideos[0]['id']]]));
        $this->assertEmpty($Video->find('first', ['conditions' => ['id' => $newVideos[0]['video_id']]]));
        $this->assertEmpty($PostFile->find('first', ['conditions' => ['id' => $newFiles[0]['id']]]));
        $this->assertEmpty($AttachedFile->find('first', ['conditions' => ['id' => $newFiles[0]['attached_file_id']]]));
    }

    public function test_editPostWithAddedResource_success()
    {

        //Mock storage clients
        $bufferClient = mock::mock('BufferStorageClient');
        $bufferClient->shouldReceive('bulkGet')->withAnyArgs()
                     ->atLeast()->once()
                     ->andReturn([new UploadedFile("eyJkYXRhIjoiaGFoYSJ9", "a")]);
        $bufferClient->shouldReceive('save')->withAnyArgs()
                     ->atLeast()->once()
                     ->andReturn("1234567890abcd.12345678");
        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        $assetsClient = mock::mock('AssetsStorageClient');
        $assetsClient->shouldReceive('bulkSave')->withAnyArgs()
                     ->atLeast()->once()->andReturn(true);
        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());

        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $resources = [
            [
                "id"            => $newFiles[0]['attached_file_id'],
                "resource_type" => Enum\Model\Post\PostResourceType::IMAGE
            ],
            [
                "id"            => $newVideos[0]['id'],
                "resource_type" => Enum\Model\Post\PostResourceType::VIDEO_STREAM
            ],
            [
                "file_uuid" => $uuid
            ]
        ];

        $newBody = 'EDITED';

        $PostService->editPost(['body' => $newBody], $newPostId, $userId, $teamId, $resources);

        $editedPost = $Post->getEntity($newPostId);

        $this->assertEquals($newBody, $editedPost['body']);
        $this->assertEquals(1, $VideoStream->find('count', ['conditions' => ['id' => $newVideos[0]['id']]]));
        $this->assertEquals(3, $PostResource->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertEquals(2, $PostFile->find('count', ['conditions' => ['post_id' => $newPostId]]));
        $this->assertEquals(2, $PostResource->findMaxResourceOrderOfPost($newPostId));
    }

    public function test_addCirclePostWithTranslation_success()
    {
        $this->createTranslatorClientMock();

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $teamId = 1;
        $circleId = 1;
        $userId = 1;
        $newBody['type'] = Post::TYPE_NORMAL;
        $newBody['body'] = "Some content";

        $TeamTranslationStatus->createEntry($teamId);

        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");
        $this->insertTranslationLanguage($teamId, "id");

        $newPostEntity = $PostService->addCirclePost($newBody, $circleId, $userId, $teamId);

        $this->assertNotEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $newPostEntity['id'],
            "ja"));
        $this->assertEquals("en", $Post->getById($newPostEntity['id'])['language']);
    }

    public function test_editCirclePostWithTranslation_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $teamId = 1;
        $userId = 1;
        $postId = 1;
        $otherPostId = 2;
        $newPostBody = "Translation is gone.";

        $TeamTranslationStatus->createEntry($teamId);

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $postId, "de");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $postId, "ja");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $otherPostId, "ja");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST_COMMENT(), $postId, "de");

        $newBody['body'] = $newPostBody;

        $PostService->editPost($newBody, $postId, $userId, $teamId, []);

        $this->assertEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $postId, "de"));
        $this->assertEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $postId, "ja"));
        $this->assertNotEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $otherPostId, "ja"));
        $this->assertNotEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST_COMMENT(), $postId,
            "de"));
    }

    public function test_deleteCirclePostWithTranslation_success()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $teamId = 1;
        $postId = 1;
        $otherPostId = 2;
        $TeamTranslationStatus->createEntry($teamId);

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $postId, "de");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $postId, "ja");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), $otherPostId, "ja");
        $Translation->createEntry(TranslationContentType::CIRCLE_POST_COMMENT(), $postId, "de");

        $PostService->softDelete($postId);

        $this->assertEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $postId, "de"));
        $this->assertEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $postId, "ja"));
        $this->assertNotEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST(), $otherPostId, "ja"));
        $this->assertNotEmpty($Translation->getTranslation(TranslationContentType::CIRCLE_POST_COMMENT(), $postId,
            "de"));
    }
}
