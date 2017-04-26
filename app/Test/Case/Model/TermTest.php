<?php App::uses('GoalousTestCase', 'Test');
App::uses('Term', 'Model');

/**
 * Term Test Case
 *
 * @property Term $Term
 */
class TermTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.term',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluateTerm = ClassRegistry::init('Term');
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

    function testGetAll()
    {
        $this->_setDefault();
        $exists = $this->Term->getAllTerm();
        $exists_count = count($exists);
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->getAllTerm();
        $this->assertCount($exists_count + 3, $res);
    }

    function testIsAbleToStartEvaluation()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $res = $this->Term->isAbleToStartEvaluation($this->Term->getCurrentTermId());
        $this->assertTrue($res);
        $this->Term->changeToInProgress($this->Term->getCurrentTermId());
        $res = $this->Term->isAbleToStartEvaluation($this->Term->getCurrentTermId());
        $this->assertFalse($res);
    }

    function testChangeFreezeStatusNoData()
    {
        $this->_setDefault();
        try {
            $this->Term->changeFreezeStatus($this->Term->getCurrentTermId());

        } catch (RuntimeException $e) {
        }

        $this->assertTrue(isset($e));
    }

    function testGetNewStartEndBeforeAdd()
    {
        $this->_setDefault();
        $res = $this->Term->getNewStartEndBeforeAdd(1, 1, 9);
        $this->assertNotEmpty($res);
    }

    function testChangeFreezeStatusCaseFrozen()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $latestTermId = $this->Term->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => Term::STATUS_EVAL_FROZEN];
        $this->Term->save($frozenData);

        $this->Term->changeFreezeStatus($latestTermId);
        $res = $this->Term->findById($latestTermId);
        $this->assertEquals($res['Term']['evaluate_status'], Term::STATUS_EVAL_IN_PROGRESS);
    }

    function testChangeFreezeStatusCaseNotFrozen()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $latestTermId = $this->Term->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => Term::STATUS_EVAL_FROZEN];
        $this->Term->save($frozenData);

        $this->Term->changeFreezeStatus($latestTermId);
        $res = $this->Term->findById($latestTermId);
        $this->assertEquals($res['Term']['evaluate_status'], Term::STATUS_EVAL_IN_PROGRESS);
    }

    function testCheckFrozenEvaluateTermCaseFrozen()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $latestTermId = $this->Term->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => Term::STATUS_EVAL_FROZEN];
        $this->Term->save($frozenData);
        $res = $this->Term->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, true);
    }

    function testCheckFrozenEvaluateTermCaseNotFrozen()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $latestTermId = $this->Term->getLastInsertID();
        $notFrozenData = ['id' => $latestTermId, 'evaluate_status' => Term::STATUS_EVAL_IN_PROGRESS];
        $this->Term->save($notFrozenData);
        $res = $this->Term->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, false);
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

        $res = $method->invoke($this->EvaluateTerm, Term::TYPE_CURRENT);
        $this->assertTrue($res);
    }

    function testGetTermData()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);

        $current_1 = $this->Term->getTermData(Term::TYPE_CURRENT);
        $this->assertNotEmpty($current_1);
        $current_2 = $this->Term->getTermData(Term::TYPE_CURRENT);
        $this->assertNotEmpty($current_2);
        $next_1 = $this->Term->getTermData(Term::TYPE_NEXT);
        $this->assertNotEmpty($next_1);
        $next_2 = $this->Term->getTermData(Term::TYPE_NEXT);
        $this->assertNotEmpty($next_2);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $next_3 = $this->Term->getTermData(Term::TYPE_NEXT);
        $this->assertNotEmpty($next_3);
        $this->Term->create();
        $this->Term->save([
            'start_date' => $current_2['start_date'] - 2678400,
            'end_date'   => $current_2['start_date'] - 1,
            'team_id'    => 1,
            'timezone'   => 9
        ]);

        $previous1 = $this->Term->getTermData(Term::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous1);
        $previous2 = $this->Term->getTermData(Term::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous2);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);
        $previous3 = $this->Term->getTermData(Term::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous3);
    }

    function testGetTermId()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertNotNull($this->Term->getTermId(Term::TYPE_CURRENT));
    }

    function testAddTermDataPreviousAlready()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->assertFalse($this->Term->addTermData(Term::TYPE_PREVIOUS));
    }

    function testAddTermDataPreviousNew()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertNotEmpty($this->Term->addTermData(Term::TYPE_PREVIOUS));
    }

    function testAddTermDataPreviousNotExistsCurrent()
    {
        $this->_setDefault();
        $this->Term->deleteAll(['team_id' => 1], false);
        $this->assertFalse($this->Term->addTermData(Term::TYPE_PREVIOUS));
    }

    function testAddTermDataCurrentAlready()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertFalse($this->Term->addTermData(Term::TYPE_CURRENT));
    }

    function testAddTermDataCurrentNew()
    {
        $this->_setDefault();
        $this->assertNotEmpty($this->Term->addTermData(Term::TYPE_CURRENT));
    }

    function testAddTermDataNextAlready()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->assertFalse($this->Term->addTermData(Term::TYPE_NEXT));
    }

    function testAddTermDataNextNew()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertNotEmpty($this->Term->addTermData(Term::TYPE_NEXT));
    }

    function testAddTermDataNextNotExistsCurrent()
    {
        $this->_setDefault();
        $this->Term->deleteAll(['team_id' => 1], false);
        $this->assertFalse($this->Term->addTermData(Term::TYPE_NEXT));
    }

    function testGetSaveDataBeforeUpdateFromCurrent()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1, 9);
        $this->assertCount(2, $res);
    }

    function testGetSaveDataBeforeUpdateFromNext()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_NEXT, 1, 1, 9);
        $this->assertCount(1, $res);
    }

    function testUpdateTermData()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->updateTermData(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1, 9);
        $this->assertTrue($res);
    }

    function testResetTermProperty()
    {
        $this->Term->resetTermProperty(Term::TYPE_CURRENT);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);
    }

    function testGetCurrentTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->Term->getCurrentTermData());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertNotEmpty($this->Term->getCurrentTermData());
    }

    function testGetCurrentTermDataUtcMidnight()
    {
        $this->_setDefault();
        $this->assertEmpty($this->Term->getCurrentTermData());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $utcMidnightTerm = $this->Term->getCurrentTermData(true);
        $this->assertRegExp('/00:00:00/',date('Y-m-d H:i:s',$utcMidnightTerm['start_date']));
        $this->assertRegExp('/23:59:59/',date('Y-m-d H:i:s',$utcMidnightTerm['end_date']));
    }


    function testGetNextTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->Term->getNextTermData());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->assertNotEmpty($this->Term->getNextTermData());
    }

    function testGetPreviousTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->Term->getPreviousTermData());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->assertNotEmpty($this->Term->getPreviousTermData());
    }

    function testGetCurrentTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->Term->getCurrentTermId());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->assertNotNull($this->Term->getCurrentTermId());
    }

    function testGetNextTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->Term->getNextTermId());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->assertNotNull($this->Term->getNextTermId());
    }

    function testGetPreviousTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->Term->getPreviousTermId());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->assertNotNull($this->Term->getPreviousTermId());
    }

    function test_getNewStartAndEndDate()
    {
        $m = new ReflectionMethod($this->EvaluateTerm, '_getStartEndWithoutExistsData');
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

    function testGetTermByDatetime()
    {
        $this->_setDefault();
        $this->Term->save(['start_date' => 1, 'end_date' => 100, 'team_id' => 1, 'timezone' => 9]);
        $actual = $this->Term->getTermDataByTimeStamp(50);
        $this->assertEquals(1, $actual['start_date']);
        $this->assertEquals(100, $actual['end_date']);
    }

    function _setDefault()
    {
        $this->Term->current_team_id = 1;
        $this->Term->my_uid = 1;
        $this->Term->Team->current_team_id = 1;
        $this->Term->Team->my_uid = 1;
    }

}
