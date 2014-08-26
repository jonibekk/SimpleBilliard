<?php
App::uses('NotifyFromUser', 'Model');

/**
 * NotifyFromUser Test Case
 *
 * @property NotifyFromUser $NotifyFromUser
 */
class NotifyFromUserTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.notify_from_user',
        'app.notification',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
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
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.thread',
        'app.message',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.notify_to_user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->NotifyFromUser = ClassRegistry::init('NotifyFromUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NotifyFromUser);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
