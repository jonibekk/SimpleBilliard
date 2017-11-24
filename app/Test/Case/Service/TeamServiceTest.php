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
        'app.payment_setting',
        'app.invoice',
        'app.credit_card',
        'app.price_plan_purchase_team',
        'app.campaign_team',
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

    function test_getReadOnlyEndDate_success()
    {
        $teamId = $this->createTeam([
            'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
            'service_use_state_start_date' => '2017-01-10',
            'service_use_state_end_date'   => '2017-02-09',
        ]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->TeamService->getStateEndDate();
        $this->assertEquals($this->TeamService->getStateEndDate(), '2017-02-09');
    }

    function test_updateServiceUseStatus_success()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->setDefaultTeamIdAndUid(1, $teamId);

        $res = $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_PAID, date('Y-m-d'));

        $this->assertTrue($res === true);
        $this->assertEquals($this->TeamService->getServiceUseStatusByTeamId($teamId), Team::SERVICE_USE_STATUS_PAID);

        // Paid to Read-only
        $res = $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_READ_ONLY, date('Y-m-d'));

        $this->assertTrue($res === true);
        $this->assertEquals($this->TeamService->getServiceUseStatusByTeamId($teamId), Team::SERVICE_USE_STATUS_READ_ONLY);
    }

    function test_getTeamTimezone_success()
    {
        $teamId = $this->createTeam([
            'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
            'service_use_state_start_date' => '2017-01-10',
            'service_use_state_end_date'   => '2017-02-09',
            'timezone'              => 9,
        ]);
        $this->setDefaultTeamIdAndUid(1, $teamId);

        // Assert created value
        $timezone = $this->TeamService->getTeamTimezone($teamId);
        $this->assertEquals(9, $timezone);

        // Assert saved value
        $this->Team->save(['id' => $teamId, 'timezone' => 11.0]);
        $timezone = $this->TeamService->getTeamTimezone($teamId);
        $this->assertEquals(11, $timezone);

        // test error
        $timezone = $this->TeamService->getTeamTimezone(987987);
        $this->assertNull($timezone);
    }

}
