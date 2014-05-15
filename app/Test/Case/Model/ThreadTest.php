<?php
App::uses('Thread', 'Model');

/**
 * Thread Test Case
 *
 * @property mixed Thread
 */
class ThreadTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.thread',
        'app.team',
        'app.image',
        'app.user',
        'app.badge',
        'app.post',
        //'app.goal',
        'app.comment_mention',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.posts_image',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.message'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Thread = ClassRegistry::init('Thread');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Thread);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
