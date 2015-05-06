<?php
App::uses('Purpose', 'Model');

/**
 * Purpose Test Case
 *
 * @property Purpose $Purpose
 */
class PurposeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.purpose',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.collaborator',
        'app.follower',
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
        'app.local_name'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Purpose = ClassRegistry::init('Purpose');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Purpose);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->_setDefault();
        $data = ['Purpose' => ['name' => 'test']];
        $res = $this->Purpose->add($data);
        $this->assertTrue(!empty($res));
    }

    function _setDefault()
    {
        $this->Purpose->my_uid = 1;
        $this->Purpose->current_team_id = 1;
    }

}
