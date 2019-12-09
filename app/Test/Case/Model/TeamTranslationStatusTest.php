<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamTranslationStatus', 'Model');

class TeamTranslationStatusTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team_translation_status'
    ];

    public function test_hasEntry_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $this->assertFalse($TeamTranslationStatus->hasEntry($teamId));

        $TeamTranslationStatus->createEntry($teamId);

        $this->assertTrue($TeamTranslationStatus->hasEntry($teamId));
    }

    public function test_createEntry_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $queryResult = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(10000, $queryResult['total_limit']);

        $teamId = 2;
        $customLimit = 25000;

        $TeamTranslationStatus->createEntry($teamId, $customLimit);

        $queryResult = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals($customLimit, $queryResult['total_limit']);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getUsageStatusNoEntry_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->getUsageStatus($teamId);
    }

    public function test_getLimit_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $this->assertEquals(10000, $TeamTranslationStatus->getLimit($teamId));
    }


    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getLimitNoEntry_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->getLimit($teamId);
    }

    public function test_setLimit_success()
    {
        $teamId = 1;
        $customLimit = 25000;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $this->assertEquals(10000, $TeamTranslationStatus->getLimit($teamId));

        $TeamTranslationStatus->setLimit($teamId, $customLimit);

        $this->assertEquals($customLimit, $TeamTranslationStatus->getLimit($teamId));
    }

    public function test_getTotalTranslationCount_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $this->assertEquals(0, $TeamTranslationStatus->getTotalUsageCount($teamId));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getTotalTranslationCountNoEntry_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->getTotalUsageCount($teamId);
    }

    public function test_incrementCount_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $TeamTranslationStatus->incrementCirclePostCount($teamId, 1000);
        $this->assertEquals(1000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->incrementCircleCommentCount($teamId, 2000);
        $this->assertEquals(3000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->incrementActionPostCount($teamId, 3000);
        $this->assertEquals(6000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->incrementActionCommentCount($teamId, 4000);
        $this->assertEquals(10000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->incrementCirclePostCount($teamId, 5000);
        $this->assertEquals(15000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->incrementMessageCount($teamId, 6000);
        $this->assertEquals(21000, $TeamTranslationStatus->getTotalUsageCount($teamId));
    }

    public function test_resetAllTranslationCount_success()
    {
        $teamId = 1;

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $TeamTranslationStatus->createEntry($teamId);

        $TeamTranslationStatus->incrementCirclePostCount($teamId, 1000);
        $TeamTranslationStatus->incrementCircleCommentCount($teamId, 2000);
        $TeamTranslationStatus->incrementActionPostCount($teamId, 3000);
        $TeamTranslationStatus->incrementActionCommentCount($teamId, 4000);
        $TeamTranslationStatus->incrementMessageCount($teamId, 15000);

        $this->assertEquals(25000, $TeamTranslationStatus->getTotalUsageCount($teamId));

        $TeamTranslationStatus->resetAllTranslationCount($teamId);

        $this->assertEquals(0, $TeamTranslationStatus->getTotalUsageCount($teamId));
    }
}
