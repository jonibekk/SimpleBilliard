<?php
App::uses('CommentMention', 'Model');

/**
 * CommentMention Test Case
 *
 * @property mixed CommentMention
 */
class CommentMentionTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_mention',
        'app.post',
        'app.user', 'app.notify_setting',
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
        $this->CommentMention = ClassRegistry::init('CommentMention');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentMention);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
