<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class SendAlertMailToAdminShellTest
 *
 * @property SendAlertMailToAdminShell $SendAlertMailToAdminShell
 * @property Team $Team
 * @property TeamMember $TeamMember
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
    public function setUp()
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

    public function tearDown()
    {

    }
    public function test_construct()
    {
        $this->assertEquals('SendAlertMailToAdmin', $this->SendAlertMailToAdminShell->name);
    }

}
