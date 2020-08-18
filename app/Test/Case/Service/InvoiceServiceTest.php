<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AtobaraiResponseTraits', 'Test/Case/Service/Traits');
App::import('Service', 'InvoiceService');
App::import('Service', 'PaymentService');

use Goalous\Enum as Enum;

/**
 * Class PaymentServiceTest
 *
 * @property InvoiceService $InvoiceService
 */
class InvoiceServiceTest extends GoalousTestCase
{
    use AtobaraiResponseTraits;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.payment_setting',
        'app.payment_setting_change_log',
        'app.credit_card',
        'app.invoice',
        'app.invoice_history',
        'app.invoice_histories_charge_history',
        'app.charge_history',
        'app.team',
        'app.team_member',
        'app.user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->InvoiceService = ClassRegistry::init('InvoiceService');
        $this->PaymentService = ClassRegistry::init('PaymentService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PaymentService);
        parent::tearDown();
    }

    public function test_registerOrder()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_OK];
        list ($teamId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);
        $this->PaymentService->clearCachePaymentSettings();

        $targetChargeHistories = [
            (int)0 => [
                'id'                  => '1',
                'team_id'             => $teamId,
                'user_id'             => null,
                'payment_type'        => '0',
                'charge_type'         => '2',
                'amount_per_user'     => '1980',
                'total_amount'        => '1980',
                'tax'                 => '158',
                'charge_users'        => '1',
                'currency'            => '1',
                'charge_datetime'     => '1483109999',
                'result_type'         => '1',
                'max_charge_users'    => '1',
                'stripe_payment_code' => null,
                'del_flg'             => false,
                'deleted'             => null,
                'created'             => '1503384766',
                'modified'            => '1503384766'
            ],
            (int)1 => [
                'id'                  => '2',
                'team_id'             => $teamId,
                'user_id'             => null,
                'payment_type'        => '0',
                'charge_type'         => '2',
                'amount_per_user'     => '1980',
                'total_amount'        => '3960',
                'tax'                 => '310',
                'charge_users'        => '2',
                'currency'            => '1',
                'charge_datetime'     => '1480431600',
                'result_type'         => '1',
                'max_charge_users'    => '2',
                'stripe_payment_code' => null,
                'del_flg'             => false,
                'deleted'             => null,
                'created'             => '1503384766',
                'modified'            => '1503384766'
            ]
        ];

        $monthlyChargeHistory = [
            'team_id'          => (int)$teamId,
            'payment_type'     => (int)0,
            'charge_type'      => (int)0,
            'amount_per_user'  => (int)1980,
            'total_amount'     => (int)19800,
            'tax'              => (int)1584,
            'charge_users'     => (int)10,
            'currency'         => (int)1,
            'charge_datetime'  => (int)1483110000,
            'result_type'      => (int)1,
            'max_charge_users' => (int)10,
            'modified'         => (int)1503384766,
            'created'          => (int)1503384766,
            'id'               => '3',
            'monthlyStartDate' => '2016-12-31',
            'monthlyEndDate'   => '2017-01-30'
        ];

        $expectedRequestData = [
            'O_ReceiptOrderDate'     => '2016-12-31',
            'O_ServicesProvidedDate' => GoalousDateTime::now()->setTimeZoneByHour(9)->format('Y-m-d'),
            'O_EnterpriseId'         => '11528',
            'O_SiteId'               => '13868',
            'O_ApiUserId'            => '10141',
            'O_UseAmount'            => (int)27792,
            'O_Ent_Note'             => 'ご請求対象チーム名: Test Team.',
            'C_PostalCode'           => '123-4567',
            'C_UnitingAddress'       => '東京都台東区浅草橋1-2-3',
            'C_CorporateName'        => '株式会社これなんで商会',
            'C_NameKj'               => 'ゴラ橋ゴラ男',
            'C_NameKn'               => 'ごらはしごらお',
            'C_CpNameKj'             => 'ゴラ橋ゴラ男',
            'C_Phone'                => '03-1234-5678',
            'C_MailAddress'          => 'test@goalous.com',
            'C_EntCustId'            => (int)$teamId,
            'I_ItemNameKj_0'         => '12/30 Goalous追加利用料',
            'I_UnitPrice_0'          => (int)2138,
            'I_ItemNum_0'            => (int)1,
            'I_ItemNameKj_1'         => '11/30 Goalous追加利用料',
            'I_UnitPrice_1'          => (int)4270,
            'I_ItemNum_1'            => (int)1,
            'I_ItemNameKj_2'         => 'Goalous月額利用料(12/31 - 1/30)',
            'I_UnitPrice_2'          => (int)21384,
            'I_ItemNum_2'            => (int)1
        ];

        $orderDate = "2016-12-31";

        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', 'AK23553506', Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $targetChargeHistories[] = $monthlyChargeHistory;
        $res = $this->InvoiceService->registerOrder($teamId, $targetChargeHistories, $orderDate);

        $this->assertEquals($expectedRequestData, $res['requestData']);
        $this->assertEquals('success', $res['status']);
        $this->assertEquals('', $res['orderId']);
        $this->assertEquals('AK23553506', $res['systemOrderId']);
        $this->assertEquals(Enum\AtobaraiCom\Credit::OK, $res['orderStatus']['@cd']);
    }

    public function test_inquireCreditStatus()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        list ($teamId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        $targetChargeHistories = [
            (int)0 => [
                'id'                  => '1',
                'team_id'             => $teamId,
                'user_id'             => null,
                'payment_type'        => '0',
                'charge_type'         => '2',
                'amount_per_user'     => '1980',
                'total_amount'        => '1980',
                'tax'                 => '158',
                'charge_users'        => '1',
                'currency'            => '1',
                'charge_datetime'     => '1483109999',
                'result_type'         => '1',
                'max_charge_users'    => '1',
                'stripe_payment_code' => null,
                'del_flg'             => false,
                'deleted'             => null,
                'created'             => '1503384766',
                'modified'            => '1503384766'
            ],
            (int)1 => [
                'id'                  => '2',
                'team_id'             => $teamId,
                'user_id'             => null,
                'payment_type'        => '0',
                'charge_type'         => '2',
                'amount_per_user'     => '1980',
                'total_amount'        => '3960',
                'tax'                 => '310',
                'charge_users'        => '2',
                'currency'            => '1',
                'charge_datetime'     => '1480431600',
                'result_type'         => '1',
                'max_charge_users'    => '2',
                'stripe_payment_code' => null,
                'del_flg'             => false,
                'deleted'             => null,
                'created'             => '1503384766',
                'modified'            => '1503384766'
            ]
        ];
        $monthlyChargeHistory = [
            'team_id'          => (int)$teamId,
            'payment_type'     => (int)0,
            'charge_type'      => (int)0,
            'amount_per_user'  => (int)1980,
            'total_amount'     => (int)19800,
            'tax'              => (int)1584,
            'charge_users'     => (int)10,
            'currency'         => (int)1,
            'charge_datetime'  => (int)1483110000,
            'result_type'      => (int)1,
            'max_charge_users' => (int)10,
            'modified'         => (int)1503384766,
            'created'          => (int)1503384766,
            'id'               => '3',
            'monthlyStartDate' => '2016-12-31',
            'monthlyEndDate'   => '2017-01-30'
        ];


        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', 'AK23553506', Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $orderDate = "2016-12-31";
        $targetChargeHistories[] = $monthlyChargeHistory;
        $res = $this->InvoiceService->registerOrder($teamId, $targetChargeHistories, $orderDate);
        $orderId = $res['systemOrderId'];


        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiInquireCreditResponse([
                [
                    'orderId'     => $orderId,
                    'entOrderId'  => '',
                    'orderCreditStatus' => Enum\AtobaraiCom\Credit::OK(),
                ],
            ])
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $res = $this->InvoiceService->inquireCreditStatus($orderId);
        $this->assertEquals('success', $res['status']);
        $this->assertEquals(Enum\AtobaraiCom\Credit::OK, $res['results']['result']['orderStatus']['@cd']);
    }

    public function test_inquireCreditStatusNotFound()
    {
        $orderId = 'order_id_not_found_xxxxxxxxxxxxx';

        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiInquireCreditResponse([
                [
                    'orderId'     => $orderId,
                    'entOrderId'  => '',
                    'orderCreditStatus' => Enum\AtobaraiCom\Credit::ORDER_NOT_FOUND(),
                ],
            ])
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $res = $this->InvoiceService->inquireCreditStatus($orderId);
        $this->assertEquals('error', $res['status']);
        $this->assertEquals(Enum\AtobaraiCom\Credit::ORDER_NOT_FOUND, $res['results']['result']['orderStatus']['@cd']);
    }

    public function test_getMonthlyFeeItemName()
    {
        // payment base day: first day of the month
        $team = ['timezone' => 9];
        $paymentSetting = [
            'payment_base_day' => 1
        ];
        $this->PaymentService->clearCachePaymentSettings();
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_NG];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);
        $datetime = '2018-12-31 14:59:59';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(12/1 - 12/31)');

        $datetime = '2018-12-31 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(1/1 - 1/31)');

        $datetime = '2019-12-31 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(1/1 - 1/31)');

        // payment base day: last day of the month
        $this->PaymentSetting->clear();
        $this->PaymentSetting->id = $paymentSettingId;
        $this->PaymentSetting->save(['payment_base_day' => 31], false);
        $this->PaymentService->clearCachePaymentSettings();
        $datetime = '2018-02-27 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(2/28 - 3/30)');

        $datetime = '2018-03-30 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(3/31 - 4/29)');

        $datetime = '2018-05-15 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(4/30 - 5/30)');

        $datetime = '2018-12-30 15:00:00';
        $res = $this->InvoiceService->getMonthlyFeeItemName($teamId, strtotime($datetime));
        $this->assertEquals($res, 'Goalous月額利用料(12/31 - 1/30)');

    }

    public function test_updateCreditStatus()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::WAITING];

        // Create invoice and invoice history
        list ($teamId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);
        $this->addInvoiceHistory($teamId, [
            'order_datetime'    => time(),
            'system_order_code' => "test",
            'order_status'      => Enum\Model\Invoice\CreditStatus::WAITING,
        ]);

        // Get the histories
        $orders = $this->InvoiceHistory->getByOrderStatus(Enum\Model\Invoice\CreditStatus::WAITING)[0];
        $invoiceHistoryId = Hash::get($orders, 'InvoiceHistory.id');

        // Update status
        $res = $this->InvoiceService->updateCreditStatus($invoiceHistoryId, Enum\Model\Invoice\CreditStatus::OK);
        $this->assertTrue($res);

        // Get updated value
        $order = $this->InvoiceHistory->getById($invoiceHistoryId);
        $invoice = $this->Invoice->getByTeamId($teamId);
        $this->assertEquals(Enum\Model\Invoice\CreditStatus::OK, Hash::get($order, 'order_status'));
        $this->assertEquals(Enum\Model\Invoice\CreditStatus::OK, Hash::get($invoice, 'credit_status'));

        // Test invalid ID
        $res = $this->InvoiceService->updateCreditStatus(98766, Enum\Model\Invoice\CreditStatus::OK);
        $this->assertFalse($res);
    }

    public function test_getAddedUserAmount()
    {
        // TODO.Payment: add unit tests.
    }

    public function test_isSentInvoice()
    {
        // TODO.Payment: add unit tests.
    }
}