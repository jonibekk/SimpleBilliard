<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PostDraft', 'Model');
App::uses('PostResource', 'Model');
App::uses('TestPostDraftTrait', 'Test/Trait');

use Goalous\Enum as Enum;

/**
 * PostResourceTest Test Case
 */
class PostResourceTest extends GoalousTestCase
{
    use TestPostDraftTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.post',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.post_file',
        'app.attached_file',
        'app.post_share_circle',
        'app.post_share_user',
        'app.video',
        'app.video_stream',
        'app.post_resource',
        'app.post_file',
    ];

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

    function test_getResourcesByPostId()
    {
        // TODO: write test
        $this->assertTrue(true);
    }

    function test_getResourcesByPostDraftId()
    {
        // TODO: write test
        $this->assertTrue(true);
    }

    function test_getPostDraftIdByResourceTypeAndResourceId()
    {
        // TODO: write test
        $this->assertTrue(true);
    }

    public function test_findDeletedPostResource_success(){
        //TODO: write test
    }

    public function test_getMaxResourceOrder_success(){

        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        $this->assertEquals(1, $PostResource->findMaxResourceOrderOfPost($newPostId));
    }
}
