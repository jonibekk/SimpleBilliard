<?php
App::uses('PostShareUser', 'Model');

/**
 * PostShareUser Test Case
 *
 * @property PostShareUser $PostShareUser
 */
class PostShareUserTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_share_user',
        'app.post',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.comment_read',
        'app.comment_mention',
        'app.given_badge',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.circle_member',
        'app.circle'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostShareUser = ClassRegistry::init('PostShareUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostShareUser);

        parent::tearDown();
    }

    public function testAdd()
    {
        $this->PostShareUser->my_uid = 1;
        $this->PostShareUser->current_team_id = 1;
        $this->PostShareUser->add(1, []);
        $this->PostShareUser->add(1, [1]);
    }

}
