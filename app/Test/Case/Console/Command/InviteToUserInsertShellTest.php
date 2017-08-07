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

    public function test_main()
    {
        $this->resetAllData();
        $this->InviteToUserInsertShell->params['currentTimestamp'] = strtotime('2017-07-20');

        $teamAId = $this->createTeam();
        $teamBId = $this->createTeam();

        $teamAnewUserInviteId = $this->createInvite(['team_id' => $teamAId, 'to_user_id' => null, 'email' => 'new_user_team_a@test.com', 'email_token_expires' => strtotime('2017-07-21')]);
        $teamAexistUserInviteId = $this->createInvite(['team_id' => $teamAId, 'to_user_id' => 1, 'email' => 'exist_user_team_a@test.com', 'email_token_expires' => strtotime('2017-07-21')]);
        $teamBnewUserInviteId = $this->createInvite(['team_id' => $teamBId, 'to_user_id' => null, 'email' => 'new_user_team_b@test.com', 'email_token_expires' => strtotime('2017-07-21')]);
        $teamBexistUserInviteId = $this->createInvite(['team_id' => $teamBId, 'to_user_id' => 2, 'email' => 'exist_user_team_b@test.com', 'email_token_expires' => strtotime('2017-07-21')]);

        $this->InviteToUserInsertShell->main();

        /* team A new user */
        // new email
        $newEmailId = Hash::get($this->Email->find('first', ['conditions' => ['email' => 'new_user_team_a@test.com']]), 'Email.id');
        $this->assertNotEmpty($newEmailId);
        // new user
        $newUserId = Hash::get($this->User->find('first', ['conditions' => ['primary_email_id' => $newEmailId]]) ,'User.id');
        $this->assertNotEmpty($newUserId);
        // udpate invite
        $this->assertNotEmpty($this->Invite->find('first', ['conditions' => ['id' => $teamAnewUserInviteId, 'team_id' => $teamAId, 'email' => 'new_user_team_a@test.com']]));
        // new team member
        $this->assertNotEmpty($this->TeamMember->find('first', ['conditions' => ['team_id' => $teamAId, 'user_id' => $newUserId]]));

        /* team A exist user */
        // new team member
        $this->assertNotEmpty($this->TeamMember->find('first', ['conditions' => ['team_id' => $teamAId, 'user_id' => 1]]));

        /* team B new user */
        // new email
        $newEmailId = Hash::get($this->Email->find('first', ['conditions' => ['email' => 'new_user_team_b@test.com']]), 'Email.id');
        $this->assertNotEmpty($newEmailId);
        // new user
        $newUserId = Hash::get($this->User->find('first', ['conditions' => ['primary_email_id' => $newEmailId]]) ,'User.id');
        $this->assertNotEmpty($newUserId);
        // udpate invite
        $this->assertNotEmpty($this->Invite->find('first', ['conditions' => ['id' => $teamBnewUserInviteId, 'team_id' => $teamBId, 'email' => 'new_user_team_b@test.com']]));
        // new team member
        $this->assertNotEmpty($this->TeamMember->find('first', ['conditions' => ['team_id' => $teamBId, 'user_id' => $newUserId]]));

        /* team B exist user */
        // new team member
        $this->assertNotEmpty($this->TeamMember->find('first', ['conditions' => ['team_id' => $teamBId, 'user_id' => 2]]));
    }

    public function resetAllData()
    {
        $this->Team->updateAll(['del_flg' => 1]);
        $this->Invite->updateAll(['del_flg' => 1]);
        $this->User->updateAll(['del_flg' => 1]);
        $this->TeamMember->updateAll(['del_flg' => 1]);
        $this->Email->updateAll(['del_flg' => 1]);
    }
}
