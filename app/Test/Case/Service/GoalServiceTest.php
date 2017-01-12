<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'GoalService');

/**
 * GoalServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property GoalService  $GoalService
 * @property Team         $Team
 * @property EvaluateTerm $EvaluateTerm
 */
class GoalServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.evaluate_term',
        'app.team',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalService = ClassRegistry::init('GoalService');
        $this->Team = ClassRegistry::init('Team');
        $this->EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $this->_setDefault();
    }

    function testGoalValidateFields()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testCacheList()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGet()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testExtend()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testUpdate()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testCreate()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testBuildUpdateGoalData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testBuildUpdateTkrData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_validateSave()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_isGoalAfterCurrentTerm()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processGoals()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getProgress()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extendTermType()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getTermType()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_canExchangeTkr()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    /**
     * グラフテスト
     * ゴールの進捗データの信憑性は可能であればやる
     * メインはグラフの描画範囲の検査
     */
    //mustでやる
    function testGetGraphRangeTargetDaysOver()
    {
        $this->_setUpGraphDefault();
        try {
            $this->GoalService->getGraphRange(time(), date('t') + 1);
        } catch (Exception $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testGetGraphRangeTargetDaysNotOver()
    {
        $this->_setUpGraphDefault();
        try {
            $this->GoalService->getGraphRange(time(), date('t'));
        } catch (Exception $e) {
        }
        $this->assertFalse(isset($e));
    }

    function testGetGraphRangeTargetStartUnderTermStart()
    {
        $this->_setUpGraphDefault();
        $expected = array(
            'graphStartDate'  => date('Y-m-01'),
            'graphEndDate'    => date('Y-m-10'),
            'plotDataEndDate' => null
        );
        $targetEndTime = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTime, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $targetEndTime = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTime, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $targetEndTime = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 10 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTime, $targetDays = 10, $maxBufferDays = 0);
        $this->assertNotEquals($expected, $actual);
    }

    //余裕があればやる
    function testGetAllMyProgressForDrawingGraph()
    {
        $this->assertTrue(false);
    }

    //余裕があればやる
    function testFindLatestTotalGoalProgress()
    {
        $this->assertTrue(false);
    }

    //余裕があればやる
    function testFindSummarizedGoalProgressesFromLog()
    {
        $this->assertTrue(false);
    }

    //余裕があればやる
    function testSumDailyGoalProgress()
    {
        $this->assertTrue(false);

    }

    //余裕があればやる
    function testSumGoalProgress()
    {
        $this->assertTrue(false);

    }

    //mustでやる
    function testGetSweetSpot()
    {
        $this->assertTrue(false);

    }

    //余裕あればやる
    function testGetProgressFromCache()
    {
        $this->assertTrue(false);

    }

    //余裕あればやる
    function testWriteProgressToCache()
    {
        $this->assertTrue(false);

    }

    function _setUpGraphDefault()
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
    }

    function _setDefault($teamId = 1, $uid = 1)
    {
        $this->Team->current_team_id = $teamId;
        $this->Team->my_uid = $uid;
        $this->EvaluateTerm->current_team_id = $teamId;
        $this->EvaluateTerm->my_uid = $uid;
    }

}
