<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('SendAlertMailToAdminShell', 'Console/Command');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('GoalousDateTime', 'DateTime');

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
     * 前提条件: free trial期間が15日、EXPIRE_ALERT_NOTIFY_BEFORE_DAYSが "10,5,3,2,1"
     */
    function test_isTargetTeam()
    {
        $timezone = 9;
        $currentDateTime = GoalousDateTime::now()->setTimeZoneByHour($timezone);
        $currentDateTimeFormatted = $currentDateTime->format('Y-m-d');
        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->subDay(1)->format('Y-m-d')// = stateEndDate
        );
        $this->assertFalse($isTargetTeam, "Expired!");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTimeFormatted
        );
        $this->assertFalse($isTargetTeam, "Just Expired!");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(1)->format('Y-m-d')
        );
        $this->assertTrue($isTargetTeam, "1 day before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(2)->format('Y-m-d')
        );
        $this->assertTrue($isTargetTeam, "2 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(3)->format('Y-m-d')
        );
        $this->assertTrue($isTargetTeam, "3 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(4)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "4 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(5)->format('Y-m-d')
        );
        $this->assertTrue($isTargetTeam, "5 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(6)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "6 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(7)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "7 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(8)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "8 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(9)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "9 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(10)->format('Y-m-d')
        );
        $this->assertTrue($isTargetTeam, "10 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $currentDateTimeFormatted,
            $currentDateTime->copy()->addDay(11)->format('Y-m-d')
        );
        $this->assertFalse($isTargetTeam, "11 days before expires");

    }

}
