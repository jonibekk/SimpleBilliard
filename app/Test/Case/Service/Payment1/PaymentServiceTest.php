<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AtobaraiResponseTraits', 'Test/Case/Service/Traits');
App::import('Service', 'PaymentService');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagClient');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagKey');

use Goalous\Enum as Enum;

// TODO.Payment: there are these things
// ・Create test_validateCreate_validateError_** method related lack of company info and contact person
// ・Add unit test related calculate tax or charge after decide specification the tax_rate of foreign countries

/**
 * Class PaymentServiceTest
 *
 * @property PaymentService          $PaymentService
 * @property PaymentSetting          $PaymentSetting
 * @property PaymentSettingChangeLog $PaymentSettingChangeLog
 * @property CreditCard              $CreditCard
 * @property ChargeHistory           $ChargeHistory
 * @property TeamMember              $TeamMember
 * @property CampaignService         $CampaignService
 */
class PaymentServiceTest extends GoalousTestCase
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
        'app.job_category',
        'app.member_type',
        'app.user',
        'app.price_plan_purchase_team',
        'app.mst_price_plan_group',
        'app.mst_price_plan',
        'app.view_price_plan',
        'app.campaign_team',
        'app.team_translation_language',
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
        $this->PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->CreditCard = ClassRegistry::init('CreditCard');
        $this->Team = $this->Team ?? ClassRegistry::init('Team');
        $this->CampaignService = ClassRegistry::init('CampaignService');
        $this->TeamMember = $this->TeamMember ?? ClassRegistry::init('TeamMember');
        $this->InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $paymentKeyFlagClient = new PaymentFlagClient();

        $paymentFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_FLAG_NAME);
        $paymentKeyFlagClient->write($paymentFlagKey, 1);
        $paymentDateKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_START_DATE_NAME);
        $paymentKeyFlagClient->write($paymentDateKey, '20191217');
    }

    function test_get_single()
    {
        $modelName = 'PaymentSetting';
        $teamId = 1;

        $createData = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($createData, false);

        /* First data */
        // Save cache
        $data = $this->PaymentService->get($teamId);
        $this->assertNotEmpty($data);
        $cacheList = $this->PaymentService->getCacheList();
        $this->assertSame($data, $cacheList[$modelName][$teamId]);

        // Check data is as same as data getting from db directly
        $ret = $this->PaymentSetting->useType()->findById($teamId)[$modelName];
        // Extract only db record columns(exclude additional data. e.g. img_url)
        $tmp = array_intersect_key($data, $ret);
        $this->assertSame($tmp, $ret);

        // Get from cache
        $data = $this->PaymentService->get($teamId);
        $this->assertSame($data, $cacheList[$modelName][$teamId]);

        /* Multiple data */
        // Save cache
        $teamId2 = 2;

        $createData = $this->createTestPaymentData(['team_id' => $teamId2, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($createData, false);

        $data2 = $this->PaymentService->get($teamId2);
        $this->assertNotEmpty($data2);
        $cacheList = $this->PaymentService->getCacheList();
        $this->assertSame($data2, $cacheList[$modelName][$teamId2]);

        // Check data is as same as data getting from db directly
        $ret = $this->PaymentSetting->useType()->findById($teamId2)[$modelName];
        $tmp = array_intersect_key($data2, $ret);
        $this->assertSame($tmp, $ret);

        // Get from cache
        $data2 = $this->PaymentService->get($teamId2);
        $this->assertSame($data2, $cacheList[$modelName][$teamId2]);
        $this->assertNotEquals($data, $data2);

        /* Empty */
        $teamId = 0;
        $data = $this->PaymentService->get($teamId);
        $this->assertSame($data, []);
        $cacheList = $this->PaymentService->getCacheList();
        $this->assertFalse(array_key_exists($teamId, $cacheList[$modelName]));

        $teamId = 9999999;
        $data = $this->PaymentService->get($teamId);
        $this->assertSame($data, []);
        $cacheList = $this->PaymentService->getCacheList();
        $this->assertSame($data, $cacheList[$modelName][$teamId]);
    }

    private function createTestPaymentData(array $data): array
    {
        $default = [
            'type'                           => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'amount_per_user'                => PaymentService::AMOUNT_PER_USER_JPY,
            'company_name'                   => 'ISAO',
            'company_country'                => 'JP',
            'company_post_code'              => '1110111',
            'company_region'                 => 'Tokyo',
            'company_city'                   => 'Taitou-ku',
            'company_street'                 => '*** ****',
            'contact_person_first_name'      => '太郎',
            'contact_person_first_name_kana' => 'タロウ',
            'contact_person_last_name'       => '東京',
            'contact_person_last_name_kana'  => 'トウキョウ',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
            'payment_base_day'               => 15,
            'currency'                       => Enum\Model\PaymentSetting\Currency::JPY
        ];
        return am($default, $data);
    }

    private function createTestPaymentDataForReg(array $data = []): array
    {
        $default = [
            'company_name'                   => 'ISAO',
            'company_country'                => 'JP',
            'company_post_code'              => '1110111',
            'company_region'                 => 'Tokyo',
            'company_city'                   => 'Taitou-ku',
            'company_street'                 => 'Chuo1-2-3',
            'contact_person_first_name'      => '太郎',
            'contact_person_first_name_kana' => 'タロウ',
            'contact_person_last_name'       => '東京',
            'contact_person_last_name_kana'  => 'トウキョウ',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
        ];
        return am($default, $data);
    }

    public function test_validateCreate_validate_basic()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 10,
            'type'             => 1,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);

        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertTrue($res);
    }

    public function test_validateCreate_validateError_token()
    {
        // No Token
        $payment = $this->createTestPaymentData([
            'token'            => '',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_teamId()
    {
        // No team ID
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'type'             => 1,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertTrue($res);
    }

    public function test_validateCreate_validateError_wrongType()
    {
        // Wrong type
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 3,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 15,
            'currency'         => 1
        ]);
        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongPaymentDay()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 33,
            'currency'         => 1
        ]);

        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongCurrency()
    {
        $payment = $this->createTestPaymentData([
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'payment_base_day' => 15,
            'currency'         => 12
        ]);

        $res = $this->PaymentService->validateCreateCc($payment);
        $this->assertFalse($res === true);
    }

    public function test_getNextBaseDate_basic()
    {
        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->clear();
        $this->Team->id = $teamId;
        $this->Team->save([
            'timezone' => 12,
        ], false);
        $this->PaymentSetting->create();
        $a = $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 2,
        ], false);
        GoalousDateTime::setTestNow("2017-01-01");
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-01-02');

        GoalousDateTime::setTestNow("2017-01-02");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        GoalousDateTime::setTestNow("2017-01-03");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        GoalousDateTime::setTestNow("2017-12-31");
        $res = $this->PaymentService->getNextBaseDate($teamId);
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

        GoalousDateTime::setTestNow("2017-01-01 11:59:59");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-01-02');

        GoalousDateTime::setTestNow("2017-01-01 12:00:00");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        GoalousDateTime::setTestNow("2017-01-01 12:00:01");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        // timezone minus
        $this->Team->save([
            'timezone' => -12.0,
        ]);
        $this->Team->current_team = [];
        $this->_clearCache();

        GoalousDateTime::setTestNow("2017-01-02 11:59:59");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-01-02');

        GoalousDateTime::setTestNow("2017-01-02 12:00:00");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        GoalousDateTime::setTestNow("2017-01-02 12:00:01");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        // timezone *.5
        $this->Team->save([
            'timezone' => -3.5,
        ]);
        $this->Team->current_team = [];
        $this->_clearCache();

        GoalousDateTime::setTestNow("2017-01-02 03:29:59");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-01-02');

        GoalousDateTime::setTestNow("2017-01-02 03:30:00");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-02');

        GoalousDateTime::setTestNow("2017-01-02 03:30:01");
        $res = $this->PaymentService->getNextBaseDate($teamId);
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

        GoalousDateTime::setTestNow("2017-02-27");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-28');

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-03-28');

        GoalousDateTime::setTestNow("2017-02-27");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-02-28');

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-03-28');

        // No exist day
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 29,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getNextBaseDate($teamId);
        $this->assertEquals($res, '2017-03-29');

        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-04-30");
        $res = $this->PaymentService->getNextBaseDate($teamId);
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

        GoalousDateTime::setTestNow("2017-02-01");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 27);

        GoalousDateTime::setTestNow("2017-02-27");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 28);

        GoalousDateTime::setTestNow("2017-03-01");
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
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

        GoalousDateTime::setTestNow("2017-01-01");
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow("2016-12-31");
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow("2017-01-31");
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow("2017-02-01");
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 28);
    }

    public function test_calcRelatedTotalChargeByAddUsers_jpy()
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

        GoalousDateTime::setTestNow("2017-01-01");
        $userCnt = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 1980);
        $this->assertEquals($res['tax'], 198);
        $this->assertEquals($res['total_charge'], 2178);

        GoalousDateTime::setTestNow("2017-01-01");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 3960);
        $this->assertEquals($res['tax'], 396);
        $this->assertEquals($res['total_charge'], 4356);

        GoalousDateTime::setTestNow("2017-01-02");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 3832);
        $this->assertEquals($res['tax'], 383);
        $this->assertEquals($res['total_charge'], 4215);

        GoalousDateTime::setTestNow("2017-01-15");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 2171);
        $this->assertEquals($res['tax'], 217);
        $this->assertEquals($res['total_charge'], 2388);

        GoalousDateTime::setTestNow("2017-01-31");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 127);
        $this->assertEquals($res['tax'], 12);
        $this->assertEquals($res['total_charge'], 139);

        // If invalid payment base date
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-04-29");
        $userCnt = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 66);
        $this->assertEquals($res['tax'], 6);
        $this->assertEquals($res['total_charge'], 72);

        GoalousDateTime::setTestNow("2017-04-30");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 3960);
        $this->assertEquals($res['tax'], 396);
        $this->assertEquals($res['total_charge'], 4356);
    }

    public function test_calcRelatedTotalChargeByAddUsers_usd()
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

        GoalousDateTime::setTestNow("2017-01-01");
        $userCnt = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 16);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 16);

        GoalousDateTime::setTestNow("2017-01-01");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 32);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 32);

        GoalousDateTime::setTestNow("2017-01-02");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 30.96);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 30.96);

        GoalousDateTime::setTestNow("2017-01-15");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 17.54);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 17.54);

        GoalousDateTime::setTestNow("2017-01-31");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 1.03);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 1.03);

        // If invalid payment base date
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-04-29");
        $userCnt = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 0.53);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0.53);

        GoalousDateTime::setTestNow("2017-04-30");
        $userCnt = 2;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $userCnt);
        $this->assertEquals($res['sub_total_charge'], 32.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 32.0);
    }

    public function test_calcRelatedTotalChargeByAddUsers_exception()
    {

        $teamId = 1;
        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, 0);
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        $res = $this->PaymentService->calcRelatedTotalChargeByAddUsers(1, 0);
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);
    }

    public function test_applyCreditCardCharge_exception()
    {
        $teamId = 1;
        $userId = 2;
        try {
            $res = null;
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), 0,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertTrue(strpos($res, 'Charge user count is 0') !== false);

        try {
            $res = null;
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), 1,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEquals(strpos($res, 'Payment setting or Credit card settings does not exist.'), 0);

        try {
            $res = null;
            $savePaymentSetting = [
                'team_id'          => $teamId,
                'type'             => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
                'payment_base_day' => 1
            ];
            $this->PaymentSetting->create();
            $this->PaymentSetting->save($savePaymentSetting, false);

            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), 1,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEquals(strpos($res, 'Payment setting or Credit card settings does not exist.'), 0);

        try {
            $res = null;
            list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], [], ['customer_code' => '']);
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), 1,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEquals(strpos($res, 'Failed to charge.'), 0);

        try {
            $res = null;
            list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], [], ['customer_code' => '']);
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), 1,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEquals(strpos($res, 'Failed to charge.'), 0);

        // check if charge fail on customer's credit card
        // no rollback, insert charge history of failed
        /** @var $CreditCardService CreditCardService */
        $CreditCardService = ClassRegistry::init('CreditCardService');
        $customer = $CreditCardService->registerCustomer("tok_chargeCustomerFail", "test@goalous.com", "Goalous TEST");
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        try {
            $res = null;
            list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], [],
                ['customer_code' => $customer["customer_id"]]);
            $this->PaymentService->applyCreditCardCharge(
                $teamId,
                Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
                1,
                $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEquals(strpos($res, 'Failed to charge.'), 0);
        $hist = $ChargeHistory->find('first', [
            'conditions' => [
                'team_id' => $teamId,
            ]
        ]);
        // assert failed history is saved
        $this->assertEquals(Enum\Model\ChargeHistory\ResultType::FAIL, $hist['ChargeHistory']['result_type']);
        $this->assertNotEmpty($hist['ChargeHistory']['stripe_payment_code']);
        $this->CreditCardService->deleteCustomer($customer["customer_id"]);
    }

    public function test_applyCreditCardCharge_jp()
    {
        /* Case charge user:1 country:JPY */
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam();
        $this->Team->current_team_id = $teamId;
        $userId = $this->createActiveUser($teamId);
        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
        $this->assertTrue($res['total_amount'] < $res['amount_per_user']);

        /* Case charge user:multiple country:JPY */
        $res = "";
        try {
            $chargeUserCnt = 100;
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $totalAmount = PaymentService::AMOUNT_PER_USER_JPY * $chargeUserCnt;
        $expected = [
            'id'               => 2,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax('JP', $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');

        /* Case max charge user */
        $res = "";
        try {
            $chargeUserCnt = 99;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $totalAmount = PaymentService::AMOUNT_PER_USER_JPY * $chargeUserCnt;
        $expected = [
            'id'               => 3,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => 199,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
        $this->assertTrue($res['total_amount'] < $res['amount_per_user'] * $chargeUserCnt);
    }

    public function test_applyCreditCardCharge_specifyTime()
    {
        /* Case charge user:1 country:JPY */
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam();
        $this->Team->current_team_id = $teamId;
        $userId = $this->createActiveUser($teamId);
        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge(
                $teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt,
                $userId,
                1500000000
            );
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
            'charge_datetime'  => 1500000000,
        ];

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
        $this->assertTrue($res['total_amount'] < $res['amount_per_user']);
    }

    public function test_applyCreditCardCharge_foreign()
    {
        $companyCountry = 'US';
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], [
            'amount_per_user' => PaymentService::AMOUNT_PER_USER_USD,
            'currency'        => Enum\Model\PaymentSetting\Currency::USD,
            'company_country' => $companyCountry
        ]);
        $userId = $this->createActiveUser($teamId);

        /* Case charge user:1*/
        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $totalAmount = PaymentService::AMOUNT_PER_USER_USD * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_USD,
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($companyCountry, $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']) * 100);
        $this->assertEquals($chargeRes->currency, 'usd');

        /* Case charge user:multiple*/
        $res = "";
        try {
            $chargeUserCnt = 9;
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $totalAmount = PaymentService::AMOUNT_PER_USER_USD * $chargeUserCnt;
        $expected = [
            'id'               => 2,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_USD,
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($companyCountry, $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']) * 100);
        $this->assertEquals($chargeRes->currency, 'usd');
    }

    public function test_applyCreditCardCharge_dailyPayment()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();
        $companyCountry = 'JP';
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam(['timezone' => 9.0], [
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'company_country'  => $companyCountry,
            'payment_base_day' => 1
        ]);
        $userId = $this->createActiveUser($teamId);

        /* Daily payment: JPY */
        // payment_base_day:31
        $this->Team->current_team_id = $teamId;

        GoalousDateTime::setTestNow('2017-12-30 14:59:59');
        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);

        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);

        GoalousDateTime::setTestNow('2017-12-30 15:00:00');
        $res = "";
        try {
            $chargeUserCnt = 3;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);

        GoalousDateTime::setTestNow('2018-12-31 15:00:00');
        $res = "";
        try {
            $chargeUserCnt = 100;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);

        $this->PaymentSetting->clear();
        $this->PaymentSetting->id = $paymentSettingId;
        $this->PaymentSetting->save([
            'amount_per_user' => PaymentService::AMOUNT_PER_USER_USD,
            'currency'        => Enum\Model\PaymentSetting\Currency::USD,
            'company_country' => 'US',
        ], false);
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        /* Daily payment: USD */
        GoalousDateTime::setTestNow('2018-02-27 14:59:59');
        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $stripeCharge = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        $this->assertEquals($chargeInfo['total_charge'] * 100, $stripeCharge['amount']);

        $res = "";
        try {
            $chargeUserCnt = 1;
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $stripeCharge = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        $this->assertEquals($chargeInfo['total_charge'] * 100, $stripeCharge['amount']);
    }

    public function test_applyCreditCardCharge_campaign()
    {
        // Activation
        $this->Team->deleteAll(['del_flg' => false]);
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1,
            $pricePlanCode = '1-1');
        $usersCount = 10;
        $this->Team->current_team_id = $teamId;
        $this->createActiveUsers($teamId, $usersCount - 1);
        $userId = $this->createActiveUser($teamId);
        $res = "";
        try {
            $this->PaymentService->applyCreditCardCharge($teamId,
                Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $usersCount, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertNotEmpty($res);

        // Monthly charge
        $this->Team->deleteAll(['del_flg' => false]);
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1,
            $pricePlanCode = '1-1');
        $usersCount = 10;
        $this->Team->current_team_id = $teamId;
        $this->createActiveUsers($teamId, $usersCount - 1);
        $userId = $this->createActiveUser($teamId);
        $res = "";
        try {
            $this->PaymentService->applyCreditCardCharge($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
                $usersCount, $userId);
        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $paymentSetting = $this->PaymentService->get($teamId);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByType($teamId, $usersCount,
            Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(), $paymentSetting);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $expected = [
            'id'                          => 2,
            'team_id'                     => $teamId,
            'user_id'                     => $userId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'             => 0,
            'total_amount'                => $chargeInfo['sub_total_charge'],
            'tax'                         => $chargeInfo['tax'],
            'charge_users'                => $usersCount,
            'currency'                    => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
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

    public function test_charge_ccJp()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $companyCountry = 'JP';
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam(
            [
                'timezone' => -12
            ],
            [
                'type'             => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                'company_country'  => $companyCountry,
                'payment_base_day' => 15
            ]
        );
        $this->Team->current_team_id = $teamId;
        $userId = $this->createActiveUser($teamId);
        GoalousDateTime::setTestNow('2017-12-31 11:59:59');
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        // $this->assertTrue($res['total_amount'] <= $amountPerUser / 31);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));
        $maxChargeUserCnt = $res['max_charge_users'];

        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $expected = [
            'id'               => 2,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $maxChargeUserCnt + $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        // $this->assertTrue($res['total_amount'] <= $amountPerUser / 31);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));
        $maxChargeUserCnt = $res['max_charge_users'];

        try {
            $res = "";
            $chargeUserCnt = 3;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 3,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $maxChargeUserCnt + $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        // $this->assertTrue($res['total_amount'] < $amountPerUser);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));

        /* Daily payment */
        GoalousDateTime::setTestNow('2017-12-31 12:00:00');

        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        // $this->assertTrue($res['total_amount'] == $amountPerUser);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));

        GoalousDateTime::setTestNow('2017-01-01 12:00:00');
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertNotEmpty($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        $this->assertTrue($res['total_amount'] < $amountPerUser);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));

        GoalousDateTime::setTestNow('2017-02-28 11:59:59');
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertNotEmpty($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        $this->assertTrue($res['total_amount'] < $amountPerUser);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));

        GoalousDateTime::setTestNow('2017-02-28 12:00:00');
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertNotEmpty($res['stripe_payment_code']);
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $this->assertEquals($res['total_amount'], $chargeInfo['sub_total_charge']);
        $this->assertEquals($res['tax'], $chargeInfo['tax']);
        // $this->assertTrue($res['total_amount'] == $amountPerUser);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']));

    }

    public function test_charge_ccForeign()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $companyCountry = 'PH';
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam(
            [
                'timezone' => -3.5
            ],
            [
                'type'             => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                'company_country'  => $companyCountry,
                'currency'         => Enum\Model\PaymentSetting\Currency::USD,
                'amount_per_user'  => PaymentService::AMOUNT_PER_USER_USD,
                'payment_base_day' => 13
            ]
        );
        $this->Team->current_team_id = $teamId;
        $userId = $this->createActiveUser($teamId);

        GoalousDateTime::setTestNow('2020-08-12 03:29:59');
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $amountPerUser = PaymentService::AMOUNT_PER_USER_USD;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => 0,
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']) * 100);
        $this->assertTrue($res['total_amount'] < $amountPerUser);
        $this->assertEquals($chargeRes->currency, 'usd');

        GoalousDateTime::setTestNow('2020-08-12 03:30:00');
        try {
            $res = "";
            $chargeUserCnt = 1000;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt);
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $amountPerUser = PaymentService::AMOUNT_PER_USER_USD;
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 2,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            // 'amount_per_user'  => $amountPerUser,
            // 'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => 0,
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt + 1,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        // $this->assertEquals($chargeRes->amount, ($res['total_amount'] + $res['tax']) * 100);
        // $this->assertTrue($res['total_amount'] == $amountPerUser * 1000);
        $this->assertEquals($chargeRes->currency, 'usd');
    }

    public function test_charge_invoice()
    {
        $companyCountry = 'JP';
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam();
        $userId = $this->createActiveUser($teamId);

        // Charge user:1
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($res['charge_datetime'] <= time());
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
            'amount_per_user'  => $amountPerUser,
            // 'total_amount'     => $totalAmount,
            // 'tax'              => $this->PaymentService->calcTax($companyCountry, $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);

        // Charge user:multiple
        try {
            $res = "";
            $chargeUserCnt = 1;
            $this->PaymentService->charge($teamId, Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                $chargeUserCnt,
                $userId);

        } catch (Exception $e) {
            $res = $e->getMessage();
        }
        $this->assertEmpty($res);

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($res['charge_datetime'] <= time());
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
            'amount_per_user'  => $amountPerUser,
            // 'total_amount'     => $totalAmount,
            // 'tax'              => $this->PaymentService->calcTax($companyCountry, $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt + 1,
        ];
        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);

    }

    public function test_registerCreditCardPaymentAndCharge_jp()
    {
        $token = 'tok_jp';
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = 1;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15',
            'pre_register_amount_per_user' => 1000
        ], false);

        $userId = $this->createActiveUser($teamId);
        $paymentData = $this->createTestPaymentDataForReg([]);

        $res = $this->PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $paymentData);
        // Check response success
        $this->assertNotNull($res);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customerId", $res);

        // Check saved PaymentSetting data
        $paySetting = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paySetting);
        $this->assertEquals(array_intersect_key($paySetting, $paymentData), $paymentData);
        $this->assertEquals($paySetting['type'], Enum\Model\PaymentSetting\Type::CREDIT_CARD);

        $timezone = $this->Team->getTimezone();
        $this->assertEquals($paySetting['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paySetting['currency'], Enum\Model\PaymentSetting\Currency::JPY);
        $this->assertEquals($paySetting['amount_per_user'], '1000');

        // Check saved CreditCard data
        $cc = $this->CreditCard->getByTeamId($teamId);
        $this->assertNotEmpty($cc);
        $this->assertEquals($cc['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($cc['customer_code'], $res["customerId"]);

        // Check if saved customer into Stripe
        $customer = \Stripe\Customer::retrieve($cc['customer_code']);
        $this->assertEquals($customer->id, $cc['customer_code']);
        $this->assertNotEmpty($customer->sources->data[0]);

        // Check saved PaymentSettingChangeLog data
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($payLog['plain_data'], $paySetting);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $amountPerUser = 1000;
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paySetting['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paySetting['amount_per_user'],
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($paySetting['company_country'], $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());
        $this->assertNotEmpty($history['stripe_payment_code']);

        $chargeRes = \Stripe\Charge::retrieve($history['stripe_payment_code']);
        $this->assertNotEmpty($chargeRes);
        $this->assertEquals($chargeRes->amount, ($history['total_amount'] + $history['tax']));
        $this->assertEquals($chargeRes->currency, 'jpy');

        // Check if team status updated
        $team = $this->Team->getById($teamId);
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        $this->deleteCustomer($res["customerId"]);
    }

    public function test_registerCreditCardPaymentAndCharge_foreign()
    {
        $token = 'tok_au';
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = 1;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15'
        ], false);

        $userId = $this->createActiveUser($teamId);
        $paymentData = $this->createTestPaymentDataForReg([
            'company_country' => 'US'
        ]);

        $res = $this->PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $paymentData);
        // Check response success
        $this->assertNotNull($res);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customerId", $res);

        // Check saved PaymentSetting data
        $paySetting = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paySetting);
        $this->assertEquals(array_intersect_key($paySetting, $paymentData), $paymentData);
        $this->assertEquals($paySetting['type'], Enum\Model\PaymentSetting\Type::CREDIT_CARD);

        $timezone = $this->Team->getTimezone();
        $this->assertEquals($paySetting['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paySetting['currency'], Enum\Model\PaymentSetting\Currency::USD);
        $this->assertEquals($paySetting['amount_per_user'], PaymentService::AMOUNT_PER_USER_USD);

        // Check saved CreditCard data
        $cc = $this->CreditCard->getByTeamId($teamId);
        $this->assertNotEmpty($cc);
        $this->assertEquals($cc['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($cc['customer_code'], $res["customerId"]);

        // Check if saved customer into Stripe
        $customer = \Stripe\Customer::retrieve($cc['customer_code']);
        $this->assertEquals($customer->id, $cc['customer_code']);
        $this->assertNotEmpty($customer->sources->data[0]);

        // Check saved PaymentSettingChangeLog data
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($payLog['plain_data'], $paySetting);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $amountPerUser = PaymentService::AMOUNT_PER_USER_USD;
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paySetting['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paySetting['amount_per_user'],
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($paySetting['company_country'], $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());
        $this->assertNotEmpty($history['stripe_payment_code']);

        $chargeRes = \Stripe\Charge::retrieve($history['stripe_payment_code']);
        $this->assertNotEmpty($chargeRes);
        $this->assertEquals($chargeRes->amount, ($history['total_amount'] + $history['tax']) * 100);
        $this->assertEquals($chargeRes->currency, 'usd');

        // Check if team status updated
        $team = $this->Team->getById($teamId);
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        $this->deleteCustomer($res["customerId"]);
    }

    public function test_registerCreditCardPaymentAndCharge_fail()
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
        $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $token = 'tok_chargeCustomerFail';
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = 1;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15'
        ], false);

        $userId = $this->createActiveUser($teamId);
        $paymentData = $this->createTestPaymentDataForReg([]);

        $countBeforeRollbackPaymentSetting = $PaymentSetting->find('count');
        $countBeforeRollbackCreditCard = $CreditCard->find('count');
        $countBeforeRollbackPaymentSettingChangeLog = $PaymentSettingChangeLog->find('count');
        $countBeforeRollbackTeamMember = $TeamMember->find('count');

        $res = $this->PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $paymentData);
        // Check response failed
        $this->assertTrue($res['error']);
        $this->assertEquals(500, $res['errorCode']);

        // check if payment_settings is rollback
        $this->assertEquals($countBeforeRollbackPaymentSetting, $PaymentSetting->find('count'));
        $this->assertEquals($countBeforeRollbackCreditCard, $CreditCard->find('count'));
        $this->assertEquals($countBeforeRollbackPaymentSettingChangeLog, $PaymentSettingChangeLog->find('count'));
        $this->assertEquals($countBeforeRollbackTeamMember, $TeamMember->find('count'));
    }

    public function test_registerCreditCardPaymentAndCharge_campaign()
    {
        $token = 'tok_jp';
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = 1;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15',
        ], false);

        $userId = $this->createActiveUser($teamId);
        $this->createCampaignTeam($teamId, $campaignType = 0, $pricePlanGroupId = 1);
        $paymentData = $this->createTestPaymentDataForReg(['price_plan_code' => $pricePlanCode = '1-1']);

        $res = $this->PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $paymentData);
        // Check response success
        $this->assertNotNull($res);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customerId", $res);

        // Check saved PaymentSetting data
        $paySetting = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paySetting);
        $this->assertEquals(array_intersect_key($paymentData, $paySetting),
            array_intersect_key($paySetting, $paymentData));
        $this->assertEquals($paySetting['type'], Enum\Model\PaymentSetting\Type::CREDIT_CARD);

        $timezone = $this->Team->getTimezone();
        $this->assertEquals($paySetting['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paySetting['currency'], Enum\Model\PaymentSetting\Currency::JPY);
        $this->assertEquals($paySetting['amount_per_user'], '0');

        // Check saved CreditCard data
        $cc = $this->CreditCard->getByTeamId($teamId);
        $this->assertNotEmpty($cc);
        $this->assertEquals($cc['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($cc['customer_code'], $res["customerId"]);

        // Check if saved customer into Stripe
        $customer = \Stripe\Customer::retrieve($cc['customer_code']);
        $this->assertEquals($customer->id, $cc['customer_code']);
        $this->assertNotEmpty($customer->sources->data[0]);

        // Check saved PaymentSettingChangeLog data
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paySetting['id']);
        $this->assertEquals($payLog['plain_data'], $paySetting);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $chargeInfo = $this->CampaignService->getChargeInfo($pricePlanCode);
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paySetting['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paySetting['amount_per_user'],
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());
        $this->assertNotEmpty($history['stripe_payment_code']);

        $chargeRes = \Stripe\Charge::retrieve($history['stripe_payment_code']);
        $this->assertNotEmpty($chargeRes);
        $this->assertEquals($chargeRes->amount, ($history['total_amount'] + $history['tax']));
        $this->assertEquals($chargeRes->currency, 'jpy');

        // Check if team status updated
        $team = $this->Team->getById($teamId);
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        // Check campaign purchase
        $isPurchased = $this->CampaignService->purchased($teamId);
        $this->assertTrue($isPurchased === true);

        $this->deleteCustomer($res["customerId"]);
    }

    public function test_registerInvoicePayment()
    {
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = $teamId;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15'
        ], false);

        $userId = $this->createActiveUser($teamId);
        $paymentData = $invoiceData = $this->createTestPaymentDataForReg([]);

        // Register invoice
        $returningOrderId = 'AK12345678';

        // mocking credit invoice as succeed
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));
        $res = $this->PaymentService->registerInvoicePayment($userId, $teamId, $paymentData, $invoiceData);
        $this->assertTrue($res === true);

        // Check team status
        $team = $this->Team->getById($teamId);
        $timezone = $this->Team->getTimezone();
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        // Check if payment settings was created
        $paymentSettings = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paymentSettings);
        $this->assertEquals($paymentData, array_intersect_key($paymentSettings, $paymentData));
        $this->assertEquals($paymentSettings['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paymentSettings['currency'], Enum\Model\PaymentSetting\Currency::JPY);
        $this->assertEquals($paymentSettings['amount_per_user'], PaymentService::AMOUNT_PER_USER_JPY);

        // Check if PaymentSettingChangeLog was created
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paymentSettings['id']);
        $this->assertEquals($payLog['plain_data'], $paymentSettings);

        // Check if invoice was created
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId(1);
        $data = array_intersect_key($invoice, $invoiceData);
        $this->assertEquals(array_intersect_key($invoiceData, $data), $data);
        $this->assertEquals($paymentSettings['id'], $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Model\Invoice\CreditStatus::WAITING, $invoice['credit_status']);

        // Check invoice history was created
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->findAllByTeamId($teamId);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all');
        $this->assertCount(1, $InvoiceHistoriesChargeHistories);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $amountPerUser = PaymentService::AMOUNT_PER_USER_JPY;
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paymentSettings['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paymentSettings['amount_per_user'],
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($paymentSettings['company_country'], $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());
    }

    public function test_registerInvoicePayment_campaign()
    {
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = $teamId;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15'
        ], false);

        $userId = $this->createActiveUser($teamId);
        $this->createCampaignTeam($teamId, $campaignType = 0, $pricePlanGroupId = 1);
        $paymentData = $invoiceData = $this->createTestPaymentDataForReg(['price_plan_code' => $pricePlanCode = '1-1']);

        // Register invoice
        $returningOrderId = 'AK12345678';

        // mocking credit invoice as succeed
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));
        $res = $this->PaymentService->registerInvoicePayment($userId, $teamId, $paymentData, $invoiceData, true,
            $pricePlanCode);
        $this->assertTrue($res === true);

        // Check team status
        $team = $this->Team->getById($teamId);
        $timezone = $this->Team->getTimezone();
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        // Check if payment settings was created
        $paymentSettings = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paymentSettings);
        $this->assertEquals(array_intersect_key($paymentData, $paymentSettings),
            array_intersect_key($paymentSettings, $paymentData));
        $this->assertEquals($paymentSettings['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paymentSettings['currency'], Enum\Model\PaymentSetting\Currency::JPY);
        $this->assertEquals($paymentSettings['amount_per_user'], 0);

        // Check if PaymentSettingChangeLog was created
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paymentSettings['id']);
        $this->assertEquals($payLog['plain_data'], $paymentSettings);

        // Check if invoice was created
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId(1);
        $data = array_intersect_key($invoice, $invoiceData);
        $this->assertEquals(array_intersect_key($invoiceData, $data), $data);
        $this->assertEquals($paymentSettings['id'], $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Model\Invoice\CreditStatus::WAITING, $invoice['credit_status']);

        // Check invoice history was created
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->findAllByTeamId($teamId);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all');
        $this->assertCount(1, $InvoiceHistoriesChargeHistories);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $chargeInfo = $this->CampaignService->getChargeInfo($pricePlanCode);
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paymentSettings['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paymentSettings['amount_per_user'],
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());

        // Check campaign purchase
        $isPurchased = $this->CampaignService->purchased($teamId);
        $this->assertTrue($isPurchased === true);
    }

    public function test_registerInvoicePayment_preRegisterAmount()
    {
        $teamId = 1;
        $this->Team->clear();
        $this->Team->id = $teamId;
        $this->Team->save([
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017/8/1',
            'service_use_state_end_date'   => '2017/8/15',
            'pre_register_amount_per_user' => 1200
        ], false);

        $userId = $this->createActiveUser($teamId);
        $paymentData = $invoiceData = $this->createTestPaymentDataForReg([]);

        // Register invoice
        $returningOrderId = 'AK12345678';

        // mocking credit invoice as succeed
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));
        $res = $this->PaymentService->registerInvoicePayment($userId, $teamId, $paymentData, $invoiceData);
        $this->assertTrue($res === true);

        // Check team status
        $team = $this->Team->getById($teamId);
        $timezone = $this->Team->getTimezone();
        $this->assertEquals($team['service_use_status'], Enum\Model\Team\ServiceUseStatus::PAID);
        $this->assertEquals($team['service_use_state_start_date'],
            GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d'));
        $this->assertNull($team['service_use_state_end_date']);

        // Check if payment settings was created
        $paymentSettings = $this->PaymentSetting->getUnique($teamId);
        $this->assertNotEmpty($paymentSettings);
        $this->assertEquals($paymentData, array_intersect_key($paymentSettings, $paymentData));
        $this->assertEquals($paymentSettings['payment_base_day'],
            date('d', strtotime(AppUtil::todayDateYmdLocal($timezone))));
        $this->assertEquals($paymentSettings['currency'], Enum\Model\PaymentSetting\Currency::JPY);
        $this->assertEquals($paymentSettings['amount_per_user'], 1200);

        // Check if PaymentSettingChangeLog was created
        $payLog = $this->PaymentSettingChangeLog->getLatest($teamId);
        $this->assertNotEmpty($payLog);
        $this->assertEquals($payLog['team_id'], $teamId);
        $this->assertEquals($payLog['user_id'], $userId);
        $this->assertEquals($payLog['payment_setting_id'], $paymentSettings['id']);
        $this->assertEquals($payLog['plain_data'], $paymentSettings);

        // Check if invoice was created
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId(1);
        $data = array_intersect_key($invoice, $invoiceData);
        $this->assertEquals(array_intersect_key($invoiceData, $data), $data);
        $this->assertEquals($paymentSettings['id'], $invoice['payment_setting_id']);
        $this->assertEquals(Enum\Model\Invoice\CreditStatus::WAITING, $invoice['credit_status']);

        // Check invoice history was created
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->findAllByTeamId($teamId);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);

        // Check invoice charge history was created
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $InvoiceHistoriesChargeHistories = $InvoiceHistoriesChargeHistory->find('all');
        $this->assertCount(1, $InvoiceHistoriesChargeHistories);

        // Check saved ChargeHistory data
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($history['charge_datetime'] <= time());
        $amountPerUser = 1200;
        $chargeUserCnt = $this->TeamMember->countChargeTargetUsers($teamId);
        $totalAmount = $amountPerUser * $chargeUserCnt;
        $expected = [
            'id'               => 1,
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => $paymentSettings['type'],
            'charge_type'      => enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $paymentSettings['amount_per_user'],
            'total_amount'     => $totalAmount,
            'tax'              => $this->PaymentService->calcTax($paymentSettings['company_country'], $totalAmount),
            'charge_users'     => $chargeUserCnt,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $chargeUserCnt,
        ];
        $this->assertEquals(array_intersect_key($history, $expected), $expected);
        $this->assertTrue($history['charge_datetime'] <= time());
    }

    public function test_registerInvoicePayment_emptyData()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $invoiceData = [];

        // Register invoice
        // this test case does not use http access (not calling api)
        $res = $this->PaymentService->registerInvoicePayment($userID, 1, $paymentData, $invoiceData);
        $this->assertFalse($res);
    }

    public function test_registerInvoicePayment_invalidTeam()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $invoiceData = $this->createTestPaymentData([]);

        // Register invoice
        // this test case does not use http access (not calling api)
        $res = $this->PaymentService->registerInvoicePayment($userID, 999, $paymentData, $invoiceData);
        $this->assertFalse($res === true);
    }

    public function test_updateInvoice()
    {
        $teamId = 1;
        $userID = $this->createActiveUser(1);
        $paymentData = $invoiceData = $this->createTestPaymentData([]);

        $returningOrderId = 'AK12345678';
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $returningOrderId, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));
        $this->PaymentService->registerInvoicePayment($userID, $teamId, $paymentData, $invoiceData);

        // update invoice
        $newData = $this->createTestPaymentData([
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ]);
        $res = $this->PaymentService->updateInvoice(1, $newData);
        $this->assertTrue($res === true);

        // Assert data was updated
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId(1);
        $data = array_intersect_key($invoice, $newData);
        $newData = array_intersect_key($newData, $data);
        $this->assertEquals($data, $newData);

        // check invoice history
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        $invoiceHistories = $InvoiceHistory->find('all', ['conditions' => ['team_id' => $teamId]]);
        $this->assertCount(1, $invoiceHistories);
        $this->assertEquals($returningOrderId, $invoiceHistories[0]['InvoiceHistory']['system_order_code']);
    }

    public function test_updateInvoice_missingFields()
    {
        $userID = $this->createActiveUser(1);
        $paymentData = $invoiceData = $this->createTestPaymentData([]);

        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', 'AK23553506', Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));
        $res = $this->PaymentService->registerInvoicePayment($userID, 1, $paymentData, $invoiceData);
        $this->assertTrue($res);

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
        $this->assertEquals(500, $res['errorCode']);
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
            'charge_type'        => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
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
            'charge_type'        => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
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
            'charge_type'        => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
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
            'charge_type'        => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
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
            'tax'              => 10.0,
            'total_charge'     => 110.0,
        ]);

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 1000.0,
            'tax'              => 100.0,
            'total_charge'     => 1100.0,
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
            'tax'              => 198.0,
            'total_charge'     => 2178.0,
        ]);

        $res = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, 10);
        $this->assertEquals($res, [
            'sub_total_charge' => 19800.0,
            'tax'              => 1980.0,
            'total_charge'     => 21780.0,
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
        $this->assertEquals($res, 0.10);
        $res = $this->PaymentService->getTaxRateByCountryCode('US');
        $this->assertEquals($res, 0);
        $res = $this->PaymentService->getTaxRateByCountryCode('VN');
        $this->assertEquals($res, 0);
    }

    public function test_calcTax_jp()
    {
        $companyCountry = 'JP';
        $res = $this->PaymentService->calcTax($companyCountry, 100);
        $this->assertEquals($res, 10);

        $res = $this->PaymentService->calcTax($companyCountry, 1);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->calcTax($companyCountry, 12);
        $this->assertEquals($res, 1);

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

    public function test_updatePayerInfo_cc()
    {
        list($teamId) = $this->createCcPaidTeam([], [
            'company_name'              => 'ISAO1',
            'company_post_code'         => '000000',
            'company_country'           => 'JP',
            'company_region'            => '東京都',
            'company_city'              => '杉並区',
            'company_street'            => '１−３−１１',
            'contact_person_tel'        => '09012345678',
            'contact_person_email'      => 'test1@example.com',
            'contact_person_first_name' => 'Steve',
            'contact_person_last_name'  => 'Jobs',
        ]);

        $updateData = [
            'type'                           => Enum\Model\PaymentSetting\Type::INVOICE,
            'company_name'                   => 'ISAO2',
            'company_post_code'              => '111111',
            'company_country'                => 'US',
            'company_region'                 => 'NY',
            'company_city'                   => 'Central Park',
            'company_street'                 => 'Somewhere',
            'contact_person_tel'             => '08012345678',
            'contact_person_email'           => 'test2@example.com',
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ];

        $paySetting = $this->PaymentSetting->getUnique($teamId);
        $paymentSettingsUpdateBefore = $this->PaymentSettingChangeLog->findByPaymentSettingId($paySetting['id']);

        // Update payment data
        $userId = $this->createActiveUser($teamId);
        $res = $this->PaymentService->updatePayerInfo($teamId, $userId, $updateData);
        $this->assertTrue($res);

        // Retrieve data from db
        $data = $this->PaymentSetting->getUnique($teamId);
        // Compare updated with saved data
        $this->assertNotEmpty($data);
        $this->assertEquals($data['team_id'], $teamId);
        $this->assertEquals($data['type'], Enum\Model\PaymentSetting\Type::CREDIT_CARD);
        $this->assertEquals($data['company_post_code'], '111111');
        $this->assertEquals($data['company_country'], 'JP');
        $this->assertEquals($data['company_region'], 'NY');
        $this->assertEquals($data['company_city'], 'Central Park');
        $this->assertEquals($data['company_street'], 'Somewhere');
        $this->assertEquals($data['contact_person_tel'], '08012345678');
        $this->assertEquals($data['contact_person_email'], 'test2@example.com');
        $this->assertEquals($data['contact_person_first_name'], 'Tonny');
        $this->assertEquals($data['contact_person_first_name_kana'], null);
        $this->assertEquals($data['contact_person_last_name'], 'Stark');
        $this->assertEquals($data['contact_person_last_name_kana'], null);

        $paySetting = $this->PaymentSetting->getUnique($teamId);
        $paymentSettingsUpdateAfter = $this->PaymentSettingChangeLog->findByPaymentSettingId($paySetting['id']);
        $this->assertTrue(count($paymentSettingsUpdateBefore) < count($paymentSettingsUpdateAfter));
    }

    public function test_updatePayerInfo_invoice()
    {
        list($teamId) = $this->createInvoicePaidTeam([], [
            'company_name'              => 'ISAO1',
            'company_post_code'         => '000000',
            'company_country'           => 'JP',
            'company_region'            => '東京都',
            'company_city'              => '杉並区',
            'company_street'            => '１−３−１１',
            'contact_person_tel'        => '09012345678',
            'contact_person_email'      => 'test1@example.com',
            'contact_person_first_name' => 'Steve',
            'contact_person_last_name'  => 'Jobs',
        ]);

        $updateData = [
            'team_id'                        => $teamId + 1, // no existing team
            'type'                           => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'company_name'                   => 'ISAO2',
            'company_post_code'              => '111111',
            'company_country'                => 'US',
            'company_region'                 => 'NY',
            'company_city'                   => 'Central Park',
            'company_street'                 => 'Somewhere',
            'contact_person_tel'             => '08012345678',
            'contact_person_email'           => 'test2@example.com',
            'contact_person_first_name'      => 'Tonny',
            'contact_person_first_name_kana' => 'トニー',
            'contact_person_last_name'       => 'Stark',
            'contact_person_last_name_kana'  => 'スターク',
        ];

        // Update payment data
        $userId = $this->createActiveUser($teamId);
        $res = $this->PaymentService->updatePayerInfo($teamId, $userId, $updateData);
        $this->assertTrue($res);

        // Retrieve data from db
        $data = $this->PaymentSetting->getUnique($teamId);
        // Compare updated with saved data
        $this->assertNotEmpty($data);
        $this->assertEquals($data['team_id'], $teamId);
        $this->assertEquals($data['type'], Enum\Model\PaymentSetting\Type::INVOICE);
        $this->assertEquals($data['company_post_code'], '111111');
        $this->assertEquals($data['company_country'], 'JP');
        $this->assertEquals($data['company_region'], 'NY');
        $this->assertEquals($data['company_city'], 'Central Park');
        $this->assertEquals($data['company_street'], 'Somewhere');
        $this->assertEquals($data['contact_person_tel'], '08012345678');
        $this->assertEquals($data['contact_person_email'], 'test2@example.com');
        $this->assertEquals($data['contact_person_first_name'], 'Tonny');
        $this->assertEquals($data['contact_person_first_name_kana'], 'トニー');
        $this->assertEquals($data['contact_person_last_name'], 'Stark');
        $this->assertEquals($data['contact_person_last_name_kana'], 'スターク');

    }

    public function test_updatePayerInfo_missingFields()
    {
        // This case is enable if validation when saving is enable
        // But Enabling validation when saving is not best way.
        // Because validation is different by case.
        // So this comment outed temporarily.
//        list($teamId) = $this->createCcPaidTeam();
//        $updateData = [
//            'company_name'                   => 'ISAO',
//            'company_post_code'              => '',
//            'company_country'                => '',
//            'company_region'                 => '',
//            'company_city'                   => '',
//            'company_street'                 => '',
//            'company_tel'                    => '',
//            'contact_person_tel'             => '123456789',
//            'contact_person_email'           => 'test@example.com',
//            'contact_person_first_name'      => 'Tonny',
//            'contact_person_first_name_kana' => 'トニー',
//            'contact_person_last_name'       => 'Stark',
//            'contact_person_last_name_kana'  => 'スターク',
//        ];
//
//        // Update payment data
//        $userId = $this->createActiveUser($teamId);
//        $res = $this->PaymentService->updatePayerInfo($teamId, $userId, $updateData);
//
//        $this->assertNotNull($res);
//        $this->assertArrayHasKey("errorCode", $res);
//        $this->assertArrayHasKey("message", $res);
//        $this->assertEquals(500, $res['errorCode']);
    }

    function test_findMonthlyChargeInvoiceTeams()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 1];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
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
            'order_datetime'    => $time,
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
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
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
        $this->assertCount(3, $res);

        $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_datetime'    => AppUtil::getEndTimestampByTimezone('2016-12-01', 9),
                'system_order_code' => "test",
            ],
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
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
        $this->assertCount(3, $res);

    }

    function test_registerInvoice()
    {
        $this->Team->deleteAll(['del_flg' => false]);

        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
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

        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', 'AK23553506', Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

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
            'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
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

    public function test_getChargeMaxUserCnt_noChargeHistory()
    {
        $teamId = 1;
        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
            0);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
            3);
        $this->assertEquals($res, 3);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
            3);
        $this->assertEquals($res, 3);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
            3);
        $this->assertEquals($res, 3);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::RECHARGE(), 3);
        $this->assertEquals($res, 3);

    }

    public function test_getChargeMaxUserCnt_existChargeHistory()
    {
        $teamId = 1;
        $data = [
            'team_id'          => $teamId,
            'charge_datetime'  => strtotime('2017-08-01'),
            'max_charge_users' => 10.
        ];
        $this->ChargeHistory->save($data, false);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
            0);
        $this->assertEquals($res, 0);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE(),
            3);
        $this->assertEquals($res, 3);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
            3);
        $this->assertEquals($res, 13);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
            3);
        $this->assertEquals($res, 13);

        $data = [
            'team_id'          => $teamId,
            'charge_datetime'  => strtotime('2017-08-02'),
            'max_charge_users' => 5.
        ];
        $this->ChargeHistory->save($data, false);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
            3);
        $this->assertEquals($res, 8);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId,
            Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
            20);
        $this->assertEquals($res, 25);

        $res = $this->PaymentService->getChargeMaxUserCnt($teamId, Enum\Model\ChargeHistory\ChargeType::RECHARGE(), 3);
        $this->assertEquals($res, 3);

    }

    public function test_getPaymentType_creditCard()
    {
        list($teamId) = $this->createCcPaidTeam();

        $res = $this->PaymentService->getPaymentType($teamId);
        $this->assertEquals(Enum\Model\PaymentSetting\Type::CREDIT_CARD, $res);
    }

    public function test_getPaymentType_invoice()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        $res = $this->PaymentService->getPaymentType(1);
        $this->assertEquals(Enum\Model\PaymentSetting\Type::INVOICE, $res);
    }

    public function test_getPaymentType_noPayment()
    {
        $res = $this->PaymentService->getPaymentType(1);
        $this->assertNull($res);
    }

    public function test_getPaymentType_creditCard_invalidTeamId()
    {
        $res = $this->PaymentService->getPaymentType(999999);
        $this->assertNull($res);
    }

    public function test_calcChargeUserCount_basic()
    {
        $teamId = $this->createTeam();
        $this->createActiveUser($teamId);

        /*
         * set team payment setting
         */
        $createData = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($createData, false);

        $res = $this->PaymentService->calcChargeUserCount($teamId, 1);
        $this->assertEquals($res, 1);

        $this->ChargeHistory->clear();
        $this->ChargeHistory->save([
            'team_id'          => $teamId,
            'max_charge_users' => 2
        ], false);
        $res = $this->PaymentService->calcChargeUserCount($teamId, 1);
        $this->assertEquals($res, 0);

        $this->createActiveUser($teamId);
        $res = $this->PaymentService->calcChargeUserCount($teamId, 1);
        $this->assertEquals($res, 1);

        $this->ChargeHistory->clear();
        $this->ChargeHistory->save([
            'team_id'          => $teamId,
            'max_charge_users' => 5
        ], false);
        $res = $this->PaymentService->calcChargeUserCount($teamId, 10);
        $this->assertEquals($res, 7);
    }

    public function test_formatCharge()
    {
        // JPY
        $currency = Enum\Model\PaymentSetting\Currency::JPY;
        $res = $this->PaymentService->formatCharge(0.00, $currency);
        $this->assertEquals($res, '¥0');
        $res = $this->PaymentService->formatCharge(100, $currency);
        $this->assertEquals($res, '¥100');
        $res = $this->PaymentService->formatCharge(1980, $currency);
        $this->assertEquals($res, '¥1,980');
        $res = $this->PaymentService->formatCharge(1234567890.0, $currency);
        $this->assertEquals($res, '¥1,234,567,890');

        // USD
        $currency = Enum\Model\PaymentSetting\Currency::USD;
        $res = $this->PaymentService->formatCharge(100.12, $currency);
        $this->assertEquals($res, '$100.12');
        $res = $this->PaymentService->formatCharge(0.1, $currency);
        $this->assertEquals($res, '$0.10');
        $res = $this->PaymentService->formatCharge(1234567890, $currency);
        $this->assertEquals($res, '$1,234,567,890.00');
    }

    public function test_formatTotalChargeByAddUsers_jp()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 1]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        GoalousDateTime::setTestNow('2017-01-31 14:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥42');

        GoalousDateTime::setTestNow('2017-01-31 14:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 2,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥86');

        GoalousDateTime::setTestNow('2017-01-31 15:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥1,347');

        GoalousDateTime::setTestNow('2017-01-31 15:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 3,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥4,042');

        $this->Team->saveField('timezone', 0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-02-28 23:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥47');

        GoalousDateTime::setTestNow('2017-03-01 00:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥1,347');

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-04-01 03:29:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥42');

        GoalousDateTime::setTestNow('2017-04-01 03:30:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥1,347');

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-09-01 11:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥42');

        GoalousDateTime::setTestNow('2017-09-01 12:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::JPY());
        $this->assertEquals($res, '¥1,347');

    }

    public function test_formatTotalChargeByAddUsers_foreign()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
            'company_country'  => 'US',
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_USD,
            'currency'         => Enum\Model\PaymentSetting\Currency::USD
        ]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        $formattedAmountPerUser = $this->PaymentService->formatCharge(PaymentService::AMOUNT_PER_USER_USD,
            Enum\Model\PaymentSetting\Currency::USD);

        GoalousDateTime::setTestNow('2017-01-30 14:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$0.38');

        GoalousDateTime::setTestNow('2017-01-30 14:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 2,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$0.77');

        GoalousDateTime::setTestNow('2017-01-30 15:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, $formattedAmountPerUser);

        GoalousDateTime::setTestNow('2017-01-30 15:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 3,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$36.00');

        $this->Team->saveField('timezone', 0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-02-27 23:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$0.42');

        GoalousDateTime::setTestNow('2017-02-28 00:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, $formattedAmountPerUser);

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-03-31 03:29:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$0.38');

        GoalousDateTime::setTestNow('2017-03-31 03:30:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, $formattedAmountPerUser);

        GoalousDateTime::setTestNow('2017-04-01 03:30:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$11.60');

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-08-31 11:59:59');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$0.38');

        GoalousDateTime::setTestNow('2017-08-31 12:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 1,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, $formattedAmountPerUser);

        GoalousDateTime::setTestNow('2017-09-10 12:00:00');
        $res = $this->PaymentService->formatTotalChargeByAddUsers($teamId, 12,
            Enum\Model\PaymentSetting\Currency::USD());
        $this->assertEquals($res, '$96.00');

    }

    public function test_getCurrentAllUseDays_basic()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 1]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        GoalousDateTime::setTestNow('2017-01-31 14:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-01-31 15:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 28);
        GoalousDateTime::setTestNow('2017-02-28 14:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 28);
        GoalousDateTime::setTestNow('2017-02-28 15:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', 0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-03-31 23:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-04-01 00:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 30);

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-05-01 03:29:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 30);
        GoalousDateTime::setTestNow('2017-05-01 03:30:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-06-01 11:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-06-01 12:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 30);
    }

    public function test_getCurrentAllUseDays_baseLastDay()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        GoalousDateTime::setTestNow('2017-01-30 14:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow('2017-01-30 15:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 28);

        GoalousDateTime::setTestNow('2017-02-27 14:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 28);

        GoalousDateTime::setTestNow('2017-02-27 15:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', 0);
        $this->Team->resetCurrentTeam();

        GoalousDateTime::setTestNow('2017-03-30 23:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow('2017-03-31 00:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 30);

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();

        GoalousDateTime::setTestNow('2017-11-30 03:29:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 30);

        GoalousDateTime::setTestNow('2017-11-30 03:30:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();

        GoalousDateTime::setTestNow('2017-12-31 11:59:59');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);

        GoalousDateTime::setTestNow('2017-12-31 12:00:00');
        $res = $this->PaymentService->getCurrentAllUseDays($teamId);
        $this->assertEquals($res, 31);
    }

    public function test_getUseDaysByNextBaseDate_basic()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 1]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        GoalousDateTime::setTestNow('2016-12-31 14:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2016-12-31 15:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-01-31 14:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-01-31 15:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 28);

        $this->Team->saveField('timezone', 0.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-02-28 23:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-03-01 00:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-03-31 23:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-04-01 00:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 30);

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-05-01 02:29:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-05-01 03:30:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-06-01 03:29:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-06-01 03:30:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 30);

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-08-01 11:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-08-01 12:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);
        GoalousDateTime::setTestNow('2017-09-01 11:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-09-01 12:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 30);

    }

    public function test_getUseDaysByNextBaseDate_baseLastDay()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->id = $teamId;
        $this->Team->saveField('timezone', 9.0);
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        GoalousDateTime::setTestNow('2017-01-30 14:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-01-30 15:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 28);
        GoalousDateTime::setTestNow('2017-02-27 14:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-02-27 15:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', 0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-03-30 23:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-03-31 00:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 30);

        $this->Team->saveField('timezone', -3.5);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-11-30 03:29:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-11-30 03:30:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);

        $this->Team->saveField('timezone', -12.0);
        $this->Team->resetCurrentTeam();
        GoalousDateTime::setTestNow('2017-12-31 11:59:59');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 1);
        GoalousDateTime::setTestNow('2017-12-31 12:00:00');
        $res = $this->PaymentService->getUseDaysByNextBaseDate($teamId);
        $this->assertEquals($res, 31);
    }

    public function test_getPreviousBaseDate()
    {
        $this->Team->resetCurrentTeam();
        $this->PaymentService->clearCachePaymentSettings();

        $teamId = 1;
        $data = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 1]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($data, false);
        $paySettingId = $this->PaymentSetting->getLastInsertID();

        // payment_base_day:1
        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2017-10-01');
        $this->assertEquals($res, '2017-09-01');

        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2017-12-01');
        $this->assertEquals($res, '2017-11-01');

        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2018-01-01');
        $this->assertEquals($res, '2017-12-01');

        // payment_base_day:31
        $this->PaymentSetting->saveField('payment_base_day', 31);
        $this->PaymentService->clearCachePaymentSettings();

        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2017-12-31');
        $this->assertEquals($res, '2017-11-30');

        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2018-01-31');
        $this->assertEquals($res, '2017-12-31');

        $res = $this->PaymentService->getPreviousBaseDate($teamId, '2018-02-28');
        $this->assertEquals($res, '2018-01-31');

    }

    public function test_getAmountPerUser()
    {
        list($teamId) = $this->createCcPaidTeam([],
            ['company_country' => 'US', 'amount_per_user' => PaymentService::AMOUNT_PER_USER_USD], []);
        $res = $this->PaymentService->getAmountPerUser($teamId);
        $this->assertEquals(PaymentService::AMOUNT_PER_USER_USD, $res);

        list($teamId) = $this->createCcPaidTeam([],
            ['company_country' => 'JP', 'amount_per_user' => PaymentService::AMOUNT_PER_USER_JPY], []);
        $res = $this->PaymentService->getAmountPerUser($teamId);
        $this->assertEquals(PaymentService::AMOUNT_PER_USER_JPY, $res);

        // Assert value based on user lang settings
        $Lang = new LangHelper(new View());
        $userCountryCode = $Lang->getUserCountryCode();
        $res = $this->PaymentService->getAmountPerUser(null);
        if ($userCountryCode == 'JP') {
            $this->assertEquals(PaymentService::AMOUNT_PER_USER_JPY, $res);
        } else {
            $this->assertEquals(PaymentService::AMOUNT_PER_USER_USD, $res);
        }
    }

    public function test_isChargeUserActivation()
    {
        // Setup team and paid plan
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        list ($teamId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        // No history expect true
        $res = $this->PaymentService->isChargeUserActivation($teamId);
        $this->assertTrue($res === true);

        // Create payment history
        $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_datetime'    => AppUtil::getEndTimestampByTimezone('2016-12-01', 9),
                'system_order_code' => "test",
            ],
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                'charge_type'      => ChargeHistory::CHARGE_TYPE_ACTIVATE_USER,
                'amount_per_user'  => 1980,
                'total_amount'     => 3960,
                'tax'              => 310,
                'charge_users'     => 2,
                'max_charge_users' => 2,
                'charge_datetime'  => AppUtil::getEndTimestampByTimezone('2016-12-01', 9)
            ]
        );

        // Already paid, expect false
        $res = $this->PaymentService->isChargeUserActivation($teamId);
        $this->assertFalse($res === true);
    }

    public function test_getAmountPerUserBeforePayment()
    {
        $teamAId = $this->createTeam(['pre_register_amount_per_user' => 1000]);
        $res = $this->PaymentService->getAmountPerUserBeforePayment($teamAId, 'JP');
        $this->assertEquals($res, 1000);

        // JPY
        $teamBId = $this->createTeam();
        $res = $this->PaymentService->getAmountPerUserBeforePayment($teamBId, 'JP');
        $this->assertEquals(PaymentService::AMOUNT_PER_USER_JPY, $res);

        // USD
        $teamCId = $this->createTeam();
        $res = $this->PaymentService->getAmountPerUserBeforePayment($teamCId, 'US');
        $this->assertEquals(PaymentService::AMOUNT_PER_USER_USD, $res);
    }

    function test_calcRelatedTotalChargeForUpgradingPlan_exception()
    {
        // Prepare data for testing
        $teamId = 9999999;
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY();

        // upgradePlanCode is empty
        $upgradePlanCode = '';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        // currentPlanCode is empty
        $upgradePlanCode = '1-1';
        $currentPlanCode = '';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        // upgradePlanCode doesn't exist
        $upgradePlanCode = '1000-1000';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        // currentPlanCode doesn't exist
        $upgradePlanCode = '1-1';
        $currentPlanCode = '1000-1000';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        // currentPlanCode and upgradePlanCode are same
        $upgradePlanCode = '1-1';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

        // Payment setting doesn't exist
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], 0);

    }

    function test_calcRelatedTotalChargeForUpgradingPlan_jp()
    {
        GoalousDateTime::setTestNow("2017-11-01");
        // Prepare data for testing
        $companyCountry = 'JP';
        $paymentSetting = ['payment_base_day' => 1, 'company_country' => $companyCountry];
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], $paymentSetting);
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY();

        // Case: Upgraded plan date is as same as payment base day
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 50000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '1-5';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 200000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '1-5';
        $currentPlanCode = '1-4';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 50000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Upgraded plan date is one day after payment base day
        GoalousDateTime::setTestNow("2017-11-02");
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 48333.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '1-5';
        $currentPlanCode = '1-2';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 145000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Upgraded plan date is one day before payment base day
        GoalousDateTime::setTestNow("2017-11-30");
        $upgradePlanCode = '1-4';
        $currentPlanCode = '1-3';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 1666.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '1-3';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 3333.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Month days are short (February)
        GoalousDateTime::setTestNow("2018-02-02");

        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 48214.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-02-28");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 1785.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Payment base day are end day of month
        $this->PaymentSetting->clear();
        $this->PaymentSetting->id = $paymentSettingId;
        $this->PaymentSetting->save([
            'payment_base_day' => 31
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2018-02-28");
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 50000.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2017-11-30");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 50000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2017-12-31");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 50000);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-01-01");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 48387.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-03-01");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 48387.0);
        $this->assertEquals($res['tax'], $this->PaymentService->calcTax($companyCountry, $res['sub_total_charge']));
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

    }

    function test_calcRelatedTotalChargeForUpgradingPlan_us()
    {
        GoalousDateTime::setTestNow("2017-11-01");
        // Prepare data for testing
        $companyCountry = 'US';
        $paymentSetting = ['payment_base_day' => 1, 'company_country' => $companyCountry];
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam(['timezone' => 0], $paymentSetting);
        $currencyType = Enum\Model\PaymentSetting\Currency::USD();
        $this->PaymentService->clearCachePaymentSettings();

        // Case: Upgraded plan date is as same as payment base day
        $upgradePlanCode = '2-2';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '2-3';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 1000.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '2-4';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 1500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        $upgradePlanCode = '2-5';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 2000.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-01-01");
        $upgradePlanCode = '2-3';
        $currentPlanCode = '2-2';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Upgraded plan date is one day after payment base day
        GoalousDateTime::setTestNow("2018-01-02");
        $upgradePlanCode = '2-4';
        $currentPlanCode = '2-3';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 483.87);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-02-02");
        $upgradePlanCode = '2-4';
        $currentPlanCode = '2-3';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 482.14);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Upgraded plan date is one day before payment base day
        GoalousDateTime::setTestNow("2017-12-31");
        $upgradePlanCode = '2-4';
        $currentPlanCode = '2-3';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 16.12);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-02-28");
        $upgradePlanCode = '2-4';
        $currentPlanCode = '2-3';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 17.85);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        // Case: Payment base day are end day of month
        $this->PaymentSetting->clear();
        $this->PaymentSetting->id = $paymentSettingId;
        $this->PaymentSetting->save([
            'payment_base_day' => 31
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2018-02-28");
        $upgradePlanCode = '2-2';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-04-30");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-12-31");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 500.0);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-03-01");
        $upgradePlanCode = '2-2';
        $currentPlanCode = '2-1';
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 483.87);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-04-01");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 483.33);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

        GoalousDateTime::setTestNow("2018-03-30");
        $res = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, $currencyType, $upgradePlanCode, $currentPlanCode
        );
        $this->assertEquals($res['sub_total_charge'], 16.12);
        $this->assertEquals($res['tax'], 0);
        $this->assertEquals($res['total_charge'], $res['sub_total_charge'] + $res['tax']);

    }

    function test_chargeForUpgradingCampaignPlan_jpCc()
    {
        $opeUserId = 1;
        $companyCountry = 'JP';
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY();
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $team = ['timezone' => 9];
        $paymentSetting = [
            'payment_base_day' => 15,
            'company_country'  => $companyCountry
        ];

        GoalousDateTime::setTestNow('2017-11-14 15:00:00');
        list($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam(1, $currentPlanCode, $team,
            $paymentSetting);
        $this->PaymentService->clearCachePaymentSettings();

        // Case: Upgraded plan date is as same as payment base day
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,
        ];
        $subTotalCharge = 50000;
        $expected = am($baseExpected, [
            'id'           => 1,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');

        // Case: Upgraded plan date is one day after payment base day
        GoalousDateTime::setTestNow('2017-11-16 14:59:59');
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $subTotalCharge = 48333;
        $expected = am($baseExpected, [
            'id'           => 2,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');

        // Case: Upgraded plan date is one day before payment base day
        GoalousDateTime::setTestNow('2017-04-14 14:59:59');
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $subTotalCharge = 1612;
        $expected = am($baseExpected, [
            'id'           => 3,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, $res['total_amount'] + $res['tax']);
        $this->assertEquals($chargeRes->currency, 'jpy');
    }

    function test_chargeForUpgradingCampaignPlan_foreignCc()
    {
        $opeUserId = 1;
        $companyCountry = 'TH';
        $currencyType = Enum\Model\PaymentSetting\Currency::USD();
        $upgradePlanCode = '2-5';
        $currentPlanCode = '2-1';
        $team = ['timezone' => -12];
        $paymentSetting = [
            'payment_base_day' => 31,
            'company_country'  => $companyCountry,
            'currency'         => $currencyType
        ];

        GoalousDateTime::setTestNow('2017-12-01 11:59:00');
        list($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam(2, $currentPlanCode, $team,
            $paymentSetting);
        $this->PaymentService->clearCachePaymentSettings();
        $this->Team->current_team_id = $teamId;
        // Case: Upgraded plan date is as same as payment base day
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => Enum\Model\PaymentSetting\Currency::USD,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,
        ];
        // Stripe specification
        // Ref: https://stripe.com/docs/currencies#zero-decimal
        $subTotalCharge = 2000;
        $expected = am($baseExpected, [
            'id'           => 1,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, (string)($subTotalCharge * 100));
        $this->assertEquals($chargeRes->currency, 'usd');

        // Case: Upgraded plan date is one day after payment base day
        GoalousDateTime::setTestNow('2017-12-02 11:59:00');
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $subTotalCharge = 1935.48;
        $expected = am($baseExpected, [
            'id'           => 2,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, (string)($subTotalCharge * 100));
        $this->assertEquals($chargeRes->currency, 'usd');

        // Case: Upgraded plan date is one day before payment base day
        GoalousDateTime::setTestNow('2017-02-28 11:59:00');
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $chargeRes = \Stripe\Charge::retrieve($res['stripe_payment_code']);
        $this->assertTrue($res['charge_datetime'] <= time());
        $this->assertNotEmpty($res['stripe_payment_code']);
        $subTotalCharge = 71.42;
        $expected = am($baseExpected, [
            'id'           => 3,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
        $this->assertEquals($chargeRes->amount, (string)($subTotalCharge * 100));
        $this->assertEquals($chargeRes->currency, 'usd');
    }

    function test_chargeForUpgradingCampaignPlan_invoice()
    {
        $opeUserId = 1;
        $companyCountry = 'JP';
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY;
        $upgradePlanCode = '1-2';
        $currentPlanCode = '1-1';
        $paymentSetting = [
            'payment_base_day' => 3,
        ];

        GoalousDateTime::setTestNow('2022-03-03');
        list($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createInvoiceCampaignTeam(1, $currentPlanCode, [],
            $paymentSetting);
        $this->PaymentService->clearCachePaymentSettings();
        $this->Team->current_team_id = $teamId;

        // Case: Upgraded plan date is as same as payment base day
        $this->PaymentService->chargeForUpgradingCampaignPlan(
            $teamId, $currentPlanCode, $upgradePlanCode, $opeUserId
        );

        $res = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId, 'id');
        $this->assertTrue($res['charge_datetime'] <= time());
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => Enum\Model\PaymentSetting\Currency::JPY,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,
        ];
        $subTotalCharge = 50000;
        $expected = am($baseExpected, [
            'id'           => 1,
            'total_amount' => $subTotalCharge,
            'tax'          => $this->PaymentService->calcTax($companyCountry, $subTotalCharge),
        ]);

        $res = array_intersect_key($res, $expected);
        $this->assertEquals($res, $expected);
    }

    function test_reorderInvoice_fail()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        // empty target charge histories
        $res = $this->PaymentService->reorderInvoice($teamId, 1);
        $this->assertEquals($res, false);

        // target invoice history exist, but order_status is WAITING(not NG).

        $chargeTs = GoalousDateTime::now()->getTimestamp();
        $saveHistory = [
            'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => 1980,
            'total_amount'     => 3960,
            'tax'              => 310,
            'charge_users'     => 2,
            'max_charge_users' => 2,
            'charge_datetime'  => $chargeTs
        ];
        list($chargeHistoryId, $invoiceHistoryId) = $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_datetime'    => $chargeTs,
                'system_order_code' => "test1",
            ],
            $saveHistory
        );
        $res = $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $this->assertEquals($res, false);

        // target invoice history exist, but order_status is OK(not NG).
        $this->InvoiceHistory->clear();
        $this->InvoiceHistory->id = $invoiceHistoryId;
        $this->InvoiceHistory->save([
            'order_status' => Enum\Model\Invoice\CreditStatus::OK
        ]);

        $orderCode = "AK23553506";
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $orderCode, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        $res = $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $this->assertEquals($res, false);
    }

    function test_reorderInvoice_normalPricing_singleChargeHistory()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        // target invoice history exist, but order_status is WAITING(not NG).

        $chargeTs = GoalousDateTime::now()->getTimestamp();
        $saveHistory = [
            'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => 1980,
            'total_amount'     => 3960,
            'tax'              => 310,
            'charge_users'     => 2,
            'max_charge_users' => 2,
            'charge_datetime'  => strtotime('2018-02-28')
        ];
        list($chargeHistoryId, $invoiceHistoryId) = $this->addInvoiceHistoryAndChargeHistory($teamId,
            [
                'order_datetime'    => $chargeTs,
                'system_order_code' => "test1",
                'order_status'      => Enum\Model\Invoice\CreditStatus::NG
            ],
            $saveHistory
        );

        $orderCode = "AK23553506";
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $orderCode, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        // Success
        $res = $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $this->assertEquals($res, true);
        $newInvoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $newInvoiceHistory = $this->InvoiceHistory->getById($newInvoiceHistoryId);
        $this->assertEquals($newInvoiceHistory['team_id'], $teamId);
        $this->assertTrue($newInvoiceHistory['order_datetime'] <= $chargeTs);
        $this->assertEquals($newInvoiceHistory['system_order_code'], $orderCode);
        $this->assertEquals($newInvoiceHistory['reorder_target_code'], "test1");

        $newChargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $newChargeHistory = $this->ChargeHistory->getById($newChargeHistoryId);
        $this->assertEquals($newChargeHistory['team_id'], $teamId);
        $this->assertEquals($newChargeHistory['charge_datetime'], $newInvoiceHistory['order_datetime']);
        $this->assertEquals($newChargeHistory['charge_type'], Enum\Model\ChargeHistory\ChargeType::RECHARGE);
        $this->assertEquals($newChargeHistory['amount_per_user'], 1225);
        $this->assertEquals($newChargeHistory['total_amount'], 3960);
        $this->assertEquals($newChargeHistory['tax'], 310);
        $this->assertEquals($newChargeHistory['charge_users'], 0);
        $this->assertEquals($newChargeHistory['max_charge_users'], 0);
        $this->assertEquals($newChargeHistory['campaign_team_id'], null);
        $this->assertEquals($newChargeHistory['price_plan_purchase_team_id'], null);

        $matchingHistory = $this->InvoiceHistoriesChargeHistory->find('first', [
            'invoice_history_id' => $newInvoiceHistoryId,
            'charge_history_id'  => $newChargeHistoryId,
        ]);
        $this->assertNotEmpty($matchingHistory);
    }

    function test_reorderInvoice_normalPricing_multipleChargeHistories()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice);

        // target invoice history exist, but order_status is WAITING(not NG).

        $saveHistories = [
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                'charge_type'      => Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
                'amount_per_user'  => 1980,
                'total_amount'     => 200,
                'tax'              => 16,
                'charge_users'     => 1,
                'max_charge_users' => 2,
                'charge_datetime'  => strtotime('2018-02-27')
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
            ],
        ];
        $chargeTs = GoalousDateTime::now()->getTimestamp();
        list($chargeHistoryIds, $invoiceHistoryId) = $this->addInvoiceHistoryAndChargeHistories($teamId,
            [
                'order_datetime'    => $chargeTs,
                'system_order_code' => "test1",
                'order_status'      => Enum\Model\Invoice\CreditStatus::NG
            ],
            $saveHistories
        );

        $orderCode = "AK23553506";
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $orderCode, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        // Success
        $res = $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $this->assertEquals($res, true);
        $newInvoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $newInvoiceHistory = $this->InvoiceHistory->getById($newInvoiceHistoryId);
        $this->assertEquals($newInvoiceHistory['team_id'], $teamId);
        $this->assertTrue($newInvoiceHistory['order_datetime'] <= $chargeTs);
        $this->assertEquals($newInvoiceHistory['system_order_code'], $orderCode);
        $this->assertEquals($newInvoiceHistory['reorder_target_code'], "test1");

        $newChargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $newChargeHistory = $this->ChargeHistory->getById($newChargeHistoryId);
        $this->assertEquals($newChargeHistory['team_id'], $teamId);
        $this->assertEquals($newChargeHistory['charge_datetime'], $newInvoiceHistory['order_datetime']);
        $this->assertEquals($newChargeHistory['charge_type'], Enum\Model\ChargeHistory\ChargeType::RECHARGE);
        $this->assertEquals($newChargeHistory['amount_per_user'], 1225);
        $subTotal = 0;
        $tax = 0;
        foreach ($saveHistories as $v) {
            $subTotal += $v['total_amount'];
            $tax += $v['tax'];
        }
        $this->assertEquals($newChargeHistory['total_amount'], $subTotal);
        $this->assertEquals($newChargeHistory['tax'], $tax);
        $this->assertEquals($newChargeHistory['charge_users'], 0);
        $this->assertEquals($newChargeHistory['max_charge_users'], 0);
        $this->assertEquals($newChargeHistory['campaign_team_id'], null);
        $this->assertEquals($newChargeHistory['price_plan_purchase_team_id'], null);

        $matchingHistories = $this->InvoiceHistoriesChargeHistory->find('all', [
            'invoice_history_id' => $newInvoiceHistoryId,
        ]);
        $this->assertNotEmpty($matchingHistories);
    }

    function test_reorderInvoice_campaign_multipleChargeHistories()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 31];
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createInvoiceCampaignTeam(
            $pricePlanGroupId = 1,
            $pricePlanCode = '1-1',
            $team
        );

        // target invoice history exist, but order_status is WAITING(not NG).
        $saveHistories = [
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                'charge_type'      => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
                'amount_per_user'  => 0,
                'total_amount'     => 200,
                'tax'              => 16,
                'charge_users'     => 1,
                'max_charge_users' => 2,
                'charge_datetime'  => strtotime('2018-02-27')
            ],
            [
                'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                'amount_per_user'  => 0,
                'total_amount'     => 3960,
                'tax'              => 310,
                'charge_users'     => 2,
                'max_charge_users' => 2,
                'charge_datetime'  => strtotime('2018-02-28')
            ],
        ];
        $chargeTs = GoalousDateTime::now()->getTimestamp();
        list($chargeHistoryIds, $invoiceHistoryId) = $this->addInvoiceHistoryAndChargeHistories($teamId,
            [
                'order_datetime'    => $chargeTs,
                'system_order_code' => "test1",
                'order_status'      => Enum\Model\Invoice\CreditStatus::NG
            ],
            $saveHistories
        );

        $orderCode = "AK23553506";
        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $this->createXmlAtobaraiOrderSucceedResponse('', $orderCode, Enum\AtobaraiCom\Credit::OK()),
        ]));
        $this->registerGuzzleHttpClient(new \GuzzleHttp\Client(['handler' => $handler]));

        // Success
        $res = $this->PaymentService->reorderInvoice($teamId, $invoiceHistoryId);
        $this->assertEquals($res, true);
        $newInvoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $newInvoiceHistory = $this->InvoiceHistory->getById($newInvoiceHistoryId);
        $this->assertEquals($newInvoiceHistory['team_id'], $teamId);
        $this->assertTrue($newInvoiceHistory['order_datetime'] <= $chargeTs);
        $this->assertEquals($newInvoiceHistory['system_order_code'], $orderCode);
        $this->assertEquals($newInvoiceHistory['reorder_target_code'], "test1");

        $newChargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $newChargeHistory = $this->ChargeHistory->getById($newChargeHistoryId);
        $this->assertEquals($newChargeHistory['team_id'], $teamId);
        $this->assertEquals($newChargeHistory['charge_datetime'], $newInvoiceHistory['order_datetime']);
        $this->assertEquals($newChargeHistory['charge_type'], Enum\Model\ChargeHistory\ChargeType::RECHARGE);
        $this->assertEquals($newChargeHistory['amount_per_user'], 0);
        $subTotal = 0;
        $tax = 0;
        foreach ($saveHistories as $v) {
            $subTotal += $v['total_amount'];
            $tax += $v['tax'];
        }
        $this->assertEquals($newChargeHistory['total_amount'], $subTotal);
        $this->assertEquals($newChargeHistory['tax'], $tax);

        $this->assertEquals($newChargeHistory['charge_users'], 0);
        $this->assertEquals($newChargeHistory['max_charge_users'], 0);
        $this->assertEquals($newChargeHistory['campaign_team_id'], $campaignTeamId);
        $this->assertEquals($newChargeHistory['price_plan_purchase_team_id'], $pricePlanPurchaseId);

        $matchingHistories = $this->InvoiceHistoriesChargeHistory->find('all', [
            'invoice_history_id' => $newInvoiceHistoryId,
        ]);
        $this->assertNotEmpty($matchingHistories);
    }

    function test_reorderCreditCardCharge()
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
        $reorderedChargeHistory = $this->ChargeHistory->getById($lastInsertedChargeHistoryId);

        $this->assertEquals(2000, $reorderedChargeHistory['total_amount']);
        $this->assertEquals(20, $reorderedChargeHistory['tax']);
        $this->assertEquals(Enum\Model\ChargeHistory\ChargeType::RECHARGE, $reorderedChargeHistory['charge_type']);
        $this->assertEquals($historyId, $reorderedChargeHistory['reorder_charge_history_id']);
    }

    public function test_paymentSkipped_success()
    {
        $team = ['timezone' => 9];
        $paymentSetting = ['payment_base_day' => 1];
        $invoice = ['credit_status' => Enum\Model\Invoice\CreditStatus::OK];
        $invoiceTeamId = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice)[0];
        $skippedInvoiceTeamId = $this->createInvoicePaidTeam($team, $paymentSetting, $invoice, 1, true)[0];

        /** @var $PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        // time is same as base date
        $time = strtotime('2017-01-01') - (9 * HOUR);
        $res = $PaymentService->findMonthlyChargeInvoiceTeams($time);
        $this->assertCount(1, $res);
        $this->assertContains($invoiceTeamId, Hash::extract($res, '{n}.PaymentSetting.team_id'));
        $this->assertNotContains($skippedInvoiceTeamId, Hash::extract($res, '{n}.PaymentSetting.team_id'));

        $ccTeamId = $this->createCcPaidTeam($team, $paymentSetting, $invoice)[0];
        $skippedCcTeamId = $this->createCcPaidTeam($team, $paymentSetting, $invoice, 1, true)[0];
        /** @var $PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        // time is same as base date
        $time = strtotime('2017-01-01') - (9 * HOUR);
        $res1 = $PaymentService->findMonthlyChargeCcTeams($time);
        $this->assertCount(1, $res1);
        $this->assertContains($ccTeamId, Hash::extract($res1, '{n}.PaymentSetting.team_id'));
        $this->assertNotContains($skippedCcTeamId, Hash::extract($res1, '{n}.PaymentSetting.team_id'));
    }

    public function test_getCurrentMonthBaseDate_success()
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

        GoalousDateTime::setTestNow("2017-02-27");
        $res = $this->PaymentService->getCurrentMonthBaseDate($teamId, GoalousDateTime::now()->getTimestamp());
        $this->assertEquals('2017-02-28', $res->format("Y-m-d"));

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getCurrentMonthBaseDate($teamId, GoalousDateTime::now()->getTimestamp());
        $this->assertEquals('2017-02-28', $res->format("Y-m-d"));

        // No exist day
        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 29,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-02-28");
        $res = $this->PaymentService->getCurrentMonthBaseDate($teamId, GoalousDateTime::now()->getTimestamp());
        $this->assertEquals('2017-02-28', $res->format("Y-m-d"));

        $this->PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31,
        ], false);
        $this->PaymentService->clearCachePaymentSettings();

        GoalousDateTime::setTestNow("2017-04-30");
        $res = $this->PaymentService->getCurrentMonthBaseDate($teamId, GoalousDateTime::now()->getTimestamp());
        $this->assertEquals('2017-04-30', $res->format("Y-m-d"));
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $paymentKeyFlagClient = new PaymentFlagClient();

        $paymentFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_FLAG_NAME);
        $paymentDateKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_START_DATE_NAME);
        $paymentKeyFlagClient->del($paymentFlagKey);
        $paymentKeyFlagClient->del($paymentDateKey);
        unset($this->PaymentService);
        parent::tearDown();
    }
}
