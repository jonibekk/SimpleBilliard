<?php App::uses('GoalousTestCase', 'Test');
App::uses('InvoiceHistory', 'Model');

use Goalous\Model\Enum as Enum;
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
     * checkCreditOkInPast method
     */
    public function test_checkCreditOkInPast()
    {
        $teamId = 1;
        $ts = strtotime('2018-01-01 00:00:00');
        $res = $this->InvoiceHistory->checkCreditOkInPast($teamId, $ts);
        $this->assertFalse($res);

        // First invoice history: NG
        $this->InvoiceHistory->create();
        $history = $this->InvoiceHistory->save([
            'team_id' => $teamId,
            'order_status' => Enum\Invoice\CreditStatus::NG,
        ], false);
        $ts = Hash::get($history , 'InvoiceHistory.created');
        $res = $this->InvoiceHistory->checkCreditOkInPast($teamId, $ts + 1);
        $this->assertFalse($res);


        // Second invoice history: OK
        $this->InvoiceHistory->create();
        $history = $this->InvoiceHistory->save([
            'team_id' => $teamId,
            'order_status' => Enum\Invoice\CreditStatus::OK,
        ], false);
        $ts = Hash::get($history , 'InvoiceHistory.created');
        $res = $this->InvoiceHistory->checkCreditOkInPast($teamId, $ts + 1);
        $this->assertTrue($res);

        // Third invoice history: NG
        $this->InvoiceHistory->create();
        $history = $this->InvoiceHistory->save([
            'team_id' => $teamId,
            'order_status' => Enum\Invoice\CreditStatus::NG,
        ], false);
        $ts = Hash::get($history , 'InvoiceHistory.created');
        $res = $this->InvoiceHistory->checkCreditOkInPast($teamId, $ts + 1);
        $this->assertTrue($res);
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
