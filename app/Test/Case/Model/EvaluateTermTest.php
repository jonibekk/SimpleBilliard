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
        $this->_setDefault();
        $res = $this->EvaluateTerm->saveCurrentTerm();
        $this->assertNotEmpty($res);
    }

    function testChangeFreezeStatusCaseFrozen()
    {
        $this->_setDefault();
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
        $this->_setDefault();
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
        $this->_setDefault();
        $this->EvaluateTerm->saveCurrentTerm();
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FROZEN];
        $this->EvaluateTerm->save($frozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, true);
    }

    function testCheckFrozenEvaluateTermCaseNotFrozen()
    {
        $this->_setDefault();
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

    function test_CheckType()
    {
        $method = new ReflectionMethod($this->EvaluateTerm, '_CheckType');
        $method->setAccessible(true);
        try {
            $method->invoke($this->EvaluateTerm, 4);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));

        $res = $method->invoke($this->EvaluateTerm, EvaluateTerm::TYPE_CURRENT);
        $this->assertTrue($res);
    }

    function testGetTermData()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);

        $current_1 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotEmpty($current_1);
        $current_2 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotEmpty($current_2);
        $next_1 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertNotEmpty($next_1);
        $next_2 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertNotEmpty($next_2);
        $this->EvaluateTerm->create();
        $this->EvaluateTerm->save([
                                      'start_date' => $current_2['start_date'] - 2678400,
                                      'end_date'   => $current_2['start_date'] - 1,
                                      'team_id'    => 1,
                                      'timezone'   => 9
                                  ]);

        $previous1 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous1);
        $previous2 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous2);
    }

    function testGetTermId()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotNull($this->EvaluateTerm->getTermId(EvaluateTerm::TYPE_CURRENT));
    }

    function testAddTermDataPrevious()
    {
        $this->_setDefault();
        $this->assertFalse($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS));
    }

    function testAddTermDataCurrentAlready()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertFalse($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT));
    }

    function testAddTermDataCurrentNew()
    {
        $this->_setDefault();
        $this->assertNotEmpty($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT));
    }

    function testAddTermDataNextAlready()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertFalse($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT));
    }

    function testAddTermDataNextNew()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotEmpty($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT));
    }

    function testAddTermDataNextNotExistsCurrent()
    {
        $this->_setDefault();
        $this->EvaluateTerm->deleteAll(['team_id' => 1], false);
        $this->assertFalse($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT));
    }

    function testUpdateTermDataPrevious()
    {
        $this->_setDefault();
        //previous
        $previous = $this->EvaluateTerm->updateTermData(1, EvaluateTerm::TYPE_PREVIOUS, 1, 1, 9);
        $this->assertFalse($previous);
    }

    function testUpdateTermDataCurrentNoPrevious()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $res = $this->EvaluateTerm->updateTermData(
            $this->EvaluateTerm->getLastInsertID(),
            EvaluateTerm::TYPE_CURRENT, 1, 1, 9
        );
        $this->assertNotEmpty($res);
    }

    function testUpdateTermDataCurrentWithPrevious()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $current_2 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->create();
        $this->EvaluateTerm->save([
                                      'start_date' => $current_2['start_date'] - 2678400,
                                      'end_date'   => $current_2['start_date'] - 1,
                                      'team_id'    => 1,
                                      'timezone'   => 9
                                  ]);

        $res = $this->EvaluateTerm->updateTermData(
            $this->EvaluateTerm->getLastInsertID(),
            EvaluateTerm::TYPE_CURRENT, 1, 1, 9
        );
        $this->assertNotEmpty($res);
    }

    function testUpdateTermDataNextNoCurrent()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $current_id = $this->EvaluateTerm->getLastInsertID();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $next_id = $this->EvaluateTerm->getLastInsertID();

        $this->EvaluateTerm->delete($current_id);
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_CURRENT);

        $res = $this->EvaluateTerm->updateTermData(
            $next_id,
            EvaluateTerm::TYPE_NEXT, 1, 1, 9
        );
        $this->assertFalse($res);
    }

    function testResetTermProperty()
    {
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_NEXT);
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_PREVIOUS);
    }

    function testUpdateTermDataNextWithCurrent()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $res = $this->EvaluateTerm->updateTermData(
            $this->EvaluateTerm->getLastInsertID(),
            EvaluateTerm::TYPE_NEXT, 1, 1, 9
        );
        $this->assertNotEmpty($res);
    }

    function test_getNewStartAndEndDate()
    {
        $m = new ReflectionMethod($this->EvaluateTerm, '_getNewStartAndEndDate');
        $m->setAccessible(true);

        //no team
        $this->assertNull($m->invoke($this->EvaluateTerm));

        $this->_setDefault();
        $timezone = 9;

        ////期間半年
        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 1, 6, $timezone);
        $this->assertEquals('2014/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/06/30 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 12, 6, $timezone);
        $this->assertEquals('2013/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/05/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 1, 6, $timezone);
        $this->assertEquals('2014/07/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 12, 6, $timezone);
        $this->assertEquals('2014/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2015/05/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/01/01'), 1, 6, $timezone);
        $this->assertEquals('2016/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/06/30 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/12/31'), 1, 6, $timezone);
        $this->assertEquals('2016/07/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/29'), 3, 6, $timezone);
        $this->assertEquals('2015/09/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/28'), 3, 6, $timezone);
        $this->assertEquals('2015/09/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/3/1'), 3, 6, $timezone);
        $this->assertEquals('2016/03/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/08/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));
        ////期間四半期
        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 1, 3, $timezone);
        $this->assertEquals('2014/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/03/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 12, 3, $timezone);
        $this->assertEquals('2013/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/02/28 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 1, 3, $timezone);
        $this->assertEquals('2014/10/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 12, 3, $timezone);
        $this->assertEquals('2014/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2015/02/28 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/01/01'), 1, 3, $timezone);
        $this->assertEquals('2016/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/03/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/12/31'), 1, 3, $timezone);
        $this->assertEquals('2016/10/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/29'), 3, 3, $timezone);
        $this->assertEquals('2015/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/28'), 3, 3, $timezone);
        $this->assertEquals('2015/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/3/1'), 3, 3, $timezone);
        $this->assertEquals('2016/03/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/05/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));
        ////期間１年
        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 1, 12, $timezone);
        $this->assertEquals('2014/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/1/1'), 12, 12, $timezone);
        $this->assertEquals('2013/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/11/30 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 1, 12, $timezone);
        $this->assertEquals('2014/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2014/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2014/12/31'), 12, 12, $timezone);
        $this->assertEquals('2014/12/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2015/11/30 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/01/01'), 1, 12, $timezone);
        $this->assertEquals('2016/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/12/31'), 1, 12, $timezone);
        $this->assertEquals('2016/01/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/12/31 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/29'), 3, 12, $timezone);
        $this->assertEquals('2015/03/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/2/28'), 3, 12, $timezone);
        $this->assertEquals('2015/03/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2016/02/29 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));

        $res = $m->invoke($this->EvaluateTerm, strtotime('2016/3/1'), 3, 12, $timezone);
        $this->assertEquals('2016/03/01 00:00:00', date('Y/m/d H:i:s', $res['start'] + $timezone * 3600));
        $this->assertEquals('2017/02/28 23:59:59', date('Y/m/d H:i:s', $res['end'] + $timezone * 3600));
    }

    function test_getTermByDatetime()
    {
        $this->_setDefault();
        $this->EvaluateTerm->save(['start_date' => 1, 'end_date' => 100, 'team_id' => 1, 'timezone' => 9]);
        $m = new ReflectionMethod($this->EvaluateTerm, '_getTermByDatetime');
        $m->setAccessible(true);
        $actual = $m->invoke($this->EvaluateTerm, 50);
        $this->assertEquals(1, $actual['start_date']);
        $this->assertEquals(100, $actual['end_date']);
    }

    function _setDefault()
    {
        $this->EvaluateTerm->current_team_id = 1;
        $this->EvaluateTerm->my_uid = 1;
        $this->EvaluateTerm->Team->current_team_id = 1;
        $this->EvaluateTerm->Team->my_uid = 1;
    }

}
