<?php
App::uses('ActionResult', 'Model');

/**
 * ActionResult Test Case
 *
 * @property ActionResult $ActionResult
 */
class ActionResultTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.purpose',
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
        'app.message',
        'app.action'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResult = ClassRegistry::init('ActionResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResult);

        parent::tearDown();
    }

    function testGetCount()
    {
        $this->_setDefault();
        $this->ActionResult->getCount('me', 1, 1000000000);
        $this->ActionResult->getCount('xxxx', 1, 1000000000);
    }

    function _setDefault()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 1;
    }

}
