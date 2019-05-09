<?php App::uses('GoalousTestCase', 'Test');
App::uses('Term', 'Model');
App::uses('AppUtil', 'Util');

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
        $this->Term = ClassRegistry::init('Term');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Term);
        parent::tearDown();
    }

    function _setDefault()
    {
        $this->Term->current_team_id = 1;
        $this->Term->my_uid = 1;
        $this->Term->Team->current_team_id = 1;
        $this->Term->Team->my_uid = 1;
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
        $method = new ReflectionMethod($this->Term, '_CheckType');
        $method->setAccessible(true);
        try {
            $method->invoke($this->Term, 4);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));

        $res = $method->invoke($this->Term, Term::TYPE_CURRENT);
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
            'start_date' => AppUtil::dateYmd(strtotime("{$current_2['start_date']} -31 days")),
            'end_date'   => AppUtil::dateYmd(strtotime("{$current_2['start_date']} -1 day")),
            'team_id'    => 1,
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
        $ret = $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->assertNotEmpty($ret);
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
        $res = $this->Term->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1);
        $this->assertCount(2, $res);
    }

    function testGetSaveDataBeforeUpdateFromNext()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->getSaveDataBeforeUpdate(Team::OPTION_CHANGE_TERM_FROM_NEXT, 1, 1);
        $this->assertCount(1, $res);
    }

    function testUpdateTermData()
    {
        $this->_setDefault();
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $res = $this->Term->updateTermData(Team::OPTION_CHANGE_TERM_FROM_CURRENT, 1, 1);
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
    
    function test_GetPreviousTermDataMore()
    {
        $this->_setDefault();
        $this->assertEmpty($this->Term->getPreviousTermData());
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->Term->create();
        $this->Term->set(array(
            'team_id' => $this->Term->current_team_id,
            'start_date' => '2010-01-01',
            'end_date' => '2010-12-31'
        ));
        $this->Term->save();
        $previous = $this->Term->getPreviousTermData();
        $more = $this->Term->getPreviousTermDataMore($previous);
        $this->assertNotEmpty($more);
        $this->assertEquals('2010-01-01', $more[0]['start_date']);
        $this->assertEquals('2010-12-31', $more[0]['end_date']);
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
        $m = new ReflectionMethod($this->Term, '_getStartEndWithoutExistsData');
        $m->setAccessible(true);

        //no team
        $this->assertNull($m->invoke($this->Term, ""));

        $this->_setDefault();

        ////期間半年
        $res = $m->invoke($this->Term, '2014-01-01', 1, 6);
        $this->assertEquals('2014-01-01', $res['start']);
        $this->assertEquals('2014-06-30', $res['end']);

        $res = $m->invoke($this->Term, '2014-01-01', 12, 6);
        $this->assertEquals('2013-12-01', $res['start']);
        $this->assertEquals('2014-05-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 1, 6);
        $this->assertEquals('2014-07-01', $res['start']);
        $this->assertEquals('2014-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 12, 6);
        $this->assertEquals('2014-12-01', $res['start']);
        $this->assertEquals('2015-05-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-01-01', 1, 6);
        $this->assertEquals('2016-01-01', $res['start']);
        $this->assertEquals('2016-06-30', $res['end']);

        $res = $m->invoke($this->Term, '2016-12-31', 1, 6);
        $this->assertEquals('2016-07-01', $res['start']);
        $this->assertEquals('2016-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-02-29', 3, 6);
        $this->assertEquals('2015-09-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-02-28', 3, 6);
        $this->assertEquals('2015-09-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-3-1', 3, 6);
        $this->assertEquals('2016-03-01', $res['start']);
        $this->assertEquals('2016-08-31', $res['end']);
        ////期間四半期
        $res = $m->invoke($this->Term, '2014-1-1', 1, 3);
        $this->assertEquals('2014-01-01', $res['start']);
        $this->assertEquals('2014-03-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-1-1', 12, 3);
        $this->assertEquals('2013-12-01', $res['start']);
        $this->assertEquals('2014-02-28', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 1, 3);
        $this->assertEquals('2014-10-01', $res['start']);
        $this->assertEquals('2014-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 12, 3);
        $this->assertEquals('2014-12-01', $res['start']);
        $this->assertEquals('2015-02-28', $res['end']);

        $res = $m->invoke($this->Term, '2016-01-01', 1, 3);
        $this->assertEquals('2016-01-01', $res['start']);
        $this->assertEquals('2016-03-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-12-31', 1, 3);
        $this->assertEquals('2016-10-01', $res['start']);
        $this->assertEquals('2016-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-2-29', 3, 3);
        $this->assertEquals('2015-12-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-2-28', 3, 3);
        $this->assertEquals('2015-12-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-3-1', 3, 3);
        $this->assertEquals('2016-03-01', $res['start']);
        $this->assertEquals('2016-05-31', $res['end']);
        ////期間１年
        $res = $m->invoke($this->Term, '2014-1-1', 1, 12);
        $this->assertEquals('2014-01-01', $res['start']);
        $this->assertEquals('2014-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-1-1', 12, 12);
        $this->assertEquals('2013-12-01', $res['start']);
        $this->assertEquals('2014-11-30', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 1, 12);
        $this->assertEquals('2014-01-01', $res['start']);
        $this->assertEquals('2014-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2014-12-31', 12, 12);
        $this->assertEquals('2014-12-01', $res['start']);
        $this->assertEquals('2015-11-30', $res['end']);

        $res = $m->invoke($this->Term, '2016-01-01', 1, 12);
        $this->assertEquals('2016-01-01', $res['start']);
        $this->assertEquals('2016-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-12-31', 1, 12);
        $this->assertEquals('2016-01-01', $res['start']);
        $this->assertEquals('2016-12-31', $res['end']);

        $res = $m->invoke($this->Term, '2016-2-29', 3, 12);
        $this->assertEquals('2015-03-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-2-28', 3, 12);
        $this->assertEquals('2015-03-01', $res['start']);
        $this->assertEquals('2016-02-29', $res['end']);

        $res = $m->invoke($this->Term, '2016-3-1', 3, 12);
        $this->assertEquals('2016-03-01', $res['start']);
        $this->assertEquals('2017-02-28', $res['end']);
    }

    function test_getTermDataByDate()
    {
        $this->_setDefault();
        $this->Term->save(['start_date' => '2017-01-01', 'end_date' => '2017-03-31', 'team_id' => 1, 'timezone' => 9]);
        $actual = $this->Term->getTermDataByDate('2017-02-01');
        $this->assertEquals('2017-01-01', $actual['start_date']);
        $this->assertEquals('2017-03-31', $actual['end_date']);
    }

    function test_updateCurrentEnd()
    {
        $this->_setDefault();
        $currentTerm = $this->saveTerm(1, date('Y-m-d') , 6, false);
        $this->Term->current_team_id = $currentTerm['team_id'];
        $endDate = date('Y-m-d', strtotime('+1 month'));
        $this->Term->updateCurrentEnd($endDate);
        $resEndDate = $this->Term->getById($this->Term->getCurrentTermId())['end_date'];
        $this->assertEquals($resEndDate, $endDate);
    }

    function test_updateRange()
    {
        $this->_setDefault();
        $term = $this->saveTerm(1, '2017-04-01', 6, false);
        $this->Term->updateRange($term['id'], '2017-10-01', '2017-12-31');
        $res = $this->Term->getById($term['id']);
        $this->assertEquals($res['start_date'], '2017-10-01');
        $this->assertEquals($res['end_date'], '2017-12-31');
    }

    function test_customValidNextStartDateInSignup(){
        $this->_setDefault();

        // previous month
        $res = $this->Term->customValidNextStartDateInSignup([
            'next_start_ym' => date('Y-m', strtotime('-1 month'))
        ]);
        $this->assertFalse($res);

        // this month
        $res = $this->Term->customValidNextStartDateInSignup([
            'next_start_ym' => date('Y-m')
        ]);
        $this->assertFalse($res);

        // next month
        $res = $this->Term->customValidNextStartDateInSignup([
            'next_start_ym' => date('Y-m', strtotime('+1 month'))
        ]);
        $this->assertTrue($res);

        // after 12 month
        $res = $this->Term->customValidNextStartDateInSignup([
            'next_start_ym' => date('Y-m', strtotime('+12 month'))
        ]);
        $this->assertTrue($res);

        // after 13 month
        $res = $this->Term->customValidNextStartDateInSignup([
            'next_start_ym' => date('Y-m', strtotime('+13 month'))
        ]);
        $this->assertFalse($res);
    }

    function test_createInitialDataAsSignup()
    {
        $curretStartDate = '2017-02-01';
        $nextStartDate = '2017-05-01';
        $termRange = 6;
        $teamId = 1;

        $this->Term->createInitialDataAsSignup($curretStartDate, $nextStartDate, $termRange, 1);

        // current term
        $currentTerm = $this->Term->find('first', ['conditions' => [
            'start_date' => '2017-02-01',
            'end_date'   => date('Y-m-t', strtotime('2017-04')),
            'team_id'    => $teamId
        ]]);
        $this->assertTrue(!empty($currentTerm));

        // next term
        $nextTerm = $this->Term->find('first', ['conditions' => [
            'start_date' => $nextStartDate,
            'end_date'   => date('Y-m-t', strtotime('2017-10')),
            'team_id'    => $teamId
        ]]);
        $this->assertTrue(!empty($nextTerm));

        // next next term
        $nextNextTerm = $this->Term->find('first', ['conditions' => [
            'start_date' => date('Y-m-01', strtotime('2017-11')),
            'end_date'   => date('Y-m-t', strtotime('2018-04')),
            'team_id'    => $teamId
        ]]);
        $this->assertTrue(!empty($nextNextTerm));
    }

    function test_getTermByDate()
    {
        $teamId = 1;
        $this->Term->saveAll([
            [
                'team_id'    => $teamId,
                'start_date' => '2019-04-01',
                'end_date'   => '2019-09-31'
            ],
            [
                'team_id'    => $teamId,
                'start_date' => '2019-10-01',
                'end_date'   => '2020-03-31'
            ],
        ]);
        $res = $this->Term->getTermByDate($teamId, '2019-04-01');
        $this->assertNotEmpty($res);
        $this->assertEquals($res['start_date'], '2019-04-01');
        $this->assertEquals($res['end_date'], '2019-09-31');

        $res = $this->Term->getTermByDate($teamId, '2019-09-31');
        $this->assertNotEmpty($res);
        $this->assertEquals($res['start_date'], '2019-04-01');
        $this->assertEquals($res['end_date'], '2019-09-31');

        $res = $this->Term->getTermByDate($teamId, '2019-10-01');
        $this->assertNotEmpty($res);
        $this->assertEquals($res['start_date'], '2019-10-01');
        $this->assertEquals($res['end_date'], '2020-03-31');

        $res = $this->Term->getTermByDate($teamId, '2020-03-31');
        $this->assertNotEmpty($res);
        $this->assertEquals($res['start_date'], '2019-10-01');
        $this->assertEquals($res['end_date'], '2020-03-31');
    }

}
