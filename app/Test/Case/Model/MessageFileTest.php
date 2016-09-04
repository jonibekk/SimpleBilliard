<?php
App::uses('MessageFile', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * MessageFile Test Case
 *
 * @property MessageFile $MessageFile
 */
class MessageFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.message_file',
        'app.topic',
        'app.message',
        'app.attached_file',
        'app.user',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.post',
        'app.goal',

        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.action_result_file',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.comment_file',
        'app.post_share_user',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.post_file',
        'app.group',
        'app.member_group',
        'app.group_vision',
        'app.invite',
        'app.job_category',
        'app.team_member',
        'app.member_type',
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
        'app.recovery_code',
        'app.device'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MessageFile = ClassRegistry::init('MessageFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MessageFile);

        parent::tearDown();
    }

    public function testDummy()
    {

    }

}
