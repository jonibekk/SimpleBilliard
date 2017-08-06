<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('InviteToUserInsertShell', 'Console/Command');
App::uses('Team', 'Model');
App::uses('Invite', 'Model');
App::uses('Email', 'Model');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');

class InviteToUserInsertShellTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.invite',
        'app.user',
        'app.team_member',
        'app.email',
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
        $this->InviteToUserInsertShell = new InviteToUserInsertShell($output, $error, $in);
        $this->InviteToUserInsertShell->initialize();
        $this->Team = ClassRegistry::init('Team');
        $this->User = ClassRegistry::init('User');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->Email = ClassRegistry::init('Email');
        $this->Invite = ClassRegistry::init('Invite');
    }
}
