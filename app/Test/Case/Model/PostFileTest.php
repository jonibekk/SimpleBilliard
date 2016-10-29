<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostFile', 'Model');

/**
 * PostFile Test Case
 *
 * @property PostFile $PostFile
 */
class PostFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_file',
        'app.post',
        'app.attached_file',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostFile = ClassRegistry::init('PostFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostFile);

        parent::tearDown();
    }

    function testDummy()
    {
    }

}
