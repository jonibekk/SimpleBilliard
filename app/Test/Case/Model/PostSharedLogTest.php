<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostSharedLog', 'Model');

/**
 * PostSharedLog Test Case
 *
 * @property PostSharedLog $PostSharedLog
 */
class PostSharedLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_shared_log',
        'app.post',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostSharedLog = ClassRegistry::init('PostSharedLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostSharedLog);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
