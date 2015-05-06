<?php
App::uses('ApprovalHistory', 'Model');

/**
 * ApprovalHistory Test Case
 *
 * @property ApprovalHistory $ApprovalHistory
 */
class ApprovalHistoryTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.approval_history',
        'app.collaborator',
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
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.post_share_user',
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
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluation_setting'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApprovalHistory = ClassRegistry::init('ApprovalHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprovalHistory);

        parent::tearDown();
    }

    public function testAdd()
    {
        $cb_id = 999;
        $user_id = 888;
        $action_status = 0;
        $comment = 'test';
        $this->ApprovalHistory->add($cb_id, $user_id, $action_status, $comment);
        $res = $this->ApprovalHistory->find('first', ['conditions' => ['collaborator_id' => $cb_id]]);
        $this->assertEquals($res['ApprovalHistory']['comment'], 'test');
    }

    public function testAddEmpty()
    {
        $cb_id = 999;
        $user_id = 888;
        $action_status = 0;
        $comment = '';
        $this->ApprovalHistory->add($cb_id, $user_id, $action_status, $comment);
        $res = $this->ApprovalHistory->find('first', ['conditions' => ['collaborator_id' => $cb_id]]);
        $this->assertEmpty($res);
    }

}
