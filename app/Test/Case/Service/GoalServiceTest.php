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

    function testGetGraphRangeTargetEndIsNotLongSinceTermStart()
    {
        $this->_setUpGraphDefault();
        $expected = [
            'graphStartDate'  => date('Y-m-01'),
            'graphEndDate'    => date('Y-m-10'),
            'plotDataEndDate' => date('Y-m-10'),
        ];
        //バッファなし
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 10 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertNotEquals($expected, $actual);
        //バッファあり
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 1 * DAY;;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $this->assertNotEquals($expected, $actual);
    }

    function testGetGraphRangeTermEndIsApproachingWithBuffer()
    {
        $this->_setUpGraphDefault();

        //バッファあり
        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . date('t')),
        ];

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 1);
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 8 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $this->assertNotEquals($expected, $actual);

        //バッファなし
        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . date('t'))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 10)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 1)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 1))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 1 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 18)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 9)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 9))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);
    }

    function testGetGraphRangeNormal()
    {
        $this->_setUpGraphDefault();

        //バッファなし
        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . date('t'))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 10)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 1)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 1))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 1 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 18)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 9)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 9))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $this->assertEquals($expected, $actual);

        //バッファあり
        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 1)),
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 1 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 1);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 3)),
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 3 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 3);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 10)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 1)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 4)),
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 4 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 3);
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-02'),
            'graphEndDate'    => date('Y-m-11'),
            'plotDataEndDate' => date('Y-m-08'),
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 7 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 3);
        $this->assertEquals($expected, $actual);
    }

    //余裕があればやる
    function testGetAllMyProgressForDrawingGraph()
    {
        $this->markTestSkipped();
    }

    //余裕があればやる
    function testFindLatestTotalGoalProgress()
    {
        $this->markTestSkipped();
    }

    //余裕があればやる
    function testFindSummarizedGoalProgressesFromLog()
    {
        $this->markTestSkipped();
    }

    //余裕があればやる
    function testSumDailyGoalProgress()
    {
        $this->markTestSkipped();
    }

    //余裕があればやる
    function testSumGoalProgress()
    {
        $this->markTestSkipped();
    }

    //mustでやる
    function testGetSweetSpotValueCount()
    {
        $this->_setUpGraphDefault();
        $termStartTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];

        $startDate = date('Y-m-d', $termStartTimestamp);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $actual = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertcount((int)date('t'), $actual['top']);

        $startDate = date('Y-m-d', $termStartTimestamp + DAY);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $actual = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertcount((int)date('t') - 1, $actual['top']);

        $startDate = date('Y-m-d', $termStartTimestamp);
        $endDate = date('Y-m-d', $termEndTimestamp - DAY);
        $actual = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertcount((int)date('t') - 1, $actual['top']);

        $startDate = date('Y-m-d', $termStartTimestamp + DAY);
        $endDate = date('Y-m-d', $termEndTimestamp - DAY);
        $actual = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertcount((int)date('t') - 2, $actual['top']);
    }

    function testGetSweetSpotValue()
    {
        $this->_setUpGraphDefault();
        $termStartTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];
        $startDate = date('Y-m-d', $termStartTimestamp);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $actualFullTerm = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertEquals(0, $actualFullTerm['top'][0]);
        $this->assertEquals(0, $actualFullTerm['bottom'][0]);
        $lastKey = (int)(date('t') - 1);
        $this->assertEquals(GoalService::GRAPH_SWEET_SPOT_MAX_TOP, floor($actualFullTerm['top'][$lastKey]));
        $this->assertEquals(GoalService::GRAPH_SWEET_SPOT_MAX_BOTTOM, floor($actualFullTerm['bottom'][$lastKey]));

        $startDate = date('Y-m-d', $termStartTimestamp + DAY);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $actual = $this->GoalService->getSweetSpot($startDate, $endDate);
        $this->assertEquals($actualFullTerm['top'][1], $actual['top'][0]);
        $this->assertEquals($actualFullTerm['bottom'][1], $actual['bottom'][0]);
    }

    function testGetSweetSpotInTermOrNot()
    {
        $this->_setUpGraphDefault();
        $termStartTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];

        $startDate = date('Y-m-d', $termStartTimestamp - DAY);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $this->assertEmpty($this->GoalService->getSweetSpot($startDate, $endDate));

        $startDate = date('Y-m-d', $termStartTimestamp);
        $endDate = date('Y-m-d', $termEndTimestamp + DAY);
        $this->assertEmpty($this->GoalService->getSweetSpot($startDate, $endDate));

        $startDate = date('Y-m-d', $termStartTimestamp - DAY);
        $endDate = date('Y-m-d', $termEndTimestamp + DAY);
        $this->assertEmpty($this->GoalService->getSweetSpot($startDate, $endDate));

        $startDate = date('Y-m-d', $termStartTimestamp);
        $endDate = date('Y-m-d', $termEndTimestamp);
        $this->assertNotEmpty($this->GoalService->getSweetSpot($startDate, $endDate));
    }

    function testProcessProgressesToGraph()
    {
        $progresses = [
            '2017-01-03' => 10,
            '2017-01-04' => 20,
            '2017-01-06' => 30,
            '2017-01-07' => 40,
        ];
        $expected = [
            (int)0 => (int)0,
            (int)1 => (int)0,
            (int)2 => (int)10,
            (int)3 => (int)20,
            (int)4 => (int)20,
            (int)5 => (int)30,
            (int)6 => (int)40,
            (int)7 => (int)40,
            (int)8 => (int)40,
            (int)9 => (int)40
        ];
        $actual = $this->GoalService->processProgressesToGraph('2017-01-01', '2017-01-10', $progresses);
        $this->assertEquals($expected, $actual);

        $progresses = [
            '2017-01-01' => 10,
            '2017-01-04' => 20,
            '2017-01-06' => 30,
            '2017-01-07' => 40,
            '2017-01-10' => 60,
        ];
        $expected = [
            (int)0 => (int)10,
            (int)1 => (int)10,
            (int)2 => (int)10,
            (int)3 => (int)20,
            (int)4 => (int)20,
            (int)5 => (int)30,
            (int)6 => (int)40,
            (int)7 => (int)40,
            (int)8 => (int)40,
            (int)9 => (int)60
        ];
        $actual = $this->GoalService->processProgressesToGraph('2017-01-01', '2017-01-10', $progresses);
        $this->assertEquals($expected, $actual);
    }

    //余裕あればやる
    function testGetProgressFromCache()
    {
        $this->markTestSkipped();
    }

    //余裕あればやる
    function testWriteProgressToCache()
    {
        $this->markTestSkipped();
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
