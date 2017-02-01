<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('GoalProgressDailyLogShell', 'Console/Command');
App::uses('GoalProgressDailyLog', 'Model');
App::uses('Goal', 'Model');
App::uses('KeyResult', 'Model');
App::uses('GlRedis', 'Model');

/**
 * Class GoalProgressDailyLogShellTest
 *
 * @property GoalProgressDailyLogShell $GoalProgressDailyLogShell
 * @property GoalProgressDailyLog      $GoalProgressDailyLog
 * @property Goal                      $Goal
 * @property KeyResult                 $KeyResult
 * @property GlRedis                   $GlRedis
 */
class GoalProgressDailyLogShellTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.evaluate_term',
        'app.goal',
        'app.goal_member',
        'app.key_result',
        'app.goal_progress_daily_log',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        $this->GoalProgressDailyLogShell = new GoalProgressDailyLogShell($output, $error, $in);
        $this->GoalProgressDailyLogShell->initialize();
        $this->Goal = ClassRegistry::init('Goal');
        $this->KeyResult = ClassRegistry::init('KeyResult');
        $this->GoalProgressDailyLog = ClassRegistry::init('GoalProgressDailyLog');
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->GlRedis->changeDbSource('redis_test');
    }

    /**
     * 今期のゴール進捗のみ取得する事
     * 前期、今期、来期のデータを保存し検査
     */
    function testMainOnlyCurrentTerm()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_PREVIOUS, [10]);
        //このデータのみログが保存される
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [20]);
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_NEXT, [30]);
        $this->GoalProgressDailyLogShell->params['date'] = date('Y-m-d');
        $this->GoalProgressDailyLogShell->main();
        $this->GoalProgressDailyLog->current_team_id = 1;
        $res = $this->GoalProgressDailyLog->findLogs(date('Y-m-d'), date('Y-m-d'), $goalIds);

        $this->assertcount(1, $res);
        $this->assertEquals(20, $res[0]['progress']);
    }

    /**
     * 進捗の計算が正しい事
     */
    function testMainCorrectProgress()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        //進捗100と0で合計50になるはず
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [100, 0]);
        //進捗0と0で合計0になるはず
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [0, 0]);
        //進捗100と100で合計100になるはず
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [100, 100]);
        $this->GoalProgressDailyLogShell->params['date'] = date('Y-m-d');
        $this->GoalProgressDailyLogShell->main();
        $this->GoalProgressDailyLog->current_team_id = 1;
        $res = $this->GoalProgressDailyLog->findLogs(date('Y-m-d'), date('Y-m-d'), $goalIds);

        $this->assertcount(3, $res);
        $this->assertEquals(50, $res[0]['progress']);
        $this->assertEquals(0, $res[1]['progress']);
        $this->assertEquals(100, $res[2]['progress']);
    }

    /**
     * 指定した日付で正しくログが保存されていること
     */
    function testMainCorrectDate()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [100, 0]);
        $this->GoalProgressDailyLogShell->params['date'] = date('Y-m-d');
        $this->GoalProgressDailyLogShell->main();
        $this->GoalProgressDailyLog->current_team_id = 1;

        $res = $this->GoalProgressDailyLog->findLogs(date('Y-m-d'), date('Y-m-d'), $goalIds);
        $this->assertcount(1, $res);

        $dateYesterday = date('Y-m-d', strtotime('yesterday'));
        $res = $this->GoalProgressDailyLog->findLogs($dateYesterday, $dateYesterday, $goalIds);
        $this->assertcount(0, $res);

        $dateTomorrow = date('Y-m-d', strtotime('tomorrow'));
        $res = $this->GoalProgressDailyLog->findLogs($dateTomorrow, $dateTomorrow, $goalIds);
        $this->assertcount(0, $res);

    }

    /**
     * 複数の違う日付で正しくログが保存されていること
     */
    function testMainCorrectDateMulti()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $term = $this->EvaluateTerm->getCurrentTermData(true);
        $dateYesterday = date('Y-m-d', $term['start_date']);
        $dateToday = date('Y-m-d', $term['start_date'] + DAY);

        $goalIds[] = $this->_saveGoalKrs(EvaluateTerm::TYPE_CURRENT, [100, 0]);
        $this->GoalProgressDailyLogShell->params['date'] = $dateToday;
        $this->GoalProgressDailyLogShell->main();
        $this->GoalProgressDailyLogShell->params['date'] = $dateYesterday;
        $this->GoalProgressDailyLogShell->main();
        $this->GoalProgressDailyLog->current_team_id = 1;

        $res = $this->GoalProgressDailyLog->findLogs($dateYesterday, $dateToday, $goalIds);
        $this->assertcount(2, $res);
    }

    function _saveGoalKrs($termType, $krProgresses, $teamId = 1, $userId = 1)
    {
        $goalData = [
            'user_id'          => $userId,
            'team_id'          => $teamId,
            'name'             => 'ゴール1',
            'goal_category_id' => 1
        ];
        $goalData['end_date'] = $this->EvaluateTerm->getTermData($termType)['end_date'];
        $this->Goal->create();
        $this->Goal->save($goalData);
        $goalId = $this->Goal->getLastInsertID();
        $krDatas = [];
        foreach ($krProgresses as $v) {
            $krDatas[] = [
                'goal_id'       => $goalId,
                'team_id'       => $teamId,
                'user_id'       => $userId,
                'name'          => 'テストKR',
                'start_value'   => 0,
                'target_value'  => 100,
                'value_unit'    => 0,
                'current_value' => $v,
            ];
        }
        $this->KeyResult->create();
        $ret = $this->KeyResult->saveAll($krDatas);
        return $goalId;
    }
}
