<?php
App::uses('TeamVision', 'Model');

/**
 * TeamVision Test Case
 *
 * @property TeamVision $TeamVision
 */
class TeamVisionTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_vision',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.purpose',
        'app.goal',
        'app.goal_category',
        'app.post',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.action_result',
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
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.member_group',
        'app.group',
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluation_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamVision = ClassRegistry::init('TeamVision');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamVision);

        parent::tearDown();
    }

    function testDummy()
    {
    }

}
