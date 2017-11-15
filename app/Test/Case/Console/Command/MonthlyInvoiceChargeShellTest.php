<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('AppShell', 'Console/Command');
App::uses('AtobaraiResponseTraits', 'Test/Case/Service/Traits');
App::uses('MonthlyInvoiceChargeShell', 'Console/Command/Batch/Payment/Console/Command');
App::uses('GoalousDateTime', 'DateTime');

use Goalous\Model\Enum as Enum;

/**
 * Class MonthlyInvoiceChargeShellTest
 *
 * @property MonthlyInvoiceChargeShell $MonthlyInvoiceChargeShell
 * @property PaymentService            $PaymentService
 * @property CampaignService           $CampaignService
 */
class MonthlyInvoiceChargeShellTest extends GoalousTestCase
{
    use AtobaraiResponseTraits;

    public $MonthlyInvoiceChargeShell;
    public $PaymentService;
    public $CampaignService;

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
        'app.invoice',
        'app.invoice_history',
        'app.invoice_histories_charge_history',
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
        $this->CampaignService = ClassRegistry::init('CampaignService');

        $this->MonthlyInvoiceChargeShell = new MonthlyInvoiceChargeShell($output, $error, $in);
        $this->MonthlyInvoiceChargeShell->initialize();
        $this->MonthlyInvoiceChargeShell->startup();
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('MonthlyInvoiceCharge', $this->MonthlyInvoiceChargeShell->name);
    }

    function test_main_regular()
    {
        $testNow = '2017-01-01';
        GoalousDateTime::setTestNow($testNow);
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 9];
        $usersCount = 10;
        list ($teamId, $paymentSettingId) = $this->createInvoicePaidTeam($team, [], [], $usersCount);
        $this->Team->current_team_id = $teamId;

        // mocking credit invoice as succeed
        $returningOrderId = 'AK12345678';
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', @$returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        // Execute batch
        $this->MonthlyInvoiceChargeShell->params['targetTimestamp'] = strtotime($testNow);
        $this->MonthlyInvoiceChargeShell->main();

        // Check charge history
        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $paymentSetting = $this->PaymentService->get($teamId);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\ChargeHistory\ChargeType::MONTHLY_FEE(), $paymentSetting);
        $this->assertTrue($res['charge_datetime'] <= time());
        $expected = [
            'id'                          => 1,
            'team_id'                     => $teamId,
            'user_id'                     => null,
            'payment_type'                => Enum\PaymentSetting\Type::INVOICE,
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
        $this->assertTrue($res['total_amount'] > $res['amount_per_user']);

        // Check if invoice was created
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId($teamId);
        $this->assertNotEmpty($invoice);
        $this->assertEquals($paymentSettingId, $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Invoice\CreditStatus::OK, $invoice['credit_status']);

        // Check invoice history was created
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->findAllByTeamId($teamId);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all');
        $this->assertCount(1, $InvoiceHistoriesChargeHistories);

        // Add new users
        $testNow = '2017-01-15';
        GoalousDateTime::setTestNow($testNow);
        $usersCount = 5;
        $this->createActiveUsers($teamId, $usersCount);

        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\ChargeHistory\ChargeType::USER_INCREMENT_FEE(), $paymentSetting);
        $maxChargeUserCnt = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\ChargeHistory\ChargeType::USER_INCREMENT_FEE(), 5);

        $historyData = [
            'team_id'          => $teamId,
            'payment_type'     => Enum\PaymentSetting\Type::INVOICE,
            'charge_type'      => Enum\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'amount_per_user'  => $paymentSetting['amount_per_user'],
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $usersCount,
            'currency'         => $paymentSetting['currency'],
            'charge_datetime'  => strtotime($testNow),
            'result_type'      => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $maxChargeUserCnt
        ];
        $this->addChargeHistory($teamId, $historyData);

        // Execute batch
        $testNow = '2017-02-01';
        GoalousDateTime::setTestNow($testNow);

        // mocking credit invoice as succeed
        $returningOrderId = 'AK12345679';
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', @$returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $this->MonthlyInvoiceChargeShell->params['targetTimestamp'] = strtotime($testNow);
        $this->MonthlyInvoiceChargeShell->main();

        // Check if invoice was created
        $invoice = $Invoice->getByTeamId($teamId);
        $this->assertNotEmpty($invoice);
        $this->assertEquals($paymentSettingId, $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Invoice\CreditStatus::OK, $invoice['credit_status']);

        // Check invoice history was created
        $invoiceHistories = $InvoiceHistory->getByOrderDate($teamId, $testNow);
        $this->assertCount(1, $invoiceHistories);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all', ['conditions' => ['invoice_history_id' => $invoiceHistories['InvoiceHistory']['id']]]);
        $this->assertCount(2, $InvoiceHistoriesChargeHistories);
    }

    function test_main_campaign()
    {
        $testNow = '2017-01-01';
        GoalousDateTime::setTestNow($testNow);
        $this->Team->deleteAll(['del_flg' => false]);
        $usersCount = 10;
        list ($teamId, $paymentSettingId, $pricePlanPurchaseId) = $this->createInvoiceCampaignTeam($pricePlanGroupId = 1,
            $pricePlanId = 1, $pricePlanCode = '1-1');
        $this->Team->current_team_id = $teamId;
        $this->createActiveUsers($teamId, $usersCount - 1);

        // mocking credit invoice as succeed
        $returningOrderId = 'AK12345678';
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', @$returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        // Execute batch
        $this->MonthlyInvoiceChargeShell->params['targetTimestamp'] = strtotime($testNow);
        $this->MonthlyInvoiceChargeShell->main();

        // Check charge history
        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $paymentSetting = $this->PaymentService->get($teamId);
        $pricePlanPurchase = $this->CampaignService->getPricePlanPurchaseTeam($teamId);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\ChargeHistory\ChargeType::MONTHLY_FEE(), $paymentSetting);
        $this->assertTrue($res['charge_datetime'] <= time());
        $expected = [
            'id'                          => 1,
            'team_id'                     => $teamId,
            'user_id'                     => null,
            'payment_type'                => Enum\PaymentSetting\Type::INVOICE,
            'charge_type'                 => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'             => 0,
            'total_amount'                => $chargeInfo['sub_total_charge'],
            'tax'                         => $chargeInfo['tax'],
            'charge_users'                => $usersCount,
            'currency'                    => Enum\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => $usersCount,
            'campaign_team_id'            => $pricePlanPurchase['CampaignTeam']['id'],
            'price_plan_purchase_team_id' => $pricePlanPurchaseId
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertTrue($res['total_amount'] > $res['amount_per_user']);

        // Check if invoice was created
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId($teamId);
        $this->assertNotEmpty($invoice);
        $this->assertEquals($paymentSettingId, $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Invoice\CreditStatus::OK, $invoice['credit_status']);

        // Check invoice history was created
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->findAllByTeamId($teamId);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all');
        $this->assertCount(1, $InvoiceHistoriesChargeHistories);
    }
}
