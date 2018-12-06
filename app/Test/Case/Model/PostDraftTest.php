<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PostDraft', 'Model');
App::uses('TestPostDraftTrait', 'Test/Trait');

use Goalous\Enum as Enum;

/**
 * PostDraft Test Case
 */
class PostDraftTest extends GoalousTestCase
{
    use TestPostDraftTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_draft',
        'app.post_resource',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        /** @var PostDraft $PostDraft */
        $this->PostDraft = ClassRegistry::init('PostDraft');
        /** @var PostResource $PostResource */
        $this->PostResource = ClassRegistry::init('PostResource');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    function test_getFirstByResourceTypeAndResourceId()
    {
        $videoStreamId = 10;
        $videoStreamId2 = 11;
        $this->createPostDraftWithVideoStreamResource($userId = 1, $teamId = 1, $videoStream = ['id' => $videoStreamId], $bodyText = 'text');
        list($postDraft, $postResource) =
            $this->createPostDraftWithVideoStreamResource($userId = 2, $teamId = 1, $videoStream = ['id' => $videoStreamId2], $bodyText = 'text2');
        $this->createPostDraftWithVideoStreamResource($userId = 2, $teamId = 1, $videoStream = ['id' => $videoStreamId2], $bodyText = 'text2');

        $firstPostDraft = $this->PostDraft->getFirstByResourceTypeAndResourceId(Enum\Model\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId2);
        $this->assertEquals($firstPostDraft['id'], $postDraft['id']);
    }

    function test_getByResourceTypeAndResourceId()
    {
        $videoStreamId = 10;
        $videoStreamId2 = 11;
        $this->createPostDraftWithVideoStreamResource($userId = 1, $teamId = 1, $videoStream = ['id' => $videoStreamId], $bodyText = 'text');
        $this->createPostDraftWithVideoStreamResource($userId = 2, $teamId = 1, $videoStream = ['id' => $videoStreamId2], $bodyText = 'text2');
        $this->createPostDraftWithVideoStreamResource($userId = 2, $teamId = 1, $videoStream = ['id' => $videoStreamId2], $bodyText = 'text2');

        $postDrafts = $this->PostDraft->getByResourceTypeAndResourceId(Enum\Model\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId2);
        $this->assertEquals(2, count($postDrafts));
    }
}
