<?php
App::uses('CommentRead', 'Model');

/**
 * CommentRead Test Case
 *
 * @property mixed CommentRead
 */
class CommentReadTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_read',
        'app.comment',
        'app.user',
        'app.team'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CommentRead = ClassRegistry::init('CommentRead');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentRead);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
