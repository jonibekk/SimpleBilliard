<?php
App::uses('GoalousTestCase', 'Test');

use Goalous\Enum as Enum;

/**
 * PaymentSetting Test Case
 *
 * @property PaymentSetting PaymentSetting
 * @property ChargeHistory  ChargeHistory
 * @property CreditCard     CreditCard
 */
class PaymentSettingTest extends GoalousTestCase
{

    /**
     * @var array
     * Fixtures
     */
    public $fixtures = array(
        'app.payment_setting',
        'app.charge_history',
        'app.credit_card',
        'app.invoice',
        'app.team',
        'app.team_member',
        'app.user',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PaymentSetting = ClassRegistry::init('PaymentSetting');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->CreditCard = ClassRegistry::init('CreditCard');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PaymentSetting);

        parent::tearDown();
    }

    // Please delete when you implement test code
    public function test_dummy()
    {
    }

    public function test_findMonthlyChargeCcTeams_basic()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $paymentSetting = ['payment_base_day' => 1];
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam([], $paymentSetting);

        // data_count: 1
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0], [
            'PaymentSetting' => [
                'id'               => $paymentSettingId,
                'team_id'          => $teamId,
                'payment_base_day' => 1,
                'payment_skip_flg' => false
            ],
            'Team'           => [
                'timezone' => 9.0
            ],
        ]);

        // data_count: multi
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam([], $paymentSetting);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEquals(count($res), 2);
        $this->assertNotEquals($res[0]['PaymentSetting']['team_id'], $res[1]['PaymentSetting']['team_id']);
        $this->assertEquals($res[1], [
            'PaymentSetting' => [
                'id'               => $paymentSettingId,
                'team_id'          => $teamId,
                'payment_base_day' => 1,
                'payment_skip_flg' => false
            ],
            'Team'           => [
                'timezone' => 9.0
            ],
        ]);

    }

    public function test_findMonthlyChargeCcTeams_condJoins()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        // Not empty
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam();
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());

        // Team.service_use_status = free trial
        $this->Team->create();
        $this->Team->save([
            'id'                 => $teamId,
            'service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL
        ]);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);

        // Team.service_use_status = read only
        $this->Team->create();
        $this->Team->save([
            'id'                 => $teamId,
            'service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY
        ]);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);

        // Team.service_use_status = can't use service
        $this->Team->create();
        $this->Team->save([
            'id'                 => $teamId,
            'service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE
        ]);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);

        // Team deleted
        $this->Team->create();
        $this->Team->save([
            'id'                 => $teamId,
            'service_use_status' => Team::SERVICE_USE_STATUS_PAID,
            'del_flg'            => true
        ]);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);

        // CreditCard deleted
        $this->Team->create();
        $this->Team->save([
            'id'                 => $teamId,
            'service_use_status' => Team::SERVICE_USE_STATUS_PAID,
            'del_flg'            => false
        ]);
        $this->CreditCard->create();
        $this->CreditCard->updateAll(
            ['del_flg' => true],
            ['payment_setting_id' => $paymentSettingId]
        );
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);
    }

    public function test_findMonthlyChargeCcTeams_condPaymentSetting()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam();

        // PaymentSetting.type != credit card
        $this->PaymentSetting->create();
        $this->PaymentSetting->save([
            'id'   => $paymentSettingId,
            'type' => PaymentSetting::PAYMENT_TYPE_INVOICE
        ], false);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);

        // PaymentSetting deleted
        $this->PaymentSetting->create();
        $this->PaymentSetting->save([
            'id'      => $paymentSettingId,
            'type'    => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
            'del_flg' => true
        ], false);
        $res = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());
        $this->assertEmpty($res);
    }

    public function test_findMonthlyChargeTeams()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 0];
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_NG];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, [], $invoice);
        $firstRes = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::INVOICE());
        $this->assertEmpty($firstRes, "It will be empty. cause, credit_status != Invoice::CREDIT_STATUS_OK");
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        $Invoice->id = $invoiceId;
        $Invoice->saveField('credit_status', Invoice::CREDIT_STATUS_OK);
        $secondRes = $this->PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::INVOICE());
        $this->assertNotEmpty($secondRes);
    }

    public function test_getAmountPerUser()
    {
        $expectedAmount = 1500;
        $this->PaymentSetting->create();
        $this->PaymentSetting->save([
            'team_id'         => 999,
            'amount_per_user' => $expectedAmount
        ], false);

        $res = $this->PaymentSetting->getAmountPerUser(999);
        $this->assertEquals($res, $expectedAmount);
    }

    public function test_getAmountPerUser_null()
    {
        $this->PaymentSetting->deleteAll(['PaymentSetting.del_flg' => false]);

        $res = $this->PaymentSetting->getAmountPerUser(999);
        $this->assertEquals($res, null);
    }

    public function test_getCcByTeamId()
    {
        // TODO.Payment: implement test code
    }

    public function test_getUnique()
    {
        // TODO.Payment: implement test code
    }

    public function test_deleteByTeamId_success()
    {
        $teamId1 = 1923;
        $teamId2 = 1924;

        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id' => $teamId1
        ], false);

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id' => $teamId2
        ], false);

        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $teamId1]]));

        $PaymentSetting->softDeleteAllByTeamId($teamId1);

        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $teamId1, 'del_flg' => false]]));
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $teamId2, 'del_flg' => false]]));
    }
}
