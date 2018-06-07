<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('AppShell', 'Console/Command');
App::uses('MonthlyCreditCardChargeShell', 'Console/Command/Batch/Payment/Console/Command');

use Goalous\Enum as Enum;

/**
 * Class MonthlyCreditCardChargeShellTest
 *
 * @property MonthlyCreditCardChargeShell $MonthlyCreditCardChargeShell
 * @property PaymentService               $PaymentService
 * @property ChargeHistory                $ChargeHistory
 */
class MonthlyCreditCardChargeShellTest extends GoalousTestCase
{
    public $MonthlyCreditCardChargeShell;
    public $PaymentService;
    public $ChargeHistory;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.user',
        'app.team',
        'app.team_member',
        'app.payment_setting',
        'app.credit_card',
        'app.charge_history',
        'app.campaign_team',
        'app.price_plan_purchase_team',
        'app.mst_price_plan',
        'app.view_price_plan',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();
        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);

        $this->PaymentService = ClassRegistry::init('PaymentService');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');

        $this->MonthlyCreditCardChargeShell = new MonthlyCreditCardChargeShell($output, $error, $in);
        $this->MonthlyCreditCardChargeShell->initialize();
        $this->MonthlyCreditCardChargeShell->startup();
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('MonthlyCreditCardCharge', $this->MonthlyCreditCardChargeShell->name);
    }

    function test_main_regular_plans()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 0];
        $usersCount = 10;
        list ($teamId) = $this->createCcPaidTeam($team, [], [], $usersCount);
        $this->Team->current_team_id = $teamId;

        // Execute batch
        $this->MonthlyCreditCardChargeShell->params['targetTimestamp'] = strtotime('2017-01-01');
        $this->MonthlyCreditCardChargeShell->main();

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $paymentSetting = $this->PaymentService->get($teamId);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\ChargeHistory\ChargeType::MONTHLY_FEE(), $paymentSetting);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $expected = [
            'id'                          => 1,
            'team_id'                     => $teamId,
            'user_id'                     => null,
            'payment_type'                => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'             => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'                => $chargeInfo['sub_total_charge'],
            'tax'                         => $chargeInfo['tax'],
            'charge_users'                => $usersCount,
            'currency'                    => Enum\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => $usersCount,
            'campaign_team_id'            => null,
            'price_plan_purchase_team_id' => null
        ];

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
        $this->assertTrue($res['total_amount'] > $res['amount_per_user']);
    }

    function test_main_campaign_plans()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1, $pricePlanCode = '1-1');
        $usersCount = 10;
        $this->Team->current_team_id = $teamId;
        $this->createActiveUsers($teamId, $usersCount - 1);

        // Execute batch
        $this->MonthlyCreditCardChargeShell->params['targetTimestamp'] = strtotime('2017-01-01');
        $this->MonthlyCreditCardChargeShell->main();

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $paymentSetting = $this->PaymentService->get($teamId);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\ChargeHistory\ChargeType::MONTHLY_FEE(), $paymentSetting);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $expected = [
            'id'                          => 1,
            'team_id'                     => $teamId,
            'user_id'                     => null,
            'payment_type'                => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'             => 0,
            'total_amount'                => $chargeInfo['sub_total_charge'],
            'tax'                         => $chargeInfo['tax'],
            'charge_users'                => $usersCount,
            'currency'                    => Enum\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => $usersCount,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId
        ];

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
        $this->assertTrue($res['total_amount'] > $res['amount_per_user']);
    }

}
