<?php App::uses('GoalousTestCase', 'Test');
App::uses('InvoiceHistory', 'Model');

/**
 * InvoiceHistory Test Case
 *
 * @property InvoiceHistory $InvoiceHistory
 */
class InvoiceHistoryTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.invoice_history',
        'app.team',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->InvoiceHistory = ClassRegistry::init('InvoiceHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InvoiceHistory);

        parent::tearDown();
    }

    // Please delete when you implement test code
    public function test_dummy() {}

}
