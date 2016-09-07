<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostSharedLog', 'Model');

/**
 * PostSharedLog Test Case
 *
 * @property PostSharedLog $PostSharedLog
 */
class PostSharedLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_shared_log',
        'app.post',
        'app.user',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.comment_like',
        'app.comment',
        'app.comment_read',
        'app.comment_file',
        'app.attached_file',
        'app.post_file',
        'app.action_result_file',
        'app.action_result',
        'app.goal',

        'app.goal_category',
        'app.key_result',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.comment_mention',
        'app.given_badge',
        'app.group',
        'app.member_group',
        'app.group_vision',
        'app.invite',
        'app.job_category',
        'app.team_member',
        'app.member_type',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.evaluation_setting',
        'app.team_vision',
        'app.team_insight',
        'app.group_insight',
        'app.circle_insight',
        'app.access_user',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.post_share_user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostSharedLog = ClassRegistry::init('PostSharedLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostSharedLog);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
