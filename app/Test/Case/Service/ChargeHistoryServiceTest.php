<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'ChargeHistoryService');

/**
 * Class ChargeHistoryService
 *
 * @property ChargeHistoryService $ChargeHistoryService
 */
class ChargeHistoryServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.payment_setting',
        'app.credit_card',
        'app.charge_history',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->ChargeHistoryService = ClassRegistry::init('ChargeHistoryService');
    }

    public function test_isLatestChargeSucceed()
    {
        // last payment succeeded
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(1));
        // failed last payment
        $this->assertTrue($this->ChargeHistoryService->isLatestChargeFailed(2));
        // setting is invoice
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(3));
    }
}
