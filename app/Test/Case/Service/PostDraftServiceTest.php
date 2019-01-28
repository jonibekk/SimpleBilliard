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

use Goalous\Enum as Enum;

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

    public function test_createPostDraftWithResources_fail_validation()
    {
        $userId = 1;
        $teamId = 1;

        $stringLongerThanPostBodyRule = str_repeat('A', 1 + $this->Post->validate['body']['maxLength']['rule'][1]);

        $result = $this->PostDraftService->createPostDraftWithResources([
            'Post' => [
                'body' => $stringLongerThanPostBodyRule,
            ]
        ], $userId, $teamId, []);

        $this->assertFalse($result);
        $this->assertTrue(isset($this->Post->validationErrors['body'][0]));
        $this->assertTrue(is_string($this->Post->validationErrors['body'][0]));
        $this->assertTrue(0 < strlen($this->Post->validationErrors['body'][0]));
    }

    public function test_createPostDraftWithResources_rollback()
    {
        $mock = $this->getMockForModel('PostDraft', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $mock->expects($this->any())
             ->method('save')
             ->will($this->returnValue(false));

        $userId = 1;
        $teamId = 1;

        $countBefore = $this->PostDraft->find('count');
        $result = $this->PostDraftService->createPostDraftWithResources([
            'Post' => [
                'body' => 'body',
            ]
        ], $userId, $teamId, []);
        $countAfter = $this->PostDraft->find('count');
        $this->assertSame($countBefore, $countAfter);

        $this->assertFalse($result);
    }

    public function test_createPostDraftWithResources_success()
    {
        $userId = 1;
        $teamId = 1;

        $resourceId = 123;

        $postDraft = $this->PostDraftService->createPostDraftWithResources([
            'Post' => [
                'body' => 'body',
            ]
        ], $userId, $teamId, [
            // TODO: https://jira.goalous.com/browse/GL-6601
            [
                'is_video' => true,
                'video_stream_id' => $resourceId,
            ],
        ]);

        $this->assertTrue(is_numeric($postDraft['id']));
        $postResources = $this->PostResource->find('all', [
            'conditions' => [
                'post_draft_id' => $postDraft['id'],
            ],
        ]);
        $this->assertTrue(1 === count($postResources));
        $this->assertSame($resourceId, intval($postResources[0]['PostResource']['resource_id']));
    }

    public function test_isPreparedToPost_true()
    {
        $userId = 1;
        $teamId = 1;
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, 'hash_string', Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource($userId, $teamId, $videoStream, 'post_body_string');

        $this->assertTrue($this->PostDraftService->isPreparedToPost($postDraft['id']));
    }

    public function test_isPreparedToPost_false()
    {
        $userId = 1;
        $teamId = 1;
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, 'hash_string', Enum\Model\Video\VideoTranscodeStatus::TRANSCODING());
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource($userId, $teamId, $videoStream, 'post_body_string');

        $this->assertFalse($this->PostDraftService->isPreparedToPost($postDraft['id']));
    }

    public function test_getPostDraftForFeed_get()
    {
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'public'),
            $userId = 1, $teamId = 1, []);
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'circle_1'),
            $userId = 1, $teamId = 1, []);

        // below is created in another team or another user
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'circle_1'),
            1, 2, []);
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'circle_1'),
            2, 1, []);

        $postDrafts = $this->PostDraftService->getPostDraftForFeed($userId, $teamId);
        $this->assertSame(2, count($postDrafts));
    }

    public function test_getPostDraftForFeed_limitByCircleIds()
    {
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'public'),
            $userId = 1, $teamId = 1, []);
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'public,circle_1'),
            $userId = 1, $teamId = 1, []);
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'public,circle_2'),
            $userId = 1, $teamId = 1, []);
        $this->PostDraftService->createPostDraftWithResources(
            $this->createPostingArrayFormat('public', 'circle_2'),
            $userId = 1, $teamId = 1, []);

        // Get circle_1
        $postDrafts = $this->PostDraftService->getPostDraftForFeed($userId, $teamId, [1]);
        $this->assertSame(1, count($postDrafts));
        $postDraft = reset($postDrafts);
        $this->assertSame('public,circle_1', $postDraft['data']['Post']['share']);

        // Get circle_2
        $postDrafts = $this->PostDraftService->getPostDraftForFeed($userId, $teamId, ["2"]);
        $this->assertSame(2, count($postDrafts));
        list($postDraft0, $postDraft1) = $postDrafts;
        $this->assertSame('circle_2',        $postDraft0['data']['Post']['share']);
        $this->assertSame('public,circle_2', $postDraft1['data']['Post']['share']);

        // Get public, circles.id = 3 is specified as team's all circle in CircleFixture
        $postDrafts = $this->PostDraftService->getPostDraftForFeed($userId, $teamId, [3]);
        $this->assertSame(3, count($postDrafts));
    }

    /**
     * @param string $share_range should be 'public' or 'secret'
     * @param string $share 'public,circle_1,user_1'
     *
     * @return array
     */
    private function createPostingArrayFormat(string $share_range, string $share): array
    {
        if ('public' === $share_range) {
            $share_public = $share;
            $share_secret = '';
        } else {
            $share_public = 'public';
            $share_secret = $share;
        }
        return [
            'Post' => [
                'body'          => 'body',
                'site_info_url' => '',
                'redirect_url'  => '',
                'share_public'  => $share_public,
                'share_secret'  => $share_secret,
                'share_range'   => $share_range,
                'share'         => $share
            ]
        ];
    }
}
