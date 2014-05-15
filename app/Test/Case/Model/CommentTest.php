<?php
App::uses('Comment', 'Model');

/**
 * Comment Test Case
 *
 * @property mixed Comment
 */
class CommentTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment',
        'app.post',
        'app.user',
        'app.team',
        'app.comment_like',
        'app.comment_read'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Comment = ClassRegistry::init('Comment');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Comment);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
