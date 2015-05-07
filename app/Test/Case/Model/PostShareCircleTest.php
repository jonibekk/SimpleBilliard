<?php
App::uses('PostShareCircle', 'Model');

/**
 * PostShareCircle Test Case
 *
 * @property PostShareCircle $PostShareCircle
 */
class PostShareCircleTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_share_circle',
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
        $this->PostShareCircle = ClassRegistry::init('PostShareCircle');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostShareCircle);

        parent::tearDown();
    }

    public function testAdd()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->add(1, []);
        $this->PostShareCircle->add(1, [1]);
    }

    public function testGetShareCircleMemberList()
    {
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->getShareCircleMemberList(1);
    }

    public function testIsMyCirclePost()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->Circle->CircleMember->my_uid = 1;
        $this->PostShareCircle->Circle->CircleMember->current_team_id = 1;
        $this->PostShareCircle->isMyCirclePost(1);
    }

}
