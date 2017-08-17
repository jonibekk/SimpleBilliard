<?php App::uses('GoalousTestCase', 'Test');
App::uses('InvoiceHistoriesChargeHistory', 'Model');

/**
 * InvoiceHistoriesChargeHistory Test Case
 *
 * @property InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory
 */
class InvoiceHistoriesChargeHistoryTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.invoice_histories_charge_history',
        'app.invoice_history',
        'app.charge_history'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InvoiceHistoriesChargeHistory);

        parent::tearDown();
    }

    function test_dummy()
    {

    }

}
