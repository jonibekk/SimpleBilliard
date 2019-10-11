<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Email', 'Model');
App::uses('TeamMember', 'Model');
App::import('Service/User/Team', 'UserTeamJoiningService');

/**
 * Class UserTeamJoiningServiceTest
 * @property Email  $Email
 * @property TeamMember  $TeamMember
 * @property int  $userId
 * @property int  $teamId
 * @property bool  $adminFlg
 */
class UserTeamJoiningServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.email',
        'app.team_member',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Email = ClassRegistry::init('Email');
        $this->Email->getDataSource()->truncate('emails');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->TeamMember->getDataSource()->truncate('team_members');

        $this->userId = rand(1, 1000);
        $this->teamId = rand(1, 1000);
        $this->adminFlg = rand(0, 1);

        $this->Email->create();
        $this->Email->save([
            'email' => 'hoge@isao.co.jp',
            'user_id' => $this->userId,
            'email_verified' => 1,
        ]);
    }

    /**
     * @group addMember
     * @throws Exception
     */
    public function testAddMember()
    {
        $service = new UserTeamJoiningService();
        $result = $service->addMember($this->userId, $this->teamId, $this->adminFlg);
        $resultId = Hash::get($result, 'TeamMember.id');

        $actual = $this->TeamMember->find('first', [
            'conditions' => [
                'user_id' => $this->userId,
                'team_id' => $this->teamId,
            ],
            'fields' => ['id', 'user_id', 'team_id', 'admin_flg']
        ]);

        $this->assertEquals($resultId, Hash::get($actual, 'TeamMember.id'));
        $this->assertEquals($this->userId, Hash::get($actual, 'TeamMember.user_id'));
        $this->assertEquals($this->teamId, Hash::get($actual, 'TeamMember.team_id'));
        $this->assertEquals($this->adminFlg, Hash::get($actual, 'TeamMember.admin_flg'));
    }

    /**
     * @group isJoined
     * @throws Exception
     */
    public function testIsJoinedIsExist()
    {
        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id' => $this->userId,
            'team_id' => $this->teamId
        ]);

        $result = (new UserTeamJoiningService())->isJoined($this->userId, $this->teamId);
        $this->assertTrue($result);
    }

    /**
     * @group isJoined
     * @throws Exception
     */
    public function testIsJoinedIsNotExist()
    {
        $result = (new UserTeamJoiningService())->isJoined($this->userId, $this->teamId);
        $this->assertFalse($result);
    }
}
