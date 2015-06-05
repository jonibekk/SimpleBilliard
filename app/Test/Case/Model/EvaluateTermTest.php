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
        'app.evaluation_setting',
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
        $res = $this->EvaluateTerm->saveCurrentTerm();
        $this->assertNotEmpty($res);
    }

    function testChangeFreezeStatusCaseFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveCurrentTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FROZEN];
        $this->EvaluateTerm->save($frozenData);

        $this->EvaluateTerm->changeFreezeStatus($latestTermId);
        $res = $this->EvaluateTerm->findById($latestTermId);
        $this->assertEquals($res['EvaluateTerm']['evaluate_status'], EvaluateTerm::STATUS_EVAL_IN_PROGRESS);
    }

    function testChangeFreezeStatusCaseNotFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveCurrentTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FROZEN];
        $this->EvaluateTerm->save($frozenData);

        $this->EvaluateTerm->changeFreezeStatus($latestTermId);
        $res = $this->EvaluateTerm->findById($latestTermId);
        $this->assertEquals($res['EvaluateTerm']['evaluate_status'], EvaluateTerm::STATUS_EVAL_IN_PROGRESS);
    }

    function testCheckFrozenEvaluateTermCaseFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveCurrentTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FROZEN];
        $this->EvaluateTerm->save($frozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, true);
    }

    function testCheckFrozenEvaluateTermCaseNotFrozen()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->saveCurrentTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $notFrozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_IN_PROGRESS];
        $this->EvaluateTerm->save($notFrozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, false);
    }

    function testGetTermIdByDate()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveTerm(100, 1000);
        $actual = $this->EvaluateTerm->getTermIdByDate(100, 1000);
        $expected = $this->EvaluateTerm->getLastInsertID();
        $this->assertEquals($expected, $actual);
    }

    function testGetNextTerm()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveCurrentTerm();
        $res1 = $this->EvaluateTerm->saveNextTerm();
        $this->EvaluateTerm->getNextTerm();
        $res2 = $this->EvaluateTerm->getNextTerm();
        $this->assertEquals($res2['id'], $res1['EvaluateTerm']['id']);
    }

    function testGetLatestTerm()
    {
        $this->_setDefault();
        $res1 = $this->EvaluateTerm->saveCurrentTerm();
        $this->EvaluateTerm->getLatestTerm();
        $res2 = $this->EvaluateTerm->getLatestTerm();
        $this->assertEquals($res2['id'], $res1['EvaluateTerm']['id']);
    }

    function testGetPreviousTerm()
    {
        $this->_setDefault();
        $res1 = $this->EvaluateTerm->saveTerm(100, 1000);
        $this->EvaluateTerm->saveCurrentTerm();
        $this->EvaluateTerm->getPreviousTerm();
        $res2 = $this->EvaluateTerm->getPreviousTerm();
        $this->assertEquals($res2['id'], $res1['EvaluateTerm']['id']);
    }

    function testSaveNextTermExistsLatest()
    {
        $this->_setDefault();
        $res1 = $this->EvaluateTerm->saveTerm(100, 1000);
        $res2 = $this->EvaluateTerm->saveNextTerm();
        $this->assertEquals($res2['EvaluateTerm']['start_date'], $res1['EvaluateTerm']['end_date'] + 1);
    }

    function testSaveNextTermNotExistsLatest()
    {
        $this->_setDefault();
        $res1 = $this->EvaluateTerm->Team->getAfterTermStartEnd();
        $res2 = $this->EvaluateTerm->saveNextTerm();
        $this->assertEquals($res2['EvaluateTerm']['start_date'], $res1['start']);
    }

    function testGetChangeCurrentNextTermOption1()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveNextTerm();
        $res = $this->EvaluateTerm->getChangeCurrentNextTerm(1, 1, 1);
        $this->assertNotNull($res['current']['start_date']);
        $this->assertNotNull($res['next']['start_date']);
    }

    function testGetChangeCurrentNextTermOption2()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveNextTerm();
        $res = $this->EvaluateTerm->getChangeCurrentNextTerm(2, 1, 1);
        $this->assertNull($res['current']['start_date']);
        $this->assertNotNull($res['next']['start_date']);
    }

    function testSaveChangedTermSuccess1()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveCurrentTerm();
        $this->EvaluateTerm->saveNextTerm();
        $res = $this->EvaluateTerm->saveChangedTerm(1, 1, 1);
        $this->assertTrue($res);
    }

    function testSaveChangedTermFailStartedSuccess2()
    {
        $this->_setDefault();
        $this->EvaluateTerm->saveCurrentTerm();
        $this->EvaluateTerm->saveNextTerm();
        $res = $this->EvaluateTerm->saveChangedTerm(2, 1, 1);
        $this->assertTrue($res);
    }

    function _setDefault()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->my_uid = 1;
        $this->EvaluateTerm->Team->current_team_id = 1;
        $this->EvaluateTerm->Team->my_uid = 1;
    }

}
