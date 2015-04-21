<?php
App::uses('EvaluateTerm', 'Model');

/**
 * EvaluateTerm Test Case
 *
 * @property EvaluateTerm $EvaluateTerm
 */
class EvaluateTermTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluate_term',
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
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.member_group',
        'app.group',
        'app.evaluator',
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluation'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluateTerm = ClassRegistry::init('EvaluateTerm');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EvaluateTerm);

        parent::tearDown();
    }

    function testSaveTerm()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $res = $this->EvaluateTerm->saveTerm();
        $this->assertNotEmpty($res);
    }

    function testFreezeEvaluateTerm()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $this->EvaluateTerm->freezeEvaluateTerm($latestTermId);
        $res = $this->EvaluateTerm->findById($latestTermId);
        $this->assertEquals($res['EvaluateTerm']['evaluate_status'], EvaluateTerm::STATUS_EVAL_FINISHED);
    }

    function testCheckFrozenEvaluateTermCaseFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FINISHED];
        $this->EvaluateTerm->save($frozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, true);
    }

    function testCheckFrozenEvaluateTermCaseNotFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $notFrozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_IN_PROGRESS];
        $this->EvaluateTerm->save($notFrozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, false);
    }

    function _setDefault()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->my_uid = 1;
        $this->EvaluateTerm->Team->current_team_id = 1;
        $this->EvaluateTerm->Team->my_uid = 1;
    }

}
