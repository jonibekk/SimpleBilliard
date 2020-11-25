<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Team', 'Model');
App::import('Service', 'PaymentService');

use Goalous\Enum as Enum;

/**
 * Team Test Case
 *
 * @property Team $Team
 */
class TeamTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.team_member',
        'app.term',
        'app.email',
        'app.payment_setting',
        'app.charge_history',
        'app.credit_card',
        'app.invoice',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Team = ClassRegistry::init('Team');
        $this->PaymentService = ClassRegistry::init('PaymentService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Team);

        parent::tearDown();
    }

    function testAddNoData()
    {
        $res = $this->Team->add(['Team' => ['name' => null]], "test");
        $this->assertFalse($res, "[異常]チーム追加 データ不正");
    }

    function testAddSuccess()
    {
        $postData = [
            'Team' => [
                'name'     => "test",
                'type'     => 1,
                'timezone' => '+9.0'
            ]
        ];
        $uid = '1';
        $res = $this->Team->add($postData, $uid);
        $this->assertTrue($res, "[正常]チーム追加");

        $newTeam = $this->Team->getById($this->Team->getLastInsertID());
        $this->assertEquals($newTeam['service_use_status'], Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $this->assertEquals($newTeam['service_use_state_start_date'], AppUtil::todayDateYmdLocal(9.0));
        $stateDays = Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_FREE_TRIAL];
        $this->assertEquals($newTeam['service_use_state_end_date'], AppUtil::dateAfter($newTeam['service_use_state_start_date'], $stateDays));

        $newTeamMember = $this->Team->TeamMember->getById($this->Team->TeamMember->getLastInsertID());
        $this->assertEquals($newTeamMember['status'], TeamMember::USER_STATUS_ACTIVE);

        // チーム全体サークルが追加されているか
        $this->Team->Circle->current_team_id = $this->Team->getLastInsertID();
        $teamAllCircle = $this->Team->Circle->getTeamAllCircle();
        $this->assertEquals($this->Team->Circle->current_team_id, $teamAllCircle["Circle"]["team_id"]);
    }

    function testEmailsValidation()
    {
        $emails = "";
        $emails .= "aaaaaa";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:単独のメアド");

        $emails = "";
        $emails .= "aaaaaa@aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:単独のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り一行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り一行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $emails .= "aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り複数行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(空行あり)");

        $emails = "";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(半角スペース混入)");

        $emails = "";
        $emails .= "aaa@aaa.com,　aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com,　aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(全角スペース混入)");

        $emails = "";
        $emails .= ",,," . "\n\n";
        $emails .= ",,," . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り複数行のメアド(データ0件)");
    }

    function testGetEmailListFromPost()
    {
        $postData = [];
        $res = $this->Team->getEmailListFromPost($postData);
        $this->assertNull($res, "[異常]テキストからメアド抽出:データなし");

        $emails = "";
        $emails .= ",,," . "\n\n";
        $emails .= ",,," . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $this->assertNull($res, "[異常]テキストからメアド抽出:validationError");

        $emails = "";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $actual = ["aaa@aaa.com"];
        $this->assertEquals($res, $actual, "[正常]テキストからメアド抽出:ダブりメアドを除去");

        $emails = "";
        $emails .= ", ,,," . "\n\n";
        $emails .= "aaa@aaa.com, bbb@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $actual = ["aaa@aaa.com", "bbb@aaa.com"];
        $this->assertEquals($res, $actual, "[正常]テキストからメアド抽出:空を除去");

    }

    function testGetCurrentTeam()
    {
        // current_team_id がセットされてない場合
        $this->assertEmpty($this->Team->getCurrentTeam());

        // current_team_id がセットされている場合
        $this->_setDefault();
        $current_team = $this->Team->getCurrentTeam();
        $this->assertEquals($this->Team->current_team_id, $current_team['Team']['id']);
    }

    function testGetBorderMonthsOptions()
    {
        $actual = $this->Team->getBorderMonthsOptions();
        $this->assertNotEmpty($actual);
        $this->assertCount(4, $actual);
    }

    function testGetMonths()
    {
        $actual = $this->Team->getMonths();
        $this->assertNotEmpty($actual);
        $this->assertCount(13, $actual);
    }

    function testDeleteTeam()
    {
        $this->_setDefault();

        $team = $this->Team->findById(1);
        $this->assertNotEmpty($team);

        $res = $this->Team->deleteTeam(1);
        $this->assertTrue($res);

        $team = $this->Team->findById(1);
        $this->assertEmpty($team);
    }

    function testGetList()
    {
        $this->_setDefault();
        $this->Team->deleteAll(['1' => '1']);
        $this->Team->saveAll(
            [
                [
                    'id'   => 100,
                    'name' => 'test1',
                ],
                [
                    'id'   => 200,
                    'name' => 'test2',
                ]
            ]
        );
        $actual = $this->Team->getListWithTeamId();
        $expected = [
            (int)100 => '100_test1',
            (int)200 => '200_test2'
        ];

        $this->assertEquals($expected, $actual);

    }

    function test_updateTermSettings()
    {
        $teamId = $this->createTeam(['start_term_month' => 4, 'border_months' => 10]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->Team->updateTermSettings(1, 4);
        $newTeam = $this->Team->getById($teamId);
        $this->assertEquals($newTeam['start_term_month'], 1);
        $this->assertEquals($newTeam['border_months'], 4);
    }

    function test_findByServiceUseStatus()
    {
        $this->Team->id = 1;
        $this->Team->saveField('service_use_status', Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $this->Team->id = 2;
        $this->Team->saveField('service_use_status', Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $ret = $this->Team->findByServiceUseStatus(Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $this->assertCount(2, $ret);
        $this->Team->id = 1;
        $ret = $this->Team->saveField('service_use_status', Team::SERVICE_USE_STATUS_READ_ONLY);
        $this->assertCount(1, $ret);
    }

    function test_updateAllServiceUseStateStartDate_success()
    {
        $freeTrialTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $paidTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_PAID]);
        $readOnlyTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $cannotUseTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);

        $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_FREE_TRIAL, '2017-07-01');
        $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_PAID, '2017-07-02');
        $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_READ_ONLY, '2017-07-03');
        $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_CANNOT_USE, '2017-07-04');

        $this->assertEqual(
            Hash::get($this->Team->findById($freeTrialTeamId), 'Team.service_use_state_start_date'),
            '2017-07-01'
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($freeTrialTeamId), 'Team.service_use_state_end_date'),
            AppUtil::dateAfter('2017-07-01', Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_FREE_TRIAL])
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($paidTeamId), 'Team.service_use_state_start_date'),
            '2017-07-02'
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($paidTeamId), 'Team.service_use_state_end_date'),
            null
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($readOnlyTeamId), 'Team.service_use_state_start_date'),
            '2017-07-03'
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($readOnlyTeamId), 'Team.service_use_state_end_date'),
            AppUtil::dateAfter('2017-07-03', Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_READ_ONLY])
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($cannotUseTeamId), 'Team.service_use_state_start_date'),
            '2017-07-04'
        );
        $this->assertEqual(
            Hash::get($this->Team->findById($cannotUseTeamId), 'Team.service_use_state_end_date'),
            AppUtil::dateAfter('2017-07-04', Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_CANNOT_USE])
        );
    }

    public function test_isPaidPlan()
    {
        $freeTrialTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->assertFalse($this->Team->isPaidPlan($freeTrialTeamId));
        $paidPlanTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_PAID]);
        $this->assertTrue($this->Team->isPaidPlan($paidPlanTeamId));
        $readOnlyTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $this->assertFalse($this->Team->isPaidPlan($readOnlyTeamId));
        $cantUseTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);
        $this->assertFalse($this->Team->isPaidPlan($cantUseTeamId));
    }

    public function test_isFreeTrial()
    {
        $freeTrialTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->assertTrue($this->Team->isFreeTrial($freeTrialTeamId));
        $paidPlanTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_PAID]);
        $this->assertFalse($this->Team->isFreeTrial($paidPlanTeamId));
        $readOnlyTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $this->assertFalse($this->Team->isFreeTrial($readOnlyTeamId));
        $cantUseTeamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);
        $this->assertFalse($this->Team->isFreeTrial($cantUseTeamId));
    }

    public function test_getCountry()
    {
        $teamId = $this->createTeam(['country' => 'JP']);
        $this->assertEqual($this->Team->getCountry($teamId), 'JP');
        $teamId = $this->createTeam(['country' => 'US']);
        $this->assertEqual($this->Team->getCountry($teamId), 'US');
        $teamId = $this->createTeam(['country' => null]);
        $this->assertEqual($this->Team->getCountry($teamId), null);
    }

    public function test_updatePaidPlan()
    {
        $teamId = 1;
        $fields = [
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
            'service_use_state_start_date' => '2017-01-01',
            'service_use_state_end_date'   => '2017-01-16',
        ];
        $this->Team->updateAll($fields, ['id' => $teamId]);
        $startDate = '2017-01-15';
        $res = $this->Team->updatePaidPlan($teamId, $startDate);
        $this->assertTrue($res);
        $team = $this->Team->getById($teamId, array_keys($fields));
        $this->assertEquals($team, [
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::PAID,
            'service_use_state_start_date' => $startDate,
            'service_use_state_end_date'   => null,
        ]);
    }

    public function test_getAmountPerUser()
    {
        $teamAId = $this->createTeam(['pre_register_amount_per_user' => 1500]);
        $this->assertEqual($this->Team->getAmountPerUser($teamAId), 1500);
        $teamBId = $this->createTeam();
        $this->assertEqual($this->Team->getAmountPerUser($teamBId), null);
    }

    public function test_findTargetsForMovingReadOnly_basic()
    {
        $startTs = strtotime("2017-01-01 00:00:00");
        $endTs = strtotime("2017-03-01 23:59:59");
        // Not paid plan team
        // Free trial
        $this->createTeam(["service_use_status" => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL]);
        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);

        // Read only
        $this->createTeam(["service_use_status" => Enum\Model\Team\ServiceUseStatus::READ_ONLY]);
        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);

        // Invoice payment type team
        $this->createInvoicePaidTeam();
        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);

        // Charge fail 1 count
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam();
        $usersCount = 5;
        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeByUserCnt($teamId, $usersCount);
        $historyData = [
            'team_id'          => $teamId,
            'payment_type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $usersCount,
            'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
            'charge_datetime'  => strtotime("2017-01-01 00:00:00"),
            'result_type'      => Enum\Model\ChargeHistory\ResultType::FAIL,
            'max_charge_users' => $usersCount
        ];
        $this->ChargeHistory->create();
        $this->ChargeHistory->save($historyData, false);

        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);


        // Charge fail 2 count
        $historyData2 = array_merge($historyData, ['charge_datetime' => strtotime("2017-02-01 00:00:00")]);
        $this->ChargeHistory->create();
        $historyId2 = $this->ChargeHistory->save($historyData2, false);

        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);

        // Charge fail 3 count
        $historyData3 = array_merge($historyData, ['charge_datetime' => strtotime("2017-03-01 00:00:00")]);
        $this->ChargeHistory->create();
        $this->ChargeHistory->save($historyData3, false);

        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0], $teamId);

        // Charge fail 3 count past
        $startTs = strtotime("2017-01-02 00:00:00");
        $endTs = strtotime("2017-03-02 23:59:59");

        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);

        // Charge fail 3 count but not continuously
        $this->ChargeHistory->id = $historyId2;
        $this->ChargeHistory->save(['result_type' => Enum\Model\ChargeHistory\ResultType::SUCCESS], false);
        $res = $this->Team->findTargetsForMovingReadOnly($startTs, $endTs);
        $this->assertEmpty($res);
    }

    public function test_findExpiredTeamIds_success()
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $this->createInvoicePaidTeam(['service_use_state_end_date' => '2020-01-02']);

        $result = $Team->findTeamIdsStatusExpired(Enum\Model\Team\ServiceUseStatus::PAID, '2019-01-01');
        $this->assertEmpty($result);

        $result = $Team->findTeamIdsStatusExpired(Enum\Model\Team\ServiceUseStatus::PAID, '2020-01-01');
        $this->assertCount(5, $result);

        $result = $Team->findTeamIdsStatusExpired(Enum\Model\Team\ServiceUseStatus::PAID, '2020-01-02');
        $this->assertCount(6, $result);

        $result = $Team->findTeamIdsStatusExpired(Enum\Model\Team\ServiceUseStatus::FREE_TRIAL, '2020-01-02');
        $this->assertEmpty($result);
    }

    public function test_filterPaidTeam_success()
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $teamIds = [4, 5, 6];

        $queryResult = $Team->filterPaidTeam($teamIds);
        $this->assertEquals([4, 5], $queryResult);
    }

    function _setDefault()
    {
        $this->Team->my_uid = 1;
        $this->Team->me['timezone'] = 9;
        $this->Team->current_team_id = 1;
        $this->Team->Term->current_team_id = 1;
        $this->Team->Term->my_uid = 1;
    }

}
