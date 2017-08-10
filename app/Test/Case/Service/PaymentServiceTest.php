<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PaymentService');

// TODO: Create test_validateCreate_validateError_** method related lack of company info and contact person

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
            'currency'         => 1
        ]);
        $customerCode = 'cus_B59aNmiTO3IZjg';

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
            'currency'         => PaymentSetting::CURRENCY_JPY
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 1980);

        $currentTimestamp = strtotime("2017-01-01");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 3960);

        $currentTimestamp = strtotime("2017-01-02");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 3832);

        $currentTimestamp = strtotime("2017-01-15");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 2171);

        $currentTimestamp = strtotime("2017-01-31");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 127);

        // If invalid payment base date
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        $currentTimestamp = strtotime("2017-04-29");
        $userCnt = 1;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 66);

        $currentTimestamp = strtotime("2017-04-30");
        $userCnt = 2;
        $res = $this->PaymentService->calcTotalChargeByAddUsers($userCnt, $currentTimestamp);
        $this->assertEquals($res, 3960);
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
            'currency'         => PaymentSetting::CURRENCY_USD
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
        $this->assertEquals($res, 32);
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
