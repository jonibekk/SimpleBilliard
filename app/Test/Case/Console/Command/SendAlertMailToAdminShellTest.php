<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('SendAlertMailToAdminShell', 'Console/Command');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class SendAlertMailToAdminShellTest
 *
 * @property SendAlertMailToAdminShell $SendAlertMailToAdminShell
 * @property Team                      $Team
 * @property TeamMember                $TeamMember
 */
class SendAlertMailToAdminShellTest extends GoalousTestCase
{
    public $SendAlertMailToAdminShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.team',
        'app.user',
        'app.team_member',
        'app.local_name',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();

        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        /** @var SendAlertMailToAdminShell $SendAlertMailToAdminShell */
        $this->SendAlertMailToAdminShell = new SendAlertMailToAdminShell($output, $error, $in);
        $this->SendAlertMailToAdminShell->initialize();
        $this->Team = ClassRegistry::init('Team');
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('SendAlertMailToAdmin', $this->SendAlertMailToAdminShell->name);
    }

    /**
     * ターゲットチームであるかどうかのテスト
     * 前提条件: free trial期間が15日、EXPIRE_ALERT_NOTIFY_BEFORE_DAYSが"10,5,3,2,1"
     * 方針: 本日の日付は変更できないので、ステータス開始日を変更して境界値テストを実施
     */
    function test_isTargetTeam()
    {
        // TODO: We have to fix it for using `service_use_state_end_date`.
//        $daysOfStatus = Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_FREE_TRIAL];
//        $timezone = 9;
//        $team = [
//            'service_use_status'           => Team::SERVICE_USE_STATUS_FREE_TRIAL,
//            'service_use_state_start_date' => null,
//            'timezone'                     => $timezone
//        ];
//
//        $localTodayDate = AppUtil::todayDateYmdLocal($timezone);
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 16);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "Expired!");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 15);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "Just Expired!");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 14);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertTrue($isTargetTeam, "1 day before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 13);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertTrue($isTargetTeam, "2 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 12);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertTrue($isTargetTeam, "3 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 11);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "4 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 10);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertTrue($isTargetTeam, "5 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 9);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "6 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 8);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "7 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 7);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "8 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 6);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "9 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 5);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertTrue($isTargetTeam, "10 days before expires");
//
//        $team['service_use_state_start_date'] = AppUtil::dateBefore($localTodayDate, 4);
//        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam($daysOfStatus, $team);
//        $this->assertFalse($isTargetTeam, "11 days before expires");
    }

}
