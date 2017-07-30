<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamService');

/**
 * @property TeamService $TeamService
 */
class TeamServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.term',
        'app.user',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamService = ClassRegistry::init('TeamService');
        $this->Team = ClassRegistry::init('Team');
    }

    function test_getServiceUseStatus_success()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->assertEquals($this->TeamService->getServiceUseStatus(), Team::SERVICE_USE_STATUS_FREE_TRIAL);
    }

    function test_isReadOnly_readOnly()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->assertTrue($this->TeamService->isReadOnly($teamId));
    }

}
