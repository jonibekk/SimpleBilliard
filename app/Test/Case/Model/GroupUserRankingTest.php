<?php
App::uses('GroupUserRanking', 'Model');

/**
 * GroupUserRanking Test Case
 *
 * @property GroupUserRanking $GroupUserRanking
 */
class GroupUserRankingTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group_user_ranking',
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
        'app.action_result_file',
        'app.attached_file',
        'app.comment_file',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.post_file',
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
        'app.group_vision',
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluation_setting',
        'app.team_vision'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GroupUserRanking = ClassRegistry::init('GroupUserRanking');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupUserRanking);

        parent::tearDown();
    }

    function testDummy()
    {

    }
}
