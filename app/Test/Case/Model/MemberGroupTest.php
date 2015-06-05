<?php
App::uses('MemberGroup', 'Model');

/**
 * MemberGroup Test Case
 *
 * @property MemberGroup $MemberGroup
 */
class MemberGroupTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.member_group',
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
        'app.action_result',
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

        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.evaluator',
        'app.group',
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
        $this->MemberGroup = ClassRegistry::init('MemberGroup');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MemberGroup);

        parent::tearDown();
    }

    function testDummy()
    {

    }

    function testGetGroupMemberUserId()
    {
        $user_id = 999;
        $team_id = 888;
        $group_id = 777;
        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'group_id' => $group_id,
        ];
        $this->MemberGroup->save($params);
        $res = $this->MemberGroup->getGroupMemberUserId($team_id, $group_id);
        $this->assertContains($user_id, $res);
    }

}
