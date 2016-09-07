<?php App::uses('GoalousTestCase', 'Test');
App::uses('ActionResultFile', 'Model');

/**
 * ActionResultFile Test Case
 *
 * @property ActionResultFile $ActionResultFile
 */
class ActionResultFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result_file',
        'app.action_result',
        'app.goal',

        'app.user',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.post',
        'app.key_result',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.post_share_user',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.group',
        'app.member_group',
        'app.group_vision',
        'app.invite',
        'app.job_category',
        'app.team_member',
        'app.member_type',
        'app.evaluator',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.evaluation_setting',
        'app.team_vision',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.collaborator',
        'app.goal_category',
        'app.approval_history',
        'app.follower',
        'app.attached_file',
        'app.comment_file',
        'app.post_file'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResultFile = ClassRegistry::init('ActionResultFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResultFile);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
