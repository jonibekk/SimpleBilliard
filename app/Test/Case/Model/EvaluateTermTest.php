<?php App::uses('GoalousTestCase', 'Test');
App::uses('EvaluateTerm', 'Model');

/**
 * EvaluateTerm Test Case
 *
 * @property EvaluateTerm $EvaluateTerm
 */
class EvaluateTermTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluate_term',
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

    function testGetAll()
    {
        $this->_setDefault();
        $exists = $this->EvaluateTerm->getAllTerm();
        $exists_count = count($exists);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $res = $this->EvaluateTerm->getAllTerm();
        $this->assertCount($exists_count + 3, $res);
    }

    function testIsAbleToStartEvaluation()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $res = $this->EvaluateTerm->isAbleToStartEvaluation($this->EvaluateTerm->getCurrentTermId());
        $this->assertTrue($res);
        $this->EvaluateTerm->changeToInProgress($this->EvaluateTerm->getCurrentTermId());
        $res = $this->EvaluateTerm->isAbleToStartEvaluation($this->EvaluateTerm->getCurrentTermId());
        $this->assertFalse($res);
    }

    function testChangeFreezeStatusNoData()
    {
        $this->_setDefault();
        try {
            $this->EvaluateTerm->changeFreezeStatus($this->EvaluateTerm->getCurrentTermId());

        } catch (RuntimeException $e) {
        }

        $this->assertTrue(isset($e));
    }

    function testGetNewStartEndBeforeAdd()
    {
        $this->_setDefault();
        $res = $this->EvaluateTerm->getNewStartEndBeforeAdd(1, 1, 9);
        $this->assertNotEmpty($res);
    }

    function testChangeFreezeStatusCaseFrozen()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
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
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
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
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $frozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_FROZEN];
        $this->EvaluateTerm->save($frozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
        $this->assertEquals($res, true);
    }

    function testCheckFrozenEvaluateTermCaseNotFrozen()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $latestTermId = $this->EvaluateTerm->getLastInsertID();
        $notFrozenData = ['id' => $latestTermId, 'evaluate_status' => EvaluateTerm::STATUS_EVAL_IN_PROGRESS];
        $this->EvaluateTerm->save($notFrozenData);
        $res = $this->EvaluateTerm->checkFrozenEvaluateTerm($latestTermId);
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
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_NEXT);
        $next_3 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertNotEmpty($next_3);
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
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_PREVIOUS);
        $previous3 = $this->EvaluateTerm->getTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertNotEmpty($previous3);
    }

    function testGetTermId()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotNull($this->EvaluateTerm->getTermId(EvaluateTerm::TYPE_CURRENT));
    }

    function testAddTermDataPreviousAlready()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertFalse($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS));
    }

    function testAddTermDataPreviousNew()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotEmpty($this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS));
    }

    function testAddTermDataPreviousNotExistsCurrent()
    {
        $this->_setDefault();
        $this->EvaluateTerm->deleteAll(['team_id' => 1], false);
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

    function testGetSaveDataBeforeUpdateFromCurrent()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $res = $this->EvaluateTerm->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1, 9);
        $this->assertCount(2, $res);
    }

    function testGetSaveDataBeforeUpdateFromNext()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $res = $this->EvaluateTerm->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_NEXT, 1, 1, 9);
        $this->assertCount(1, $res);
    }

    function testUpdateTermData()
    {
        $this->_setDefault();
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $res = $this->EvaluateTerm->updateTermData(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1, 9);
        $this->assertTrue($res);
    }

    function testResetTermProperty()
    {
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_NEXT);
        $this->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_PREVIOUS);
    }

    function testGetCurrentTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->EvaluateTerm->getCurrentTermData());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotEmpty($this->EvaluateTerm->getCurrentTermData());
    }

    function testGetCurrentTermDataUtcMidnight()
    {
        $this->_setDefault();
        $this->assertEmpty($this->EvaluateTerm->getCurrentTermData());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $utcMidnightTerm = $this->EvaluateTerm->getCurrentTermData(true);
        $this->assertRegExp('/00:00:00/',date('Y-m-d H:i:s',$utcMidnightTerm['start_date']));
        $this->assertRegExp('/23:59:59/',date('Y-m-d H:i:s',$utcMidnightTerm['end_date']));
    }


    function testGetNextTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->EvaluateTerm->getNextTermData());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertNotEmpty($this->EvaluateTerm->getNextTermData());
    }

    function testGetPreviousTermData()
    {
        $this->_setDefault();
        $this->assertEmpty($this->EvaluateTerm->getPreviousTermData());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertNotEmpty($this->EvaluateTerm->getPreviousTermData());
    }

    function testGetCurrentTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->EvaluateTerm->getCurrentTermId());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->assertNotNull($this->EvaluateTerm->getCurrentTermId());
    }

    function testGetNextTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->EvaluateTerm->getNextTermId());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->assertNotNull($this->EvaluateTerm->getNextTermId());
    }

    function testGetPreviousTermId()
    {
        $this->_setDefault();
        $this->assertNull($this->EvaluateTerm->getPreviousTermId());
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->assertNotNull($this->EvaluateTerm->getPreviousTermId());
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
        $this->EvaluateTerm->save(['start_date' => 1, 'end_date' => 100, 'team_id' => 1, 'timezone' => 9]);
        $actual = $this->EvaluateTerm->getTermDataByDatetime(50);
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
