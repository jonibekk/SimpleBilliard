<?php App::uses('GoalousTestCase', 'Test');
App::uses('ChargeHistory', 'Model');

use Goalous\Model\Enum as Enum;

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
        $this->ChargeHistory->save([
            'id'                  => 1,
            'team_id'             => 1,
            'user_id'             => 1,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000000,
            'result_type'         => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1500000000,
            'modified'            => 1500000000,
        ], false);
        // for team id 2
        $this->ChargeHistory->save([
            'id'                  => 2,
            'team_id'             => 2,
            'user_id'             => 2,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 1,
            'deleted'             => 1500000000,
            'created'             => 1500000000,
            'modified'            => 1500000000,
        ], false);
        $this->ChargeHistory->save([
            'id'                  => 3,
            'team_id'             => 2,
            'user_id'             => 2,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\ChargeHistory\ResultType::FAIL,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1500000001,
            'modified'            => 1500000001,
        ], false);

        $chargeHistory = $this->ChargeHistory->getLastChargeHistoryByTeamId(1);
        $this->assertEquals(1, $chargeHistory['id']);

        $chargeHistory = $this->ChargeHistory->getLastChargeHistoryByTeamId(2);
        $this->assertEquals(3, $chargeHistory['id']);

        $chargeHistory = $this->ChargeHistory->getLastChargeHistoryByTeamId(3);
        $this->assertEquals([], $chargeHistory);
    }
}
