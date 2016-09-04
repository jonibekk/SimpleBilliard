<?php
App::uses('GoalClearEvaluate', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * GoalClearEvaluate Test Case
 * @property GoalClearEvaluate $GoalClearEvaluate
 */
class GoalClearEvaluateTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_clear_evaluate',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.action_result_file',
        'app.attached_file',
        'app.comment_file',
        'app.post_file',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.post_share_user',
        'app.post_share_circle',
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
        'app.member_group',
        'app.group',
        'app.group_vision',
        'app.recovery_code',
        'app.device',
        'app.invite',
        'app.thread',
        'app.evaluation_setting',
        'app.team_vision',
        'app.team_insight',
        'app.group_insight',
        'app.circle_insight',
        'app.access_user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalClearEvaluate = ClassRegistry::init('GoalClearEvaluate');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalClearEvaluate);

        parent::tearDown();
    }
    function testDummy()
    {

    }

}
