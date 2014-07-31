<?php
App::uses('Circle', 'Model');

/**
 * Circle Test Case

 */
class CircleTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.notification',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.thread',
        'app.message',
        'app.circle_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Circle = ClassRegistry::init('Circle');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Circle);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
