<?php App::uses('GoalousTestCase', 'Test');
App::uses('ChargeHistory', 'Model');

/**
 * ChargeHistory Test Case
 *
 * @property ChargeHistory $ChargeHistory
 */
class ChargeHistoryTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.charge_history'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ChargeHistory);

        parent::tearDown();
    }

    public function test_addInvoiceMonthlyCharge()
    {
        // TODO.Payment: implement test code
    }

    public function test_findForInvoiceByStartEnd()
    {
        // TODO.Payment: implement test code
    }

    public function test_getByChargeDate()
    {
        // TODO.Payment: implement test code
    }

    public function test_getLatestMaxChargeUsers()
    {
        // TODO.Payment: implement test code
    }

    public function test_getLastChargeHistoryByTeamId()
    {
        $chargeHistory = $this->ChargeHistory->getLastChargeHistoryByTeamId(1);
        $this->assertEquals(1, $chargeHistory['ChargeHistory']['id']);

        $chargeHistory = $this->ChargeHistory->getLastChargeHistoryByTeamId(2);
        $this->assertEquals(3, $chargeHistory['ChargeHistory']['id']);
    }
}
