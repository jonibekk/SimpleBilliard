<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostService');
App::import('Service', 'PostDraftService');
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
App::uses('TeamStatus', 'Lib/Status');

use Goalous\Model\Enum as Enum;

/**
 * Class PostDraftServiceTest
 */
class PostDraftServiceTest extends GoalousTestCase
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
     * @var PostDraftService
     */
    private $PostDraftService;

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
        $this->PostDraftService = ClassRegistry::init('PostDraftService');
    }

    public function test_createPostDraftWithResources()
    {
        // TODO: write test
        $this->assertTrue(true);
    }

    public function test_isPreparedToPost_true()
    {
        $userId = 1;
        $teamId = 1;
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, 'hash_string', Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource($userId, $teamId, $videoStream, 'post_body_string');

        $this->assertTrue($this->PostDraftService->isPreparedToPost($postDraft['id']));
    }

    public function test_isPreparedToPost_false()
    {
        $userId = 1;
        $teamId = 1;
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, 'hash_string', Enum\Video\VideoTranscodeStatus::TRANSCODING());
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource($userId, $teamId, $videoStream, 'post_body_string');

        $this->assertFalse($this->PostDraftService->isPreparedToPost($postDraft['id']));
    }
}
