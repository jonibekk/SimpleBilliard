<?php
App::uses('KeyResultUser', 'Model');

/**
 * KeyResultUser Test Case
 *
 * @property KeyResultUser $KeyResultUser
 */
class KeyResultUserTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.key_result_user',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.thread',
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
        $this->KeyResultUser = ClassRegistry::init('KeyResultUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KeyResultUser);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
