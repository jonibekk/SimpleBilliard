<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamMemberService');

/**
 * @property TeamMemberService $TeamMemberService
 */
class TeamMemberServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.team',
        'app.team_member',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->TeamMemberService = ClassRegistry::init('TeamMemberService');
    }

    public function test_validateActivation()
    {
        // free trial
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamMemberId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertTrue($this->TeamMemberService->validateActivation($teamId, $teamMemberId));

        // paid
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_PAID]);
        $teamMemberId = $this->createTeamMember($teamId, 2, TeamMember::USER_STATUS_INACTIVE);
        $this->assertTrue($this->TeamMemberService->validateActivation($teamId, $teamMemberId));
    }

    public function test_validateActivation_notAllowedTeamPlan()
    {
        // free trial
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $teamMemberId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberId));

        // can not use service
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);
        $teamMemberId = $this->createTeamMember($teamId, 2, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberId));
    }

    public function test_validateActivation_BelongTeam()
    {
        $teamAId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamBId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamAMemberId = $this->createTeamMember($teamAId, 1, TeamMember::USER_STATUS_INACTIVE);
        $teamBMemberId = $this->createTeamMember($teamBId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamAId, $teamBMemberId));
    }

    public function test_validateActivation_notAllowdTeamMemberStatus()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamMemberAId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INVITED);
        $teamMemberBId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_ACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberAId));
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberBId));
    }
}
