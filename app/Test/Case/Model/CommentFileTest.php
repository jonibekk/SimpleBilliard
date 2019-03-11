<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CommentFile', 'Model');
App::import('Model/Entity', 'CommentFile');

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

    public function test_getAllCommentFile_success()
    {
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');

        $result = $CommentFile->getAllCommentFiles(1);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof CommentFileEntity);

        $result = $CommentFile->getAllCommentFiles(2);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof CommentFileEntity);

        $result = $CommentFile->getAllCommentFiles(3);
        $this->assertEmpty($result);

        $this->createCommentFile(4,1,1, 4);
        $result = $CommentFile->getAllCommentFiles(4);
        $this->assertCount(4, $result);

    }

}
