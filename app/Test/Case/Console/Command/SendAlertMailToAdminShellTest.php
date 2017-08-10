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
        $timezone = 9;
        $localTodayDate = AppUtil::todayDateYmdLocal($timezone);
        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateBefore($localTodayDate, 1),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "Expired!");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            $localTodayDate,
            $timezone
        );
        $this->assertFalse($isTargetTeam, "Just Expired!");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 1),
            $timezone
        );
        $this->assertTrue($isTargetTeam, "1 day before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 2),
            $timezone
        );
        $this->assertTrue($isTargetTeam, "2 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 3),
            $timezone
        );
        $this->assertTrue($isTargetTeam, "3 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 4),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "4 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 5),
            $timezone
        );
        $this->assertTrue($isTargetTeam, "5 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 6),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "6 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 7),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "7 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 8),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "8 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 9),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "9 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 10),
            $timezone
        );
        $this->assertTrue($isTargetTeam, "10 days before expires");

        $isTargetTeam = $this->SendAlertMailToAdminShell->_isTargetTeam(
            AppUtil::dateAfter($localTodayDate, 11),
            $timezone
        );
        $this->assertFalse($isTargetTeam, "11 days before expires");

    }

}
