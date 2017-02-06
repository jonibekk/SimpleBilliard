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
 * @property GoalService          $GoalService
 * @property Team                 $Team
 * @property EvaluateTerm         $EvaluateTerm
 * @property Goal                 $Goal
 * @property GoalProgressDailyLog $GoalProgressDailyLog
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
        'app.goal',
        'app.goal_member',
        'app.goal_progress_daily_log',
        'app.key_result',
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
        $this->Goal = ClassRegistry::init('Goal');
        $this->GoalProgressDailyLog = ClassRegistry::init('GoalProgressDailyLog');
        $this->setDefaultTeamIdAndUid();
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
    function testGetGraphRangeTargetDaysOver()
    {
        $this->_setUpGraphDefault();
        try {
            $this->GoalService->getGraphRange(time(), date('t') + 1);
        } catch (Exception $e) {
        }
        $this->assertTrue(isset($e));
    }

    /**
     * グラフの日付範囲指定で期間内の場合
     */
    function testGetGraphRangeTargetDaysNotOver()
    {
        $this->_setUpGraphDefault();
        try {
            $this->GoalService->getGraphRange(time(), date('t'));
        } catch (Exception $e) {
        }
        //例外が返らないこと
        $this->assertFalse(isset($e));
    }

    /**
     * グラフ表示期間算出メソッドのテスト
     * - 指定終了日が期の開始日に近い場合
     */
    function testGetGraphRangeTargetEndIsNotLongSinceTermStart()
    {
        $this->_setUpGraphDefault();
        $expected = [
            'graphStartDate' => date('Y-m-01'),
            'graphEndDate'   => date('Y-m-10'),
        ];
        //バッファなし
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $expected['plotDataEndDate'] = date('Y-m-01');
        //当日が期の開始日と一緒の場合、期の開始日とプロットデータのエンドは一緒になる
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        $expected['plotDataEndDate'] = date('Y-m-10');
        //バッファなしで当日が期の開始日から9日後はプロットデータも9日後になる
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 10 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        //バッファなしで、targetDaysが10日で当日が期の開始日から10日後は範囲全体の日付が変わる
        $this->assertEquals([
            'graphStartDate'  => date('Y-m-02'),
            'graphEndDate'    => date('Y-m-11'),
            'plotDataEndDate' => date('Y-m-11')
        ], $actual);

        //バッファあり
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $expected['plotDataEndDate'] = date('Y-m-01');
        //バッファありでも$targetEndTimestampが収まる場合は、日付が一緒になる。
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 1 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        $expected['plotDataEndDate'] = date('Y-m-01');
        //バッファありで、$targetEndTimestampが収まらない場合は日付が変わる。
        $this->assertNotEquals([
            'graphStartDate'  => date('Y-m-02'),
            'graphEndDate'    => date('Y-m-11'),
            'plotDataEndDate' => date('Y-m-11')
        ], $actual);
    }

    /**
     * グラフ表示期間算出メソッドのテスト
     * - 指定終了日が期の終了日に近い場合
     */
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
        //バッファありでも期の終了日に近い場合は、バッファ考慮しない
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 8 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        //バッファありでも期の終了日に近い場合は、バッファ考慮しない
        $this->assertEquals($expected, $actual);

        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 9);
        //バッファありで、指定終了日と期の終了日の差分がバッファを超える場合はバッファ考慮される
        $this->assertNotEquals($expected, $actual);

        //バッファなし
        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 9)),
            'graphEndDate'    => date('Y-m-' . date('t')),
            'plotDataEndDate' => date('Y-m-' . date('t'))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'];
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        //期の終了日までのデータ表示
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 10)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 1)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 1))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 1 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        //期の終了日から１日前が指定終了日ならそれまでのデータ表示
        $this->assertEquals($expected, $actual);

        $expected = [
            'graphStartDate'  => date('Y-m-' . (string)(date('t') - 18)),
            'graphEndDate'    => date('Y-m-' . (string)(date('t') - 9)),
            'plotDataEndDate' => date('Y-m-' . (string)(date('t') - 9))
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['end_date'] - 9 * DAY;
        $actual = $this->GoalService->getGraphRange($targetEndTimestamp, $targetDays = 10, $maxBufferDays = 0);
        //期の終了日から９日前が指定終了日ならそれまでのデータ表示
        $this->assertEquals($expected, $actual);
    }

    /**
     * グラフ表示期間算出メソッドのテスト
     * - 指定終了日が期の開始日、終了日の両方に近くない場合
     */
    function testGetGraphRangeNormal()
    {
        $this->_setUpGraphDefault();

        $expected = [
            'graphStartDate'  => date('Y-m-01'),
            'graphEndDate'    => date('Y-m-10'),
            'plotDataEndDate' => date('Y-m-07'),
        ];
        $targetEndTimestamp = $this->EvaluateTerm->getCurrentTermData(true)['start_date'] + 6 * DAY;
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

    /**
     * グラフデータ取得の基本テスト(期の始め)
     * 正しい件数で正しいデータが取得できていること
     * データがない場合
     */
    function testUserGraphDataBasicStartTerm()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $targetDays = 10;
        $maxBufferDays = 2;
        $term = $this->EvaluateTerm->getCurrentTermData(true);
        $termStartTimestamp = $term['start_date'];

        $ret = $this->_getUserAllGoalProgressForDrawingGraph($termStartTimestamp, $targetDays, $maxBufferDays);

        //データ件数のチェック(10日分+項目名1=11)
        $this->assertCount(11, $ret[0]);//sweet spot top
        $this->assertCount(11, $ret[1]);//sweet spot bottom
        $this->assertCount(11, $ret[3]);//x
        //sweet spotの開始値が0になっていること
        $this->assertEquals(0, $ret[0][1]);
        $this->assertEquals(0, $ret[1][1]);
        //sweet spotの終了値が前の値より大きいこと
        $this->assertTrue($ret[0][9] < $ret[0][10]);
        $this->assertTrue($ret[1][9] < $ret[1][10]);
    }

    /**
     * グラフデータ取得の基本テスト(期の真ん中)
     * 正しい件数で正しいデータが取得できていること
     * データがない場合
     */
    function testUserGraphDataBasicMiddleTerm()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $targetDays = 10;
        $maxBufferDays = 2;
        $term = $this->EvaluateTerm->getCurrentTermData(true);
        $targetEndTimestamp = $term['start_date'] + 15 * DAY;

        $ret = $this->_getUserAllGoalProgressForDrawingGraph($targetEndTimestamp, $targetDays, $maxBufferDays);

        //データ件数のチェック(10日分+項目名1=11)
        $this->assertCount(11, $ret[0]);//sweet spot top
        $this->assertCount(11, $ret[1]);//sweet spot bottom
        $this->assertCount(9, $ret[2]);//data(10日-バッファ2+項目1=9)
        $this->assertCount(11, $ret[3]);//x
        //sweet spotの開始値が0以外になっていること
        $this->assertNotEquals(0, $ret[0][1]);
        $this->assertNotEquals(0, $ret[1][1]);
        //sweet spotの終了値が前の値より大きいこと
        $this->assertTrue($ret[0][9] < $ret[0][10]);
        $this->assertTrue($ret[1][9] < $ret[1][10]);
        //dataは全てnullになっていること
        $this->assertNull($ret[2][1]);
        $this->assertNull($ret[2][8]);
    }

    /**
     * グラフデータ取得の基本テスト(期の終わり)
     * 正しい件数で正しいデータが取得できていること
     * データがない場合
     */
    function testUserGraphDataBasicEndTerm()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $targetDays = 10;
        $maxBufferDays = 2;
        $term = $this->EvaluateTerm->getCurrentTermData(true);
        $targetEndTimestamp = $term['end_date'];

        $ret = $this->_getUserAllGoalProgressForDrawingGraph($targetEndTimestamp, $targetDays, $maxBufferDays);

        //データ件数のチェック(10日分+項目名1=11)
        $this->assertCount(11, $ret[0]);//sweet spot top
        $this->assertCount(11, $ret[1]);//sweet spot bottom
        $this->assertCount(11, $ret[3]);//x
        //sweet spotの開始値が0以外になっていること
        $this->assertNotEquals(0, $ret[0][1]);
        $this->assertNotEquals(0, $ret[1][1]);
        //sweet spotの終了値が前の値より大きいこと
        $this->assertTrue($ret[0][9] < $ret[0][10]);
        $this->assertTrue($ret[1][9] < $ret[1][10]);
        //dataは全てnullになっていること
        $this->assertNull($ret[2][1]);
        $this->assertNull($ret[2][9]);
    }

    /**
     * 今日が今期の開始日
     * - 前期のゴールが含まれないこと
     * - 来期のゴールが含まれないこと
     * - 今期のゴール追加後に今期のゴールが含まれること
     */
    function testUserGraphNoLogStartTermOnlyToday()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupCurrentTermStartToday();
        $targetDays = 10;
        $maxBufferDays = 2;
        $now = time();

        $this->createGoalKrs(EvaluateTerm::TYPE_PREVIOUS, [50]);
        $this->createGoalKrs(EvaluateTerm::TYPE_NEXT, [50]);
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $this->GoalService->saveGoalProgressLogsAsBulk(1, $yesterday);

        $ret = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        //前期のゴールが含まれないこと
        $this->assertCount(1, $ret[2]);//dataが項目名のみ

        //今期のゴール追加
        $goalId = $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [50]);
        $ret = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        $this->assertCount(2, $ret[2]);
        $this->assertEquals(50, $ret[2][1]);//ゴール進捗が存在すること

        //ゴール進捗を更新
        $this->createKr($goalId, 1, 1, 0);
        $ret = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        $this->assertNotEquals(50, $ret[2][1]);//ゴール進捗が更新されること
    }

    /**
     * 今日が今期の終了日
     * - データの個数がフルになっていること
     * - 当日に進捗があった場合に昨日のデータのログと、当日のデータが違っていること
     */
    function testUserGraphEndTermToday()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupCurrentTermEndToday();
        //昨日のログ作成
        $goalId = $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [50]);
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $this->GoalService->saveGoalProgressLogsAsBulk(1, $yesterday);
        //進捗を更新(KRを追加)
        $this->createKr($goalId, 1, 1, 100);
        $targetDays = 10;
        $maxBufferDays = 2;
        $now = time();
        $ret = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        $this->assertCount(11, $ret[2]);//dataの数が全件分あること
        $this->assertEquals(50, $ret[2][9]);//一日前のゴール進捗
        $this->assertEquals(75, $ret[2][10]);//当日のゴール進捗
    }

    /**
     * ゴール作成が過去のログ進捗に影響を与えないこと
     * - 昨日のログがあり、ゴールが追加された場合に過去のログ進捗に影響を与えないこと
     */
    function testUserGraphEffectLogs()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupCurrentTermExtendDays();
        //昨日のログ作成
        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [50]);
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $this->GoalService->saveGoalProgressLogsAsBulk(1, $yesterday);

        $targetDays = 10;
        $maxBufferDays = 2;
        $now = time();
        //1回目のデータ取得
        $before = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        //新しいゴール追加
        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [100]);
        $this->_clearCache();
        //2回目のデータ取得
        $after = $this->_getUserAllGoalProgressForDrawingGraph($now, $targetDays, $maxBufferDays);
        //ログデータに影響がないこと
        $this->assertEquals($before[2][7], $after[2][7]);
        //当日のデータが更新されていること
        $this->assertNotEquals($before[2][8], $after[2][8]);
    }

    /**
     * グラフデータ取得でのデータの整合性チェック
     */
    function testUserGraphDataValid()
    {
        //今期を3ヶ月に設定(当月にその前後30日ずつ拡張したものにする)
        $this->setupCurrentTermExtendDays();
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [0]);
        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [100]);
        $this->GoalService->saveGoalProgressLogsAsBulk(1, $yesterday);

        $targetDays = 10;
        $maxBufferDays = 2;
        $targetEndTimestamp = time();

        //ゴールの進捗が変わっていない場合のログと当日のデータが等しくなることを確認
        $ret = $this->_getUserAllGoalProgressForDrawingGraph($targetEndTimestamp, $targetDays, $maxBufferDays);
        $this->assertEquals($ret[2][8], $ret[2][7]);
        $this->assertEquals(50, $ret[2][8]);

        //ゴールの進捗が変わった場合のログと当日のデータが変わることを確認
        //新しいゴールを一つ追加。これにより最新の進捗の合計値は変化する
        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [0]);
        $ret = $this->_getUserAllGoalProgressForDrawingGraph($targetEndTimestamp, $targetDays, $maxBufferDays);
        $this->assertNotEquals($ret[2][8], $ret[2][7]);
        $this->assertNotEquals(50, $ret[2][8]);
    }

    /**
     * テストの為のユーザグラフデータ取得用メソッド
     *
     * @param string $targetEndTimestamp
     * @param int    $targetDays
     * @param int    $maxBufferDays
     *
     * @return array
     */
    function _getUserAllGoalProgressForDrawingGraph($targetEndTimestamp, $targetDays, $maxBufferDays)
    {

        $graphRange = $this->GoalService->getGraphRange(
            $targetEndTimestamp,
            $targetDays,
            $maxBufferDays
        );
        $progressGraph = $this->GoalService->getUserAllGoalProgressForDrawingGraph(
            1,
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate'],
            true
        );
        return $progressGraph;
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

    /**
     * sweet spotの値の件数チェック
     */
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

    /**
     * sweet spotの値が正しいこと
     */
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

    /**
     * sweet spotの値取得範囲が期を超えている場合のテスト
     */
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

    /**
     * ゴール進捗計算メソッドのKRの重要度によって重み付けして計算しているかのテスト
     * - ステートレスなメソッドのため、前提となるデータの準備不要
     */
    function testGetProgressPriority()
    {
        //KRの重要度が同じ場合
        $krs = [
            [
                'priority'      => 1,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 0,
            ],
            [
                'priority'      => 1,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 100,
            ],
        ];
        //進捗0と100で50になるはず
        $sumPriorities = $this->GoalService->getSumPriorities($krs);
        $this->assertEquals(50, $this->GoalService->getProgress($krs, $sumPriorities));

        //KRの重要度が違う場合
        $krs = [
            [
                'priority'      => 1,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 0,
            ],
            [
                'priority'      => 5,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 100,
            ],
        ];
        //進捗0と100だが、priorityが違うため、50にはならないはず
        $sumPriorities = $this->GoalService->getSumPriorities($krs);
        $this->assertNotEquals(50, $this->GoalService->getProgress($krs, $sumPriorities));
    }

    /**
     * ゴール進捗計算メソッドでの閾値テスト
     */
    function testGetProgressThreshold()
    {
        //進捗率が99.*の場合は結果が99になるはず
        $krs = [
            [
                'priority'      => 1,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 99.01,
            ],
        ];
        $sumPriorities = $this->GoalService->getSumPriorities($krs);
        $this->assertEquals(99, $this->GoalService->getProgress($krs, $sumPriorities));

        //進捗率が0.*の場合は結果が1になるはず
        $krs = [
            [
                'priority'      => 1,
                'start_value'   => 0,
                'target_value'  => 100,
                'current_value' => 0.01,
            ],
        ];
        $sumPriorities = $this->GoalService->getSumPriorities($krs);
        $this->assertEquals(1, $this->GoalService->getProgress($krs, $sumPriorities));
    }

    /**
     * グラフ表示用の進捗データの生成のテスト
     */
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

}
