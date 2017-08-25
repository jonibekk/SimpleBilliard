<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PaymentService');

// TODO.Payment: there are these things
// ・Create test_validateCreate_validateError_** method related lack of company info and contact person
// ・Add unit test related calculate tax or charge after decide specification the tax_rate of foreign countries

/**
 * Class PaymentServiceTest
 *
 * @property PaymentService $PaymentService
 * @property PaymentSetting $PaymentSetting
 * @property CreditCard     $CreditCard
 * @property ChargeHistory  $ChargeHistory
 */
class PaymentServiceTest extends GoalousTestCase
{
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
        $this->PaymentService = ClassRegistry::init('PaymentService');
        $this->PaymentSetting = ClassRegistry::init('PaymentSetting');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->CreditCard = ClassRegistry::init('CreditCard');
        $this->Team = $this->Team ?? ClassRegistry::init('Team');
    }

    private function createTestPaymentData(array $data): array
    {
        $default = [
            'token'                          => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'type'                           => 1,
            'amount_per_user'                => 1800,
            'company_name'                   => 'ISAO',
            'company_country'                => 'JP',
            'company_post_code'              => '1110111',
            'company_region'                 => 'Tokyo',
            'company_city'                   => 'Taitou-ku',
            'company_street'                 => '*** ****',
            'company_tel'                    => '123456789',
            'contact_person_first_name'      => '太郎',
            'contact_person_first_name_kana' => 'タロウ',
            'contact_person_last_name'       => '東京',
            'contact_person_last_name_kana'  => 'トウキョウ',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
            'payment_base_day'               => 15,
            'currency'                       => 1
        ];
        return am($default, $data);
    }

    /**
     * Create a credit card payment for test
     */
    private function createCreditCardPayment()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => PaymentSetting::CURRENCY_TYPE_JPY,
            'company_country'  => 'JP'
        ]);
        $customerCode = 'cus_BDjPwryGzOQRBI';

        $userID = $this->createActiveUser(1);
        $res = $this->PaymentService->registerCreditCardPayment($payment, $customerCode, $userID);
        //$this->assertTrue($res);
    }

    public function test_validateCreate_validate_basic()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 10,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertTrue($res);
    }

    public function test_validateCreate_validateError_token()
    {
        // No Token
        $payment = $this->createTestPaymentData([
            'token'            => '',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_teamId()
    {
        // No team ID
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongType()
    {
        // Wrong type
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 3,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongPaymentDay()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 33,
            'currency'         => 1
        ]);

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongCurrency()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 12
        ]);

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_registerCreditCardPayment_basic()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $customerCode = 'cus_B3ygr9hxqg5evH';

        $userID = $this->createActiveUser(1);
        $res = $this->PaymentService->registerCreditCardPayment($payment, $customerCode, $userID);
        $this->assertTrue($res);
    }

    public function test_registerCreditCardPayment_noCustomerCode()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $customerCode = '';

        $userID = $this->createActiveUser(1);
        $res = $this->PaymentService->registerCreditCardPayment($payment, $customerCode, $userID);
        $this->assertFalse($res);
    }

    public function test_getNextBaseDate_basic()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 12,
        ]);
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 2,
        ], false);
        $currentTimestamp = strtotime("2017-01-01");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-01-02');

        $currentTimestamp = strtotime("2017-01-02");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        $currentTimestamp = strtotime("2017-01-03");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        $currentTimestamp = strtotime("2017-12-31");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2018-01-02');
    }

    public function test_getNextBaseDate_timezone()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 12.0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 2,
        ], false);

        $currentTimestamp = strtotime("2017-01-01 11:59:59");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-01-02');

        $currentTimestamp = strtotime("2017-01-01 12:00:00");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        $currentTimestamp = strtotime("2017-01-01 12:00:01");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        // timezone minus
        $this->Team->save([
            'timezone' => -12.0,
        ]);
        $this->Team->current_team = [];
        $this->_clearCache();

        $currentTimestamp = strtotime("2017-01-02 11:59:59");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-01-02');

        $currentTimestamp = strtotime("2017-01-02 12:00:00");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        $currentTimestamp = strtotime("2017-01-02 12:00:01");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        // timezone *.5
        $this->Team->save([
            'timezone' => -3.5,
        ]);
        $this->Team->current_team = [];
        $this->_clearCache();

        $currentTimestamp = strtotime("2017-01-02 03:29:59");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-01-02');

        $currentTimestamp = strtotime("2017-01-02 03:30:00");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

        $currentTimestamp = strtotime("2017-01-02 03:30:01");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-02');

    }

    public function test_getNextBaseDate_checkDate()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 28,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-02-27");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-28');

        $currentTimestamp = strtotime("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-03-28');

        $currentTimestamp = strtotime("2017-02-27");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-02-28');

        $currentTimestamp = strtotime("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-03-28');

        // No exist day
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 29,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-03-29');

        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-04-30");
        $res = $this->PaymentService->getNextBaseDate($currentTimestamp);
        $this->assertEquals($res, '2017-05-31');
    }

    public function test_getUseDaysUntilNext()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 28,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-02-01");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($currentTimestamp);
        $this->assertEquals($res, 27);

        $currentTimestamp = strtotime("2017-02-27");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($currentTimestamp);
        $this->assertEquals($res, 1);

        $currentTimestamp = strtotime("2017-02-28");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($currentTimestamp);
        $this->assertEquals($res, 28);

        $currentTimestamp = strtotime("2017-03-01");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($currentTimestamp);
        $this->assertEquals($res, 27);

    }

    public function test_getAllUseDaysOfMonth()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-01-01");
        $res = $this->PaymentService->getCurrentAllUseDays($currentTimestamp);
        $this->assertEquals($res, 31);

        $currentTimestamp = strtotime("2016-12-31");
        $res = $this->PaymentService->getCurrentAllUseDays($currentTimestamp);
        $this->assertEquals($res, 31);

        $currentTimestamp = strtotime("2017-01-31");
        $res = $this->PaymentService->getCurrentAllUseDays($currentTimestamp);
        $this->assertEquals($res, 31);

        $currentTimestamp = strtotime("2017-02-01");
        $res = $this->PaymentService->getCurrentAllUseDays($currentTimestamp);
        $this->assertEquals($res, 28);
    }

    public function test_calcTotalChargeByAddUsers_jpy()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
            'amount_per_user'  => 1980,
            'currency'         => PaymentSetting::CURRENCY_TYPE_JPY,
            'company_country'  => 'JP'
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 2138);

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 4276);

        $currentTimestamp = strtotime("2017-01-02");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 4138);

        $currentTimestamp = strtotime("2017-01-15");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 2344);

        $currentTimestamp = strtotime("2017-01-31");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 137);

        // If invalid payment base date
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-04-29");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 71);

        $currentTimestamp = strtotime("2017-04-30");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 4276);
    }

    public function test_calcTotalChargeByAddUsers_usd()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 0,
        ]);
        $teamId = 1;
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
            'amount_per_user'  => 16,
            'currency'         => PaymentSetting::CURRENCY_TYPE_USD,
            'company_country'  => 'US'
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 16);

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 32);

        $currentTimestamp = strtotime("2017-01-02");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 30.96);

        $currentTimestamp = strtotime("2017-01-15");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 17.54);

        $currentTimestamp = strtotime("2017-01-31");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 1.03);

        // If invalid payment base date
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-04-29");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 0.53);

        $currentTimestamp = strtotime("2017-04-30");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 32.0);
    }

    public function test_applyCreditCardCharge()
    {
        $this->createCreditCardPayment();

        $res = $this->PaymentService->applyCreditCardCharge(1, PaymentSetting::CHARGE_TYPE_MONTHLY_FEE,
            30, "Payment TEST");

        $this->assertNotNull($res);
        $this->assertArrayHasKey("error", $res);
        $this->assertArrayHasKey("success", $res);
        $this->assertFalse($res["error"]);
        $this->assertTrue($res["success"]);
    }

    public function test_registerCreditCardPaymentAndCharge()
    {
        $token = $this->createToken(self::CARD_MASTERCARD);
        $userID = $this->createActiveUser(1);
        $paymentData = $this->createTestPaymentData([
            'token'            => $token,
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1,
            'company_country'  => 'JP'
        ]);

        $res = $this->PaymentService->registerCreditCardPaymentAndCharge($userID, 1, $token, $paymentData);

        $this->assertNotNull($res);
        $this->assertArrayHasKey("error", $res);
        $this->assertArrayHasKey("customerId", $res);
        $this->assertFalse($res["error"]);

        $this->deleteCustomer($res["customerId"]);
    }

    public function test_registerInvoicePayment()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $this->createTestPaymentData([
            'team_id'          => 1,
            'type'             => PaymentSetting::PAYMENT_TYPE_INVOICE,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1,
            'company_country'  => 'JP'
        ]);
        unset($paymentData['token']);

        $res = $this->PaymentService->registerInvoicePayment($userID, 1, $paymentData);
        $this->assertTrue($res === true);
    }

    public function test_updateInvoice()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $this->createTestPaymentData([
            'team_id'          => 1,
            'type'             => PaymentSetting::PAYMENT_TYPE_INVOICE,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1,
            'company_country'  => 'JP'
        ]);
        unset($paymentData['token']);

        $this->PaymentService->registerInvoicePayment($userID, 1, $paymentData);
        $newData = $this->createTestPaymentData([
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ]);

        $res = $this->PaymentService->updateInvoice(1, $newData);
        $this->assertTrue($res === true);
    }

    public function test_updateInvoice_missingFields()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $this->createTestPaymentData([
            'team_id'          => 1,
            'type'             => PaymentSetting::PAYMENT_TYPE_INVOICE,
            'amount_per_user'  => 1800,
            'payment_base_day' => 15,
            'currency'         => 1,
            'company_country'  => 'JP'
        ]);
        unset($paymentData['token']);

        $this->PaymentService->registerInvoicePayment($userID, 1, $paymentData);
        $newData = $this->createTestPaymentData([
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
        ]);

        $res = $this->PaymentService->updateInvoice(1, $newData);
        
        $this->assertNotNull($res);
        $this->assertArrayHasKey("errorCode", $res);
        $this->assertArrayHasKey("message", $res);
    }

    public function test_findMonthlyChargeCcTeams_timezone()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 0];
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam($team);
        // Data count: 1
        // timezone: 0.0
        $time = strtotime('2016-01-01 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-01 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-12-31 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-01-02 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        // timezone: +
        $this->Team->save(['id' => $teamId, 'timezone' => 9.0], false);
        $time = strtotime('2016-12-31 14:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2016-12-31 15:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-01 14:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-01 15:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        // timezone: +
        $this->Team->save(['id' => $teamId, 'timezone' => 12.0], false);
        $time = strtotime('2016-12-31 11:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2016-12-31 12:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-01 11:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-01 12:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        // timezone: -
        $this->Team->save(['id' => $teamId, 'timezone' => -12.0], false);
        $time = strtotime('2017-01-01 11:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-01-01 12:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-02 11:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-02 12:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        // timezone: *.5
        $this->Team->save(['id' => $teamId, 'timezone' => -3.5], false);
        $time = strtotime('2017-01-01 03:29:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-01-01 03:30:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-02 03:29:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-01-02 03:30:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);
    }

    public function test_findMonthlyChargeCcTeams_chargeHistory()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 0];
        $paymentSetting = ['payment_base_day' => 28];
        list ($teamId, $paymentSettingId) = $this->createCcPaidTeam($team, $paymentSetting);
        $this->ChargeHistory->clear();
        $this->ChargeHistory->save([
            'team_id'            => $teamId,
            'payment_setting_id' => $paymentSettingId,
            'payment_type'       => ChargeHistory::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'        => ChargeHistory::CHARGE_TYPE_MONTHLY,
            'charge_datetime'    => strtotime('2017-01-28 23:59:59'),
        ], false);
        $chargeHistoryId = $this->ChargeHistory->getLastInsertID();

        // Data count: 1
        $time = strtotime('2017-01-28 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-01-28 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-02-28 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $time = strtotime('2017-02-28 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        // Add charge history
        $this->ChargeHistory->create();
        $this->ChargeHistory->save([
            'team_id'            => $teamId,
            'payment_setting_id' => $paymentSettingId,
            'payment_type'       => ChargeHistory::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'        => ChargeHistory::CHARGE_TYPE_MONTHLY,
            'charge_datetime'    => strtotime('2017-02-28 00:00:00'),
        ], false);
        $time = strtotime('2017-01-28 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-01-28 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-02-28 00:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-02-28 23:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        // Change payment base date
        // Check if payment base day is 29(Not exist day on Febuary)
        $this->Team->save(['id' => $teamId, 'timezone' => 9.0]);
        $this->PaymentSetting->save([
            'id'               => $paymentSettingId,
            'payment_base_day' => 29
        ]);
        $this->ChargeHistory->deleteAll(['del_flg' => false]);

        $time = strtotime('2017-01-27 15:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $this->ChargeHistory->create();
        $this->ChargeHistory->save([
            'team_id'            => $teamId,
            'payment_setting_id' => $paymentSettingId,
            'payment_type'       => ChargeHistory::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'        => ChargeHistory::CHARGE_TYPE_MONTHLY,
            'charge_datetime'    => strtotime('2017-01-28 00:00:00'),
        ], false);

        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-02-27 15:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 1);

        $this->ChargeHistory->save([
            'team_id'            => $teamId,
            'payment_setting_id' => $paymentSettingId,
            'payment_type'       => ChargeHistory::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'        => ChargeHistory::CHARGE_TYPE_MONTHLY,
            'charge_datetime'    => strtotime('2017-02-28 12:00:00'),
        ], false);

        $time = strtotime('2017-02-27 15:00:00');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);

        $time = strtotime('2017-02-28 14:59:59');
        $res = $this->PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertEquals(count($res), 0);
    }

    public function test_calcRelatedTotalChargeByUserCnt_invalid()
    {
        $this->PaymentService->clearCachePaymentSettings();
        $teamId = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 0,
            'tax'              => 0,
            'total_charge'     => 0,
        ]);

        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        // Sample price
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
            'amount_per_user'  => 100,
            'currency'         => PaymentSetting::CURRENCY_TYPE_JPY,
            'company_country'  => 'JP'
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 0);
        $this->assertEquals($res, [
            'sub_total_charge' => 0,
            'tax'              => 0,
            'total_charge'     => 0,
        ]);
    }

    public function test_calcRelatedTotalChargeByUserCnt_jp()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        // Sample price
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
            'amount_per_user'  => 100,
            'currency'         => PaymentSetting::CURRENCY_TYPE_JPY,
            'company_country'  => 'JP'
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 1);
        $this->assertEquals($res, [
            'sub_total_charge' => 100.0,
            'tax'              => 8.0,
            'total_charge'     => 108.0,
        ]);

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 1000.0,
            'tax'              => 80.0,
            'total_charge'     => 1080.0,
        ]);

        // Standard price
        $this->PaymentSetting->save([
            'team_id'         => $teamId,
            'amount_per_user' => 1980,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 1);
        $this->assertEquals($res, [
            'sub_total_charge' => 1980.0,
            'tax'              => 158.0,
            'total_charge'     => 2138.0,
        ]);

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 19800.0,
            'tax'              => 1584.0,
            'total_charge'     => 21384.0,
        ]);
    }

    public function test_calcRelatedTotalChargeByUserCnt_us()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        // Standard price
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 1,
            'amount_per_user'  => 17,
            'currency'         => PaymentSetting::CURRENCY_TYPE_USD,
            'company_country'  => 'PH'
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 1);
        $this->assertEquals($res, [
            'sub_total_charge' => 17.0,
            'tax'              => 0,
            'total_charge'     => 17.0,
        ]);

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 170.0,
            'tax'              => 0.0,
            'total_charge'     => 170.0,
        ]);
    }

    public function test_getTaxRateByCountryCode()
    {
        $res = $this->PaymentService->getTaxRateByCountryCode('JP');
        $this->assertEquals($res, 0.08);
        $res = $this->PaymentService->getTaxRateByCountryCode('US');
        $this->assertEquals($res, 0);
        $res = $this->PaymentService->getTaxRateByCountryCode('VN');
        $this->assertEquals($res, 0);
    }

    public function test_calcTax_jp()
    {
        $companyCountry = 'JP';
        $res = $this->PaymentService->calcTax($companyCountry, 100);
        $this->assertEquals($res, 8);

        $res = $this->PaymentService->calcTax($companyCountry, 1);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->calcTax($companyCountry, 12);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->calcTax($companyCountry, 13);
        $this->assertEquals($res, 1);
    }

    public function test_calcTax_us()
    {
        $companyCountry = 'UK';
        $res = $this->PaymentService->calcTax($companyCountry, 100);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->calcTax($companyCountry, 1000);
        $this->assertEquals($res, 0);
    }

    public function test_processDecimalPointForAmount_jp()
    {
        $currencyType = PaymentSetting::CURRENCY_TYPE_JPY;
        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 100);
        $this->assertEquals($res, 100);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 1);
        $this->assertEquals($res, 1);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.1);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.01);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.99);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 9.9);
        $this->assertEquals($res, 9);

    }

    public function test_processDecimalPointForAmount_us()
    {
        $currencyType = PaymentSetting::CURRENCY_TYPE_USD;

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 100);
        $this->assertEquals($res, 100);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 1);
        $this->assertEquals($res, 1);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.1);
        $this->assertEquals($res, 0.1);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.01);
        $this->assertEquals($res, 0.01);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.001);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 0.999);
        $this->assertEquals($res, 0.99);

        $res = $this->PaymentService->processDecimalPointForAmount($currencyType, 9.9);
        $this->assertEquals($res, 9.9);
    }

    public function test_getDefaultAmountPerUserByCountry()
    {
        $county = 'JP';
        $res = $this->PaymentService->getDefaultAmountPerUserByCountry($county);
        $this->assertEquals($res, PaymentService::AMOUNT_PER_USER_JPY);

        $county = 'US';
        $res = $this->PaymentService->getDefaultAmountPerUserByCountry($county);
        $this->assertEquals($res, PaymentService::AMOUNT_PER_USER_USD);

        $county = 'PH';
        $res = $this->PaymentService->getDefaultAmountPerUserByCountry($county);
        $this->assertEquals($res, PaymentService::AMOUNT_PER_USER_USD);
    }

    public function test_getCurrencyTypeByCountry()
    {
        $county = 'JP';
        $res = $this->PaymentService->getCurrencyTypeByCountry($county);
        $this->assertEquals($res, PaymentSetting::CURRENCY_TYPE_JPY);

        $county = 'US';
        $res = $this->PaymentService->getCurrencyTypeByCountry($county);
        $this->assertEquals($res, PaymentSetting::CURRENCY_TYPE_USD);

        $county = 'PH';
        $res = $this->PaymentService->getCurrencyTypeByCountry($county);
        $this->assertEquals($res, PaymentSetting::CURRENCY_TYPE_USD);
    }

    public function test_checkIllegalChoiceCountry()
    {
        $ccCounty = 'JP';
        $companyCountry = 'JP';
        $res = $this->PaymentService->checkIllegalChoiceCountry($ccCounty, $companyCountry);
        $this->assertTrue($res);

        $ccCounty = 'JP';
        $companyCountry = 'US';
        $res = $this->PaymentService->checkIllegalChoiceCountry($ccCounty, $companyCountry);
        $this->assertFalse($res);

        $ccCounty = 'UK';
        $companyCountry = 'US';
        $res = $this->PaymentService->checkIllegalChoiceCountry($ccCounty, $companyCountry);
        $this->assertTrue($res);

        $ccCounty = 'US';
        $companyCountry = 'US';
        $res = $this->PaymentService->checkIllegalChoiceCountry($ccCounty, $companyCountry);
        $this->assertTrue($res);

        $ccCounty = 'PH';
        $companyCountry = 'JP';
        $res = $this->PaymentService->checkIllegalChoiceCountry($ccCounty, $companyCountry);
        $this->assertFalse($res);
    }

    public function test_updatePayerInfo()
    {
        $this->createCreditCardPayment();
        $updateData = [
            'company_name'                   => 'ISAO',
            'company_post_code'              => '000000',
            'company_country'                => 'US',
            'company_region'                 => 'NY',
            'company_city'                   => 'Central Park',
            'company_street'                 => 'Somewhere',
            'company_tel'                    => '123456789',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ];

        // Update payment data
        $userId = $this->createActiveUser(1);
        $res = $this->PaymentService->updatePayerInfo(1, $userId, $updateData);
        $this->assertTrue($res);

        // Retrieve data from db
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        $data = Hash::get($PaymentSetting->getCcByTeamId(1), "PaymentSetting");

        // Compare updated with saved data
        $data = array_intersect_key($data, $updateData);
        $this->assertEquals($updateData, $data);
    }

    public function test_updatePayerInfo_missingFields()
    {
        $this->createCreditCardPayment();
        $updateData = [
            'company_name'                   => 'ISAO',
            'company_post_code'              => '',
            'company_country'                => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ];

        // Update payment data
        $userId = $this->createActiveUser(1);
        $res = $this->PaymentService->updatePayerInfo(1, $userId, $updateData);

        $this->assertNotNull($res);
        $this->assertArrayHasKey("errorCode", $res);
        $this->assertArrayHasKey("message", $res);
    }

    function test_findMonthlyChargeInvoiceTeams()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 1];
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_OK];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        // time is difference as base date
        $time = strtotime('2017-01-02') - (9 * HOUR);
        $res = $this->PaymentService->findMonthlyChargeInvoiceTeams($time);
        $this->assertEmpty($res);

        // time is same as base date
        $time = strtotime('2017-01-01') - (9 * HOUR);
        $res = $this->PaymentService->findMonthlyChargeInvoiceTeams($time);
        $this->assertNotEmpty($res);

        $this->addInvoiceHistory($teamId, [
            'order_date'        => '2017-01-01',
            'system_order_code' => "test",
        ]);
        $res = $this->PaymentService->findMonthlyChargeInvoiceTeams($time);
        $this->assertEmpty($res);
    }

    function test_findTargetInvoiceChargeHistories()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_OK];
        $time = strtotime('2016-12-31') - (9 * HOUR);
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);
        // expected scope is from 2016-11-30 - 2016-12-30
        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getStartTimestampByTimezone('2016-12-31', 9)
        ]);
        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertEmpty($res, "2016-12-31 is out of scope");

        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getEndTimestampByTimezone('2016-12-30', 9)
        ]);
        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertCount(1, $res);

        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getStartTimestampByTimezone('2016-11-30', 9)
        ]);
        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertCount(2, $res);

        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getStartTimestampByTimezone('2016-11-29', 9)
        ]);
        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertCount(2, $res);

        $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_date'        => '2016-12-01',
                'system_order_code' => "test",
            ],
            [
                'payment_type'     => ChargeHistory::PAYMENT_TYPE_INVOICE,
                'charge_type'      => ChargeHistory::CHARGE_TYPE_ACTIVATE_USER,
                'amount_per_user'  => 1980,
                'total_amount'     => 3960,
                'tax'              => 310,
                'charge_users'     => 2,
                'max_charge_users' => 2,
                'charge_datetime'  => AppUtil::getEndTimestampByTimezone('2016-12-01', 9)
            ]
        );

        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertCount(2, $res);

    }

    function test_registerInvoice()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Invoice::CREDIT_STATUS_OK];
        $time = strtotime('2016-12-31') - (9 * HOUR);
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getEndTimestampByTimezone('2016-12-30', 9)
        ]);
        $res = $this->PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
        $this->assertCount(1, $res);

        $this->_addInvoiceChargeHistory($teamId, [
            'charge_datetime' => AppUtil::getStartTimestampByTimezone('2016-11-30', 9)
        ]);
        $res = $this->PaymentService->registerInvoice($teamId, 10, $time);
        $this->assertTrue($res);
        // checking invoiceHistory
        /** @var InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $this->assertCount(1, $InvoiceHistory->find('all', ['conditions' => ['team_id' => $teamId]]));
        // checking chargeHistory
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->assertCount(3, $ChargeHistory->find('all', ['conditions' => ['team_id' => $teamId]]));
        // checking invoiceHistory and chargeHistory relation
        /** @var InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory */
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $this->assertCount(3, $InvoiceHistoriesChargeHistory->find('all'));
    }

    function _addInvoiceChargeHistory($teamId, $data)
    {
        $data = am([
            'payment_type'     => ChargeHistory::PAYMENT_TYPE_INVOICE,
            'charge_type'      => ChargeHistory::CHARGE_TYPE_ACTIVATE_USER,
            'amount_per_user'  => 1980,
            'total_amount'     => 3960,
            'tax'              => 310,
            'charge_users'     => 2,
            'max_charge_users' => 2,
            'charge_datetime'  => ""
        ], $data);
        return $this->addChargeHistory($teamId, $data);
    }

    public function test_getAmountPerUser()
    {
        // TODO: implement test code
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
}
