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
        'app.evaluate_term',
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
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);

        $currentGoalId = $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [0, 10], $teamId);
        $previousGoalId = $this->createGoalKrs(EvaluateTerm::TYPE_PREVIOUS, [0, 10], $teamId);
        $nextGoalId = $this->createGoalKrs(EvaluateTerm::TYPE_NEXT, [0, 10], $teamId);

        $this->KrValuesDailyLogShell->params['date'] = date('Y-m-d');
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
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);

        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [10], $teamId);
        $this->KrValuesDailyLogShell->params['date'] = date('Y-m-d');
        $this->KrValuesDailyLogShell->main();

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d')]]);
        $this->assertcount(1, $res);

        $dateYesterday = date('Y-m-d', strtotime('yesterday'));
        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d', strtotime('yesterday'))]]);
        $this->assertcount(0, $res);

        $dateTomorrow = date('Y-m-d', strtotime('tomorrow'));
        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d', strtotime('tomorrow'))]]);
        $this->assertcount(0, $res);

    }

    /**
     * 複数の違う日付で正しくログが保存されていること
     */
    function testMainCorrectDateMulti()
    {
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);
        $term = $this->EvaluateTerm->getCurrentTermData(true);
        $dateYesterday = date('Y-m-d', strtotime('yesterday'));
        $dateToday = date('Y-m-d');
        $dateTomorrow = date('Y-m-d', strtotime('tomorrow'));

        $this->createGoalKrs(EvaluateTerm::TYPE_CURRENT, [100, 0], $teamId);
        $this->KrValuesDailyLogShell->params['date'] = $dateToday;
        $this->KrValuesDailyLogShell->main();
        $this->KrValuesDailyLogShell->params['date'] = $dateYesterday;
        $this->KrValuesDailyLogShell->main();
        $this->KrValuesDailyLogShell->params['date'] = $dateTomorrow;
        $this->KrValuesDailyLogShell->main();

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d')]]);
        $this->assertcount(2, $res);

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d', strtotime('yesterday'))]]);
        $this->assertcount(2, $res);

        $res = $this->KrValuesDailyLog->find('list', ['conditions' => ['target_date' => date('Y-m-d', strtotime('tomorrow'))]]);
        $this->assertcount(2, $res);
    }
}
