<?php App::uses('GoalousTestCase', 'Test');
App::uses('CommentFile', 'Model');

/**
 * CommentFile Test Case
 *
 * @property CommentFile $CommentFile
 */
class CommentFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_file',
        'app.comment',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CommentFile = ClassRegistry::init('CommentFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentFile);

        parent::tearDown();
    }

    function testDummy()
    {
    }

}
