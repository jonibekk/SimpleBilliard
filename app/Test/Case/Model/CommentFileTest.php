<?php App::uses('GoalousTestCase', 'Test');
App::uses('CommentFile', 'Model');

/**
 * CommentFile Test Case
 *
 * @property CommentFile $CommentFile
 */
class CommentFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_file',
        'app.comment',
        'app.post',
        'app.user',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.comment_like',
        'app.comment_mention',
        'app.comment_read',
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
        'app.evaluator',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.goal',

        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation_setting',
        'app.team_vision',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.post_share_user',
        'app.attached_file',
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
        $this->CommentFile = ClassRegistry::init('CommentFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentFile);

        parent::tearDown();
    }

    function testDummy()
    {
    }

}
