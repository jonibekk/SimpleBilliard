<?php App::uses('GoalousTestCase', 'Test');
App::uses('Team', 'Model');

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

    function _setDefault()
    {
        $this->Team->my_uid = 1;
        $this->Team->me['timezone'] = 9;
        $this->Team->current_team_id = 1;
        $this->Team->Term->current_team_id = 1;
        $this->Team->Term->my_uid = 1;
    }

}
