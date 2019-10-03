<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'SetupService');

class SetupServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.user',
        'app.team_member',
        'app.device',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    function test_getSetupStatuses()
    {
        /** @var SetupService $SetupService */
        $SetupService = ClassRegistry::init("SetupService");
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');

        $GlRedis->deleteSetupGuideStatus(1);
        $statuses = $SetupService->getSetupStatuses(1);
        $this->assertTrue($statuses[User::SETUP_PROFILE]);
        $this->assertTrue($statuses[User::SETUP_MOBILE_APP]);

        $GlRedis->deleteSetupGuideStatus(2);
        $statuses = $SetupService->getSetupStatuses(2);
        $this->assertFalse($statuses[User::SETUP_PROFILE]);
        $this->assertTrue($statuses[User::SETUP_MOBILE_APP]);
    }

    function test_resolveSetupCompleteAndRest()
    {
        /** @var SetupService $SetupService */
        $SetupService = ClassRegistry::init("SetupService");
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');

        $GlRedis->deleteSetupGuideStatus(1);
        $resolved = $SetupService->resolveSetupCompleteAndRest(1, true);
        $this->assertTrue($resolved['complete']);
        $this->assertEquals(0, $resolved['rest_count']);

        $GlRedis->deleteSetupGuideStatus(2);
        $resolved = $SetupService->resolveSetupCompleteAndRest(2, false);
        $this->assertFalse($resolved['complete']);
        $this->assertEquals(1, $resolved['rest_count']);

        $GlRedis->deleteSetupGuideStatus(4);
        $resolved = $SetupService->resolveSetupCompleteAndRest(4, false);
        $this->assertFalse($resolved['complete']);
        $this->assertEquals(2, $resolved['rest_count']);

        $this->assertTrue(true);
    }
}
