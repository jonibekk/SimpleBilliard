<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('ChangeServiceStatusShell', 'Console/Command');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class ChangeServiceStatusShellTest
 *
 * @property ChangeServiceStatusShell $ChangeServiceStatusShell
 * @property Team                     $Team
 * @property TeamMember               $TeamMember
 */
class ChangeServiceStatusShellTest extends GoalousTestCase
{
    public $ChangeServiceStatusShell;

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
        /** @var ChangeServiceStatusShell $ChangeServiceStatusShell */
        $this->ChangeServiceStatusShell = new ChangeServiceStatusShell($output, $error, $in);
        $this->ChangeServiceStatusShell->initialize();
        $this->Team = ClassRegistry::init('Team');
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('ChangeServiceStatus', $this->ChangeServiceStatusShell->name);
    }

}
