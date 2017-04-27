<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('KrValuesDailyLogShell', 'Console/Command');
App::uses('KrValuesDailyLog', 'Model');
App::uses('Goal', 'Model');
App::uses('GoalMember', 'Model');
App::uses('KeyResult', 'Model');

/**
 * Class KrValuesDailyLogShellTest
 *
 * @property KrValuesDailyLogShell $KrValuesDailyLogShell
 * @property KrValuesDailyLog      $KrValuesDailyLog
 * @property KeyResult             $KeyResult
 */
class KrValuesDailyLogShellTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.term',
        'app.key_result',
        'app.goal',
        'app.goal_member',
        'app.kr_values_daily_log',
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
        $this->KrValuesDailyLogShell = new KrValuesDailyLogShell($output, $error, $in);
        $this->KrValuesDailyLogShell->initialize();
        $this->Goal = ClassRegistry::init('Goal');
        $this->GoalMember = ClassRegistry::init('GoalMember');
        $this->KeyResult = ClassRegistry::init('KeyResult');
        $this->KrValuesDailyLog = ClassRegistry::init('KrValuesDailyLog');
    }

    /**
     * 今期のKRのみ取得すること
     * 前期、今期、来期のデータを保存し検査
     */
    function testMainOnlyCurrentTerm()
    {
        $this->deleteAllTeam();
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);

        $currentGoalId = $this->createGoalKrs(Term::TYPE_CURRENT, [0, 10], $teamId);
        $previousGoalId = $this->createGoalKrs(Term::TYPE_PREVIOUS, [0, 10], $teamId);
        $nextGoalId = $this->createGoalKrs(Term::TYPE_NEXT, [0, 10], $teamId);

        $this->KrValuesDailyLogShell->params['date'] = date('Y-m-d');
        $this->KrValuesDailyLogShell->params['timezone'] = 9;
        $this->KrValuesDailyLogShell->main();

        // 今期は空じゃない
        $this->assertNotEmpty($this->KrValuesDailyLog->findByGoalId($currentGoalId));
        // 前期は空
        $this->assertEmpty($this->KrValuesDailyLog->findByGoalId($previousGoalId));
        // 来期も空
        $this->assertEmpty($this->KrValuesDailyLog->findByGoalId($nextGoalId));
    }

    /**
     * 指定した日付で正しくログが保存されていること
     */
    function testMainCorrectDate()
    {
        $this->deleteAllTeam();
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);

        $this->createGoalKrs(Term::TYPE_CURRENT, [10], $teamId);
        $this->KrValuesDailyLogShell->params['date'] = date('Y-m-d');
        $this->KrValuesDailyLogShell->params['timezone'] = 9;
        $this->KrValuesDailyLogShell->main();

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d')]]);
        $this->assertcount(1, $res);

        $dateYesterday = date('Y-m-d', strtotime('yesterday'));
        $res = $this->KrValuesDailyLog->find('list',
            ['conditions' => ['target_date' => date('Y-m-d', strtotime('yesterday'))]]);
        $this->assertcount(0, $res);

        $dateTomorrow = date('Y-m-d', strtotime('tomorrow'));
        $res = $this->KrValuesDailyLog->find('list',
            ['conditions' => ['target_date' => date('Y-m-d', strtotime('tomorrow'))]]);
        $this->assertcount(0, $res);

    }

    /**
     * 複数の違う日付で正しくログが保存されていること
     */
    function testMainCorrectDateMulti()
    {
        $this->deleteAllTeam();
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupCurrentTermExtendDays($teamId);
        $dateYesterday = date('Y-m-d', strtotime('yesterday'));
        $dateToday = date('Y-m-d');
        $dateTomorrow = date('Y-m-d', strtotime('tomorrow'));

        $this->createGoalKrs(Term::TYPE_CURRENT, [100, 0], $teamId);
        $this->KrValuesDailyLogShell->params['timezone'] = 9;
        $this->KrValuesDailyLogShell->params['date'] = $dateToday;
        $this->KrValuesDailyLogShell->main();
        $this->KrValuesDailyLogShell->params['date'] = $dateYesterday;
        $this->KrValuesDailyLogShell->main();
        $this->KrValuesDailyLogShell->params['date'] = $dateTomorrow;
        $this->KrValuesDailyLogShell->main();

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d')]]);
        $this->assertcount(2, $res);

        $res = $this->KrValuesDailyLog->find('list',
            ['conditions' => ['target_date' => date('Y-m-d', strtotime('yesterday'))]]);
        $this->assertcount(2, $res);

        $res = $this->KrValuesDailyLog->find('list',
            ['conditions' => ['target_date' => date('Y-m-d', strtotime('tomorrow'))]]);
        $this->assertcount(2, $res);
    }

    /**
     * 複数のタイムゾーンでデータが保存されていること
     */
    function test_main_allTimezone()
    {
        $this->deleteAllTeam();

        $todayDate = date('Y-m-d');
        $yesterdayDate = date('Y-m-d', strtotime("yesterday"));
        $expectedValues = [
            [
                'timezone'       => "-12",
                'currentTime'    => "12:00:00",
                'targetDate'     => $yesterdayDate,
                'krCurrentValue' => 10,
            ],
            [
                'timezone'       => "-11",
                'currentTime'    => "11:00:00",
                'targetDate'     => $yesterdayDate,
                'krCurrentValue' => 20,
            ],
            [
                'timezone'       => "-3.5",
                'currentTime'    => "03:30:00",
                'targetDate'     => $yesterdayDate,
                'krCurrentValue' => 30,
            ],
            [
                'timezone'       => "0",
                'currentTime'    => "00:00:00",
                'targetDate'     => $yesterdayDate,
                'krCurrentValue' => 40,
            ],
            [
                'timezone'       => "1",
                'currentTime'    => "23:00:00",
                'targetDate'     => $todayDate,
                'krCurrentValue' => 50,
            ],
            [
                'timezone'       => "3.5",
                'currentTime'    => "20:30:00",
                'targetDate'     => $todayDate,
                'krCurrentValue' => 60,
            ],
            [
                'timezone'       => "9",
                'currentTime'    => "15:00:00",
                'targetDate'     => $todayDate,
                'krCurrentValue' => 70,
            ],
            [
                'timezone'       => "11",
                'currentTime'    => "13:00:00",
                'targetDate'     => $todayDate,
                'krCurrentValue' => 80,
            ],
            [
                'timezone'       => "12",
                'currentTime'    => "12:00:00",
                'targetDate'     => $todayDate,
                'krCurrentValue' => 90,
            ],
        ];
        //データの準備
        foreach ($expectedValues as $k => $v) {
            $teamId = $this->_saveDatasForTimezoneTest($v['timezone'], $v['krCurrentValue']);
            //アサーションでチーム毎にチェックするため、teamIdを退避
            $expectedValues[$k]['teamId'] = $teamId;
        }

        //重複する時間を除去してバッチ実行(12:00:00が被っているため)
        $currentTimes = array_unique(Hash::extract($expectedValues, '{n}.currentTime'));
        foreach ($currentTimes as $currentTime) {
            $this->setDefaultTeamIdAndUid(null, null);
            $this->KrValuesDailyLogShell->params['currentTimestamp'] = strtotime($currentTime);
            $this->KrValuesDailyLogShell->main();
        }

        //各チーム毎に期待する対象日でデータが保存できているかチェック
        foreach ($expectedValues as $v) {
            $data = $this->KrValuesDailyLog->find('all', ['conditions' => ['team_id' => $v['teamId']]]);
            //事前にKRは一つずつ保存しているのでログデータは１チーム一つずつになるはず
            $this->assertCount(1, $data);
            //期待する対象日になっているか？
            $this->assertEquals($v['targetDate'], $data[0]['KrValuesDailyLog']['target_date']);
            //KRの値は期待する値になっているか？
            $this->assertEquals($v['krCurrentValue'], $data[0]['KrValuesDailyLog']['current_value']);
        }
    }

    /**
     * タイムゾーンを指定して、チーム、期間、ゴール１つを作成
     *
     * @param $timezone
     * @param $krCurrentValue
     *
     * @return int
     */
    function _saveDatasForTimezoneTest($timezone, $krCurrentValue)
    {
        $teamId = $this->createTeam(['timezone' => $timezone]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupCurrentTermExtendDays($teamId);
        $this->createGoalKrs(Term::TYPE_CURRENT, [$krCurrentValue], $teamId);
        return $teamId;
    }
}
