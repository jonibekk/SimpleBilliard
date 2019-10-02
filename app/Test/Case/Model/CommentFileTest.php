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

    public function test_findMaxOrderOfComment()
    {
        // No exist comment file
        $res = $this->CommentFile->findMaxOrderOfComment(999);
        $this->assertEquals($res, -1);

        // Exist one
        $res = $this->CommentFile->findMaxOrderOfComment(1);
        $this->assertEquals($res, 0);

        // Exist multiple
        $this->CommentFile->saveAll([
            [
                'comment_id'       => 3,
                'attached_file_id' => 100,
                'team_id'          => 1,
                'index_num'        => 0,
            ],
            [
                'comment_id'       => 3,
                'attached_file_id' => 101,
                'team_id'          => 1,
                'index_num'        => 1,
            ],
            [
                'comment_id'       => 3,
                'attached_file_id' => 102,
                'team_id'          => 1,
                'index_num'        => 2,
            ]
        ], ['validate' => false]);
        $res = $this->CommentFile->findMaxOrderOfComment(3);
        $this->assertEquals($res, 2);
    }

}
