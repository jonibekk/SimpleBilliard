<?php
App::uses('Follower', 'Model');

/**
 * Follower Test Case
 *
 * @property Follower $Follower
 */
class FollowerTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.follower',
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
        'app.collabo_id',
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
        $this->Follower = ClassRegistry::init('Follower');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Follower);

        parent::tearDown();
    }

    function testAddFollow()
    {
        $this->setDefault();
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => 100
        ];
        $this->Follower->save($data);
        $this->assertFalse($this->Follower->addFollower(100));
    }

    function setDefault()
    {
        $this->Follower->my_uid = 1;
        $this->Follower->current_team_id = 1;
    }

}
