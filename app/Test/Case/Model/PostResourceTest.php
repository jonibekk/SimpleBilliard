<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PostDraft', 'Model');
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
    public $fixtures = array(
        'app.post',
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
}
