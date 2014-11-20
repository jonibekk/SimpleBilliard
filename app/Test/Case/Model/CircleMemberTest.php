<?php
App::uses('CircleMember', 'Model');

/**
 * CircleMember Test Case
 *
 * @property CircleMember $CircleMember
 */
class CircleMemberTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle_member',
        'app.circle',
        'app.team',
        'app.badge',
        'app.user', 'app.notify_setting',
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
        $this->CircleMember = ClassRegistry::init('CircleMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CircleMember);

        parent::tearDown();
    }

    function _setDefault($uid, $team_id)
    {
        $this->CircleMember->my_uid = $uid;
        $this->CircleMember->current_team_id = $team_id;
        $this->CircleMember->Circle->my_uid = $uid;
        $this->CircleMember->Circle->current_team_id = $team_id;
    }

    function testGetMemberList()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getMemberList(1, true);
        $this->CircleMember->getMemberList(1, true, false);
    }

    function testGetAdminMemberList()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getAdminMemberList(1);
        $this->CircleMember->getAdminMemberList(1, true);
    }

    function testGetMemberListNotWithMe()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getMemberList(1, false);
    }

    public function testGetCircleInitMemberSelect2()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getCircleInitMemberSelect2(1, true);
    }

    public function testIncrementUnreadCount()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->incrementUnreadCount([]);
    }
}
