<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AtobaraiResponseTraits', 'Test/Case/Service/Traits');
App::import('Service', 'ChargeHistoryService');
App::import('DateTime', 'GoalousDateTime');

use Goalous\Enum as Enum;

/**
 * Class ChargeHistoryService
 *
 * @property PaymentService       $PaymentService
 * @property ChargeHistoryService $ChargeHistoryService
 */
class ChargeHistoryServiceTest extends GoalousTestCase
{
    use AtobaraiResponseTraits;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.payment_setting',
        'app.credit_card',
        'app.charge_history',
        'app.team',
        'app.invoice',
        'app.user',
        'app.team_member',
        'app.price_plan_purchase_team',
        'app.campaign_team',
        'app.invoice_history',
        'app.invoice_histories_charge_history',
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
        $this->PaymentSetting = ClassRegistry::init('PaymentSetting');
        $this->PaymentService = ClassRegistry::init('PaymentService');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->CreditCard = ClassRegistry::init('CreditCard');
    }

    public function test_isLatestChargeSucceed()
    {
        $this->PaymentSetting->save([
            'id'                             => 1,
            'team_id'                        => 1,
            'type'                           => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'currency'                       => Enum\Model\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => 1500000000,
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => 1500000000,
            'modified'                       => 1500000000,
        ], false);
        $this->PaymentSetting->save([
            'id'                             => 2,
            'team_id'                        => 2,
            'type'                           => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'currency'                       => Enum\Model\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Team2 Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => 1500000000,
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => 1500000000,
            'modified'                       => 1500000000,
        ], false);
        $this->PaymentSetting->save([
            'id'                             => 3,
            'team_id'                        => 3,
            'type'                           => Enum\Model\PaymentSetting\Type::INVOICE,
            'currency'                       => Enum\Model\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Team3 Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
            'modified'                       => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
        ], false);

        $this->ChargeHistory->save([
            'id'                  => 1,
            'team_id'             => 1,
            'user_id'             => 1,
            'payment_type'        => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\Model\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000000,
            'result_type'         => Enum\Model\ChargeHistory\ResultType::SUCCESS,
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
            'payment_type'        => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\Model\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\Model\ChargeHistory\ResultType::SUCCESS,
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
            'payment_type'        => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\Model\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\Model\ChargeHistory\ResultType::FAIL,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1500000001,
            'modified'            => 1500000001,
        ], false);
        $this->CreditCard->save([
            'id'                 => 1,
            'team_id'            => 1,
            'payment_setting_id' => 1,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ], false);
        $this->CreditCard->save([
            'id'                 => 2,
            'team_id'            => 2,
            'payment_setting_id' => 2,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ], false);

        // last payment succeeded
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(1));
        // failed last payment
        $this->assertTrue($this->ChargeHistoryService->isLatestChargeFailed(2));
        // setting is invoice
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(3));
    }

    public function test_getReceipt_creditCardMonthly()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];
        $card = ['customer_code' => $this->createCustomer(self::CARD_VISA)];

        list($teamId, $paymentSettingId) = $this->createCcPaidTeam($team, $paySetting, $card);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $historyId = $this->addChargeHistory($teamId, [
            'amount_per_user' => 1980,
            'payment_type'    => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'total_amount'    => 2000,
            'tax'             => 20,
            'charge_datetime' => strtotime('2017-08-01'),
            'charge_type'     => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'charge_users'    => 20
        ]);
        $res = $this->ChargeHistoryService->getReceipt($historyId);
        $this->assertEquals($res['Team']['name'], 'Test Team');
        $this->assertEquals($res['ChargeHistory']['sub_total_with_currency'], '¥2,000');
        $this->assertEquals($res['ChargeHistory']['total_with_currency'], '¥2,020');
        $this->assertEquals($res['ChargeHistory']['tax_with_currency'], '¥20');
        $this->assertEquals($res['ChargeHistory']['charge_users'], 20);
        $this->assertTrue($res['PaymentSetting']['is_card']);
        $this->assertTrue($res['ChargeHistory']['is_monthly']);
    }

    public function test_getReceipt_creditCardAddUser()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];
        $card = ['customer_code' => $this->createCustomer(self::CARD_VISA)];

        list($teamId, $paymentSettingId) = $this->createCcPaidTeam($team, $paySetting, $card);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $historyId = $this->addChargeHistory($teamId, [
            'amount_per_user' => 29700,
            'payment_type'    => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'total_amount'    => 30000,
            'tax'             => 300,
            'charge_datetime' => strtotime('2017-08-01'),
            'charge_type'     => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'charge_users'    => 10
        ]);
        $res = $this->ChargeHistoryService->getReceipt($historyId);
        $this->assertEquals($res['Team']['name'], 'Test Team');
        $this->assertEquals($res['ChargeHistory']['sub_total_with_currency'], '¥30,000');
        $this->assertEquals($res['ChargeHistory']['total_with_currency'], '¥30,300');
        $this->assertEquals($res['ChargeHistory']['tax_with_currency'], '¥300');
        $this->assertEquals($res['ChargeHistory']['charge_users'], 10);
        $this->assertTrue($res['PaymentSetting']['is_card']);
        $this->assertFalse($res['ChargeHistory']['is_monthly']);
    }

    public function test_getReceipt_invoiceMonthly()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];

        list($teamId, $paymentSettingId) = $this->createInvoicePaidTeam($team, $paySetting);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $historyId = $this->addChargeHistory($teamId, [
            'amount_per_user' => 1980,
            'payment_type'    => Enum\Model\PaymentSetting\Type::INVOICE,
            'total_amount'    => 2000,
            'tax'             => 20,
            'charge_datetime' => strtotime('2017-08-01'),
            'charge_type'     => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'charge_users'    => 20
        ]);
        $res = $this->ChargeHistoryService->getReceipt($historyId);
        $this->assertEquals($res['Team']['name'], 'Test Team');
        $this->assertEquals($res['ChargeHistory']['sub_total_with_currency'], '¥2,000');
        $this->assertEquals($res['ChargeHistory']['total_with_currency'], '¥2,020');
        $this->assertEquals($res['ChargeHistory']['tax_with_currency'], '¥20');
        $this->assertFalse($res['PaymentSetting']['is_card']);
        $this->assertEquals($res['ChargeHistory']['charge_users'], 20);
        $this->assertTrue($res['ChargeHistory']['is_monthly']);
    }

    public function test_getReceipt_invoiceAddUser()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];

        list($teamId, $paymentSettingId) = $this->createInvoicePaidTeam($team, $paySetting);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $historyId = $this->addChargeHistory($teamId, [
            'amount_per_user' => 1980,
            'payment_type'    => Enum\Model\PaymentSetting\Type::INVOICE,
            'total_amount'    => 2000,
            'tax'             => 20,
            'charge_datetime' => strtotime('2017-08-01'),
            'charge_type'     => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'charge_users'    => 20
        ]);
        $res = $this->ChargeHistoryService->getReceipt($historyId);
        $this->assertEquals($res['Team']['name'], 'Test Team');
        $this->assertEquals($res['ChargeHistory']['sub_total_with_currency'], '¥2,000');
        $this->assertEquals($res['ChargeHistory']['total_with_currency'], '¥2,020');
        $this->assertEquals($res['ChargeHistory']['tax_with_currency'], '¥20');
        $this->assertFalse($res['PaymentSetting']['is_card']);
        $this->assertEquals($res['ChargeHistory']['charge_users'], 20);
        $this->assertFalse($res['ChargeHistory']['is_monthly']);
    }

    public function test_addInvoiceRecharge()
    {
        $teamId = 1;
        $histories[] = [
            'total_amount' => 1000,
            'tax'          => 80
        ];
        $baseCorrectData = [
            'payment_type'                => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::RECHARGE,
            'amount_per_user'             => 1225,
            'charge_users'                => 0,
            'currency'                    => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => 0,
            'campaign_team_id'            => null,
            'price_plan_purchase_team_id' => null,

        ];
        // history 1
        GoalousDateTime::setTestNow('2018-01-01 01:23:45');
        $res = $this->ChargeHistoryService->addInvoiceRecharge($teamId, $histories);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['team_id'], $teamId);
        foreach ($baseCorrectData as $k => $v) {
            $this->assertEquals($res[$k], $v);
        }
        $this->assertEquals($res['total_amount'], 1000);
        $this->assertEquals($res['tax'], 80);
        $this->assertEquals($res['charge_datetime'], GoalousDateTime::now()->getTimestamp());

        // history N
        $histories = [
            [
                'total_amount' => 2000,
                'tax'          => 160
            ],
            [
                'total_amount' => 10000,
                'tax'          => 800
            ]
        ];
        $res = $this->ChargeHistoryService->addInvoiceRecharge($teamId, $histories);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['team_id'], $teamId);
        foreach ($baseCorrectData as $k => $v) {
            $this->assertEquals($res[$k], $v);
        }
        $this->assertEquals($res['total_amount'], 12000);
        $this->assertEquals($res['tax'], 960);
        $this->assertEquals($res['charge_datetime'], GoalousDateTime::now()->getTimestamp());

        // campaign team
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createInvoiceCampaignTeam($pricePlanGroupId = 1,
            $pricePlanCode = '1-1');
        $baseCorrectData['amount_per_user'] = 0;
        $baseCorrectData['campaign_team_id'] = $campaignTeamId;
        $baseCorrectData['price_plan_purchase_team_id'] = $pricePlanPurchaseId;
        $res = $this->ChargeHistoryService->addInvoiceRecharge($teamId, $histories);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['team_id'], $teamId);
        foreach ($baseCorrectData as $k => $v) {
            $this->assertEquals($res[$k], $v);
        }
        $this->assertEquals($res['total_amount'], 12000);
        $this->assertEquals($res['tax'], 960);
        $this->assertEquals($res['charge_datetime'], GoalousDateTime::now()->getTimestamp());
    }

    public function test_processForReceiptRechargeInvoice()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];

        list($teamId, $paymentSettingId) = $this->createInvoicePaidTeam($team, $paySetting);
        list($chargeHistoryId, $invoiceHistoryId) = $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_datetime'    => GoalousDateTime::now()->getTimestamp(),
                'system_order_code' => "test1",
                'order_status'      => Enum\Model\Invoice\CreditStatus::NG
            ],
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                'amount_per_user'  => 1980,
                'total_amount'     => 3960,
                'tax'              => 310,
                'charge_users'     => 2,
                'max_charge_users' => 2,
                'charge_datetime'  => strtotime('2018-02-28')
            ]
        );

        // mocking credit invoice as succeed
        $returningOrderId = 'AK12345678';
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', @$returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $lastInsertedChargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $arrayForReceipt = $this->ChargeHistoryService->getReceipt($lastInsertedChargeHistoryId);
        $this->assertEquals($chargeHistoryId, $arrayForReceipt["ChargeHistory"]["recharge_history_ids"][0]);
    }

    public function test_processForReceiptRechargeCreditCard()
    {
        $team = ['name' => 'Test Team', 'timezone' => 9];
        $paySetting = ['currency' => PaymentSetting::CURRENCY_TYPE_JPY];

        list($teamId, $paymentSettingId) = $this->createCcPaidTeam($team, $paySetting);
        $historyId = $this->addChargeHistory($teamId, [
            'amount_per_user' => 1980,
            'payment_type'    => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'total_amount'    => 2000,
            'tax'             => 20,
            'charge_datetime' => strtotime('2017-08-01'),
            'charge_type'     => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'charge_users'    => 20
        ]);
        $this->PaymentService->reorderCreditCardCharge($this->ChargeHistory->getById($historyId));
        $lastInsertedChargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $arrayForReceipt = $this->ChargeHistoryService->getReceipt($lastInsertedChargeHistoryId);
        $this->assertEquals($historyId, $arrayForReceipt["ChargeHistory"]["recharge_history_ids"][0]);
    }
}
