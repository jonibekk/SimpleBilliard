<?php App::uses('GoalousTestCase', 'Test');
App::uses('TeamMember', 'Model');
App::import('Service', 'TeamSsoSettingService');

use Goalous\Enum as Enum;

/**
 * TeamMember Test Case
 *
 * @property TeamMember $TeamMember
 */
class TeamMemberTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
        'app.evaluate_score',
        'app.invite',
        'app.team_member',
        'app.member_group',
        'app.evaluator',
        'app.email',
        'app.local_name',
        'app.member_type',
        'app.user',
        'app.notify_setting',
        'app.team',
        'app.group',
        'app.job_category',
        'app.term',
        'app.circle',
        'app.circle_member',
        'app.mst_translation_language',
        'app.team_sso_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamMember);

        parent::tearDown();
    }

    function testGetActiveTeamList()
    {
        $uid = '1';
        $data = [
            'TeamMember' => [
                ['user_id' => $uid, 'status' => TeamMember::USER_STATUS_ACTIVE]],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->my_uid = $uid;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $before_cunt = count($this->TeamMember->getActiveTeamList($uid));
        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 2);

        $this->TeamMember->delete();
        $this->TeamMember->myTeams = null;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 2);

        $this->TeamMember->saveField('status', TeamMember::USER_STATUS_INACTIVE);
        $this->TeamMember->myTeams = null;
        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

    }

    function test_getSsoEnabledTeams()
    {
        $uid = '1';
        $data = [
            'TeamMember' => [
                ['user_id' => $uid, 'status' => TeamMember::USER_STATUS_ACTIVE]],
            'Team'       => [
                'name' => 'test'
            ]
        ];

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var TeamSsoSettingService $TeamSsoSettingService */
        $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');

        Cache::delete($this->TeamMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');

        $this->assertEmpty($TeamMember->getSsoEnabledTeams($uid));
        $TeamSsoSettingService->addOrUpdateSetting(1, "https://somesampleidp.com/12345", "https://somesampleidp.com/", "anykindofcertificate");
        $this->assertNotEmpty($TeamMember->getSsoEnabledTeams($uid));
    }

    function testPermissionCheck()
    {
        $team_id = null;
        $uid = '1';
        try {
            $this->TeamMember->permissionCheck($team_id, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]ユーザ権限チェック　チーム切換えなし");

        $this->TeamMember->myStatusWithTeam = null;
        $data = [
            'TeamMember' => [['user_id' => $uid,]],
        ];
        $this->TeamMember->Team->save($data);
        try {
            $this->TeamMember->permissionCheck("test", $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]ユーザ権限チェック　チームなし");

        $this->TeamMember->myStatusWithTeam = null;

        $data = [
            'TeamMember' => [
                [
                    'user_id' => $uid,
                    'status'  => TeamMember::USER_STATUS_INACTIVE,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        try {
            $this->TeamMember->permissionCheck($this->TeamMember->Team->getLastInsertID(), $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]ユーザ権限チェック　非アクティブ");

        $this->TeamMember->myStatusWithTeam = null;

        $data = [
            'TeamMember' => [
                [
                    'user_id' => $uid,
                    'status'  => TeamMember::USER_STATUS_ACTIVE,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        $res = $this->TeamMember->permissionCheck($this->TeamMember->Team->getLastInsertID(), $uid);
        $this->assertTrue($res, "[正常]ユーザ権限チェック");
    }

    function testGetWithTeam()
    {
        $this->setDefault();
        $res = $this->TeamMember->getWithTeam();
        $this->assertNotEmpty($res);
        $this->TeamMember->setMyStatusWithTeam(1, 1);
        $res = $this->TeamMember->getWithTeam();
        $this->assertNotEmpty($res);
        $this->TeamMember->myStatusWithTeam = null;
        $res = $this->TeamMember->getWithTeam();
        $this->assertNotEmpty($res);

    }

    function testAdminCheck()
    {
        $this->TeamMember->myStatusWithTeam = null;
        $uid = '1';

        $data = [
            'TeamMember' => [
                [
                    'user_id'   => $uid,
                    'status'    => TeamMember::USER_STATUS_ACTIVE,
                    'admin_flg' => false,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        try {
            $this->TeamMember->adminCheck($this->TeamMember->Team->getLastInsertID(), $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]アドミンチェック　非アドミン");

        $this->TeamMember->myStatusWithTeam = null;
        $data = [
            'TeamMember' => [
                [
                    'user_id'   => $uid,
                    'status'    => TeamMember::USER_STATUS_ACTIVE,
                    'admin_flg' => true,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->current_team_id = $this->TeamMember->Team->getLastInsertID();
        $this->TeamMember->my_uid = $uid;
        $res = $this->TeamMember->adminCheck();
        $this->assertTrue($res, "[正常]アドミンチェック");

    }

    function testAdd()
    {
        $uid = '1';

        $data = [
            'Team' => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->save($data);
        $res = $this->TeamMember->add($uid, $this->TeamMember->Team->id);
        $this->assertEquals($res['TeamMember']['status'], TeamMember::USER_STATUS_ACTIVE);
        $this->assertArrayHasKey("id", $res['TeamMember'], "[正常]メンバー追加が正常に完了");
        $res = $this->TeamMember->add($uid, $this->TeamMember->Team->id);
        $this->assertEquals($res['TeamMember']['status'], TeamMember::USER_STATUS_ACTIVE);
        $this->assertArrayHasKey("id", $res['TeamMember'], "[正常]メンバー追加が正常に完了");
    }

    function testGetTeamAdminUidNotNull()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $admin_id = $this->TeamMember->getTeamAdminUid();
        $this->assertEquals(1, $admin_id);
    }

    function testGetTeamAdminUidNull()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->updateAll(['TeamMember.admin_flg' => false], ['TeamMember.team_id' => 1]);
        $admin_id = $this->TeamMember->getTeamAdminUid();
        $this->assertEquals(null, $admin_id);
    }

    function testGetAllMemberUserIdList()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->getAllMemberUserIdList(false);
    }

    function testGetAllMemberUserIdListWithEval()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->save(['id' => 1, 'evaluation_enable_flg' => false]);
        $expected = [
            (int)2  => '2',
            (int)3  => '12',
            (int)4  => '13',
            (int)13 => '3',
        ];
        $actual = $this->TeamMember->getAllMemberUserIdList(true, true, true);
        $this->assertEquals($expected, $actual);
    }

    function testValidateUpdateMemberCsvDataNotMatchRecordCount()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);
        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 0
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotMatchColumnCount()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);

        unset($csv_data[0]['email']);
        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 1
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotMatchTitle()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);

        $csv_data[0]['email'] = 'test';
        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 1
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotExistsEmail()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['aaa@aaa.com', 'firstname', 'lastname', 'member_id', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataFirstNameNotEqual()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstnamex', 'lastname', 'member_1', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);
        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataLastNameNotEqual()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastnamex', 'member_1', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);
        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotMemberId()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', '', 'ON', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotActiveFlg()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', '', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataActiveFlgOnOrOffError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'aaa', 'ON', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNoAdminFlg()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', '', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataAdminFlgOnOrOffError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'aa', 'ON']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNoEvaluate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', '']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataEvaluateOnOrOffError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'aaa']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataGroupAlignLeftError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', '', 'group2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataGroupDuplicateError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group1']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataGroupDuplicateMemberId()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON',]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataCoachIdEqualSError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_1'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataEvaluatorAlignLeftError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                '',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataEvaluatorMemberIdError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'member_1',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataEvaluatorDuplicateError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'rater1',
                'rater1'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataRequireAdminAndActive()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'OFF',
                'OFF',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'rater1',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'OFF', 'OFF', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'OFF', 'OFF', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'OFF', 'OFF', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 0
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataDuplicateEmail()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'rater1',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataDuplicateMemberId()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'test@aaa.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'rater1',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['from@email.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);
        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotExistsCoach()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'xxxxxxxxxx',
                'rater1',
                'rater2'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotExistsEvaluator()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
            [
                'from@email.com',
                'firstname',
                'lastname',
                'member_1',
                'ON',
                'ON',
                'ON',
                '',
                'group1',
                'group2',
                'group3',
                'group4',
                'group5',
                'group6',
                'group7',
                'member_2',
                'rater1',
                'rater2',
                'xxxxxxxxxx'
            ]);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
            ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateFinalEvaluationCsvDataUnMatchColumnCount()
    {
        $this->setDefault();
        $this->_saveEvaluations();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeadingEvaluation();
        unset($csv_data[0]['member_no']);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(33), ['member_no' => 'test']);
        $actual = $this->TeamMember->validateUpdateFinalEvaluationCsvData($csv_data, 1);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 1
        ];
        $this->assertEquals($excepted, $actual);

    }

    function testValidateUpdateFinalEvaluationCsvDataUnMatchTitle()
    {
        $this->setDefault();
        $this->_saveEvaluations();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeadingEvaluation();
        $csv_data[0]['member_no'] = 'test';
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(33), ['member_no' => 'test']);
        $actual = $this->TeamMember->validateUpdateFinalEvaluationCsvData($csv_data, 1);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 1
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateFinalEvaluationCsvDataNotExistsMember()
    {
        $this->setDefault();
        $this->_saveEvaluations();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeadingEvaluation();
        $csv_data[1] = $this->getEmptyRowOnCsv(33);
        $csv_data[1] = copyKeyName($this->TeamMember->_getCsvHeadingEvaluation(), $csv_data[1]);
        $csv_data[1] = Hash::merge($csv_data[1],
            ['member_no' => 'test', 'total.final.score' => 'aaaaa']);
        $actual = $this->TeamMember->validateUpdateFinalEvaluationCsvData($csv_data, 1);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);

    }

    function testValidateUpdateFinalEvaluationCsvDataNotExistsScore()
    {
        $this->setDefault();
        $this->_saveEvaluations();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeadingEvaluation();
        $csv_data[1] = $this->getEmptyRowOnCsv(33);
        $csv_data[1] = copyKeyName($this->TeamMember->_getCsvHeadingEvaluation(), $csv_data[1]);
        $csv_data[1] = Hash::merge($csv_data[1],
            ['member_no' => 'member_1', 'total.final.score' => 'aaaaa']);
        $actual = $this->TeamMember->validateUpdateFinalEvaluationCsvData($csv_data, 1);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);

    }

    function testValidateUpdateFinalEvaluationCsvDataMemberIdDuplicated()
    {
        $this->setDefault();
        $this->_saveEvaluations();
        $eval_data = [
            'team_id'           => 1,
            'evaluatee_user_id' => 2,
            'evaluator_user_id' => 1,
            'term_id'           => 1,
            'comment'           => null,
            'evaluate_score_id' => null,
            'evaluate_type'     => 0,
            'goal_id'           => null,
            'index_num'         => 0,
            'status'            => 0
        ];
        $this->TeamMember->Team->Evaluation->save($eval_data);

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeadingEvaluation();
        $csv_data[1] = $this->getEmptyRowOnCsv(33);
        $csv_data[1] = copyKeyName($this->TeamMember->_getCsvHeadingEvaluation(), $csv_data[1]);
        $csv_data[1] = Hash::merge($csv_data[1],
            ['member_no' => 'member_1', 'total.final.score' => 'A']);
        $csv_data[2] = $this->getEmptyRowOnCsv(33);
        $csv_data[2] = copyKeyName($this->TeamMember->_getCsvHeadingEvaluation(), $csv_data[2]);
        $csv_data[2] = Hash::merge($csv_data[2],
            ['member_no' => 'member_1', 'total.final.score' => 'A']);
        $actual = $this->TeamMember->validateUpdateFinalEvaluationCsvData($csv_data, 1);

        if (Hash::get($actual, 'error_msg')) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);

    }

    function testGetAllMembersCsvDataNoUser()
    {
        $data = [
            'team_id' => 1
        ];
        $this->TeamMember->save($data);
        $this->TeamMember->getAllMembersCsvData(1);
    }

    function getEmptyRowOnCsv($colum_count = 30)
    {
        $row = [];
        for ($i = 0; $i < $colum_count; $i++) {
            $row[] = null;
        }
        return $row;
    }

    function testActivateMembers()
    {
        $res = $this->TeamMember->activateMembers('1000', 100000);
        $this->asserttrue($res);
    }

    function testIsActiveTrue()
    {
        $this->TeamMember->current_team_id = 1;
        $uid = 1;
        $this->assertTrue($this->TeamMember->isActive($uid));
    }

    function testIsActiveFalse()
    {
        $this->TeamMember->current_team_id = 1;
        $uid = 1;
        $this->assertFalse($this->TeamMember->isActive($uid, 10000));
    }

    function testIsActiveFalseCurrentTeamIdNull()
    {
        $this->TeamMember->current_team_id = null;
        $uid = 1;
        $this->assertFalse($this->TeamMember->isActive($uid));
    }

    function testIsActiveDefault()
    {
        $this->setDefault();
        $res = $this->TeamMember->isActive(1);
        $this->assertTrue($res);
        $res = $this->TeamMember->isActive(1);
        $this->assertTrue($res);
        $res = $this->TeamMember->isActive(999);
        $this->assertFalse($res);
        $res = $this->TeamMember->isActive(999);
        $this->assertFalse($res);
    }

    function setDefault()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->User->Email->current_team_id = $team_id;
        $this->TeamMember->User->Email->my_uid = $uid;
    }

    function testSelectCoachUserIdFromTeamMembersTB()
    {
        $user_id = 777;
        $team_id = 888;
        $coach_user_id = 999;

        $params = [
            'user_id'       => $user_id,
            'team_id'       => $team_id,
            'coach_user_id' => $coach_user_id,
            'status'        => TeamMember::USER_STATUS_ACTIVE
        ];
        $this->TeamMember->save($params, false);
        $res = $this->TeamMember->getCoachUserIdByMemberUserId($user_id);
        $this->assertEquals($coach_user_id, $res);

    }

    function testSelectUserIdFromTeamMembersTB()
    {
        $user_id = 777;
        $team_id = 888;
        $coach_user_id = 999;

        $params = [
            'user_id'       => $user_id,
            'team_id'       => $team_id,
            'coach_user_id' => $coach_user_id,
            'status'        => TeamMember::USER_STATUS_ACTIVE
        ];
        $this->TeamMember->save($params, false);
        $res = $this->TeamMember->getMyMembersList($coach_user_id);
        $this->assertContains($user_id, $res);
    }

    function testGetEvaluationEnableFlgReturnTrue()
    {
        $user_id = 777;
        $team_id = 888;

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $params = [
            'user_id'               => $user_id,
            'team_id'               => $team_id,
            'status'                => TeamMember::USER_STATUS_ACTIVE,
            'evaluation_enable_flg' => 1
        ];
        $this->TeamMember->save($params);
        $this->TeamMember->current_team_id = $team_id;
        $flg = $this->TeamMember->getEvaluationEnableFlg($user_id);
        $this->assertTrue($flg);
    }

    function testGetEvaluationEnableFlgReturnFalsePattern1()
    {
        $user_id = 777;
        $team_id = 888;

        $params = [
            'user_id'               => $user_id,
            'team_id'               => $team_id,
            'status'                => TeamMember::USER_STATUS_INACTIVE,
            'evaluation_enable_flg' => 1
        ];
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->save($params);
        $flg = $this->TeamMember->getEvaluationEnableFlg($user_id);
        $this->assertFalse($flg);
    }

    function testGetEvaluationEnableFlgReturnFalsePattern2()
    {
        $user_id = 777;
        $team_id = 888;

        $params = [
            'user_id'               => $user_id,
            'team_id'               => $team_id,
            'status'                => TeamMember::USER_STATUS_ACTIVE,
            'evaluation_enable_flg' => 0
        ];
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->save($params);
        $flg = $this->TeamMember->getEvaluationEnableFlg($user_id);
        $this->assertFalse($flg);
    }

    function testAddDefaultSellForCsvData()
    {
        $this->TeamMember->addDefaultSellForCsvData('test');
        $this->assertEmpty($this->TeamMember->csv_datas);
    }

    function testSetTotalFinalEvaluationForCsvDataContinue()
    {
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);

        $this->TeamMember->setTotalFinalEvaluationForCsvData();
    }

    function testSetTotalFinalEvaluationForCsvDataIterator()
    {
        App::uses('Evaluation', 'Model');
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);
        $evaluations = [
            1 => [
                [
                    'Evaluation'    => [
                        'evaluate_type' => Evaluation::TYPE_FINAL_EVALUATOR,
                        'goal_id'       => null,
                        'comment'       => 'nice!'
                    ],
                    'EvaluateScore' => [
                        'name' => 'score_name',
                    ]
                ]
            ]
        ];
        $property = $reflectionClass->getProperty('evaluations');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, $evaluations);

        $this->TeamMember->setTotalFinalEvaluationForCsvData();

        $expected = [
            (int)0 => [
                'total.final.score'   => 'score_name',
                'total.final.comment' => 'nice!'
            ]
        ];
        $actual = $this->TeamMember->csv_datas;
        $this->assertEquals($expected, $actual);
    }

    function testSetTotalEvaluatorEvaluationForCsvDataContinue()
    {
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);
        $this->TeamMember->setTotalEvaluatorEvaluationForCsvData();
    }

    function testSetTotalEvaluatorEvaluationForCsvDataIterator()
    {
        App::uses('Evaluation', 'Model');
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);
        $evaluations = [
            1 => [
                [
                    'Evaluation'    => [
                        'evaluate_type' => Evaluation::TYPE_EVALUATOR,
                        'goal_id'       => null,
                        'comment'       => 'nice!'
                    ],
                    'EvaluateScore' => [
                        'name' => 'score_name',
                    ],
                    'EvaluatorUser' => [
                        'display_username' => 'test user'
                    ]
                ]
            ]
        ];
        $property = $reflectionClass->getProperty('evaluations');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, $evaluations);

        $this->TeamMember->setTotalEvaluatorEvaluationForCsvData();

        $expected = [
            (int)0 => [
                'total.evaluator.1.name'    => 'test user',
                'total.evaluator.1.score'   => 'score_name',
                'total.evaluator.1.comment' => 'nice!'
            ]
        ];
        $actual = $this->TeamMember->csv_datas;
        $this->assertEquals($expected, $actual);
    }

    function testSetTotalSelfEvaluationForCsvDataContinue()
    {
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);
        $this->TeamMember->setTotalSelfEvaluationForCsvData();
    }

    function testSetGoalEvaluationForCsvData()
    {
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => 1]]]);
        $this->TeamMember->setGoalEvaluationForCsvData();
        $expected = [
            (int)0 => [
                'kr_count'      => (int)0,
                'action_count'  => (int)0,
                'goal_progress' => (int)0
            ]
        ];
        $actual = $this->TeamMember->csv_datas;
        $this->assertEquals($expected, $actual);
    }

    function testSetUserInfoForCsvDataContinue()
    {
        $reflectionClass = new ReflectionClass($this->TeamMember);
        $property = $reflectionClass->getProperty('all_users');
        $property->setAccessible(true);
        $property->setValue($this->TeamMember, [['User' => ['id' => null]]]);
        $this->TeamMember->setUserInfoForCsvData();
    }

    function testSetAllMembers()
    {
        $this->TeamMember->current_team_id = 1;
        $this->TeamMember->setAllMembers(null, 'final_evaluation');
    }

    function testSetAdminUserFlagPatternON()
    {
        $member_id = 999;
        $params = [
            'id'        => $member_id,
            'admin_flg' => 0
        ];
        $this->TeamMember->save($params);
        $this->TeamMember->setAdminUserFlag($member_id, 'ON');

        $options['conditions']['id'] = $member_id;
        $res = $this->TeamMember->find('first', $options);
        $this->assertEquals(1, $res['TeamMember']['admin_flg']);
    }

    function testSetAdminUserFlagPatternOFF()
    {
        $member_id = 999;
        $params = [
            'id'        => $member_id,
            'admin_flg' => 0,
        ];
        $this->TeamMember->save($params);
        $this->TeamMember->setAdminUserFlag($member_id, 'OFF');

        $options['conditions']['id'] = $member_id;
        $res = $this->TeamMember->find('first', $options);
        $this->assertEquals(0, $res['TeamMember']['admin_flg']);
    }

    function test_inactivate()
    {
        $memberId = 999;
        $params = [
            'id'     => $memberId,
            'status' => TeamMember::USER_STATUS_ACTIVE,
        ];
        $this->TeamMember->save($params, false);
        $this->TeamMember->inactivate($memberId);

        $options['conditions']['id'] = $memberId;
        $res = $this->TeamMember->find('first', $options);
        $this->assertEquals(TeamMember::USER_STATUS_INACTIVE, $res['TeamMember']['status']);
    }

    function testSetEvaluationEnableFlagPatternON()
    {
        $member_id = 999;
        $params = [
            'id'                    => $member_id,
            'evaluation_enable_flg' => 0,
        ];
        $this->TeamMember->save($params);
        $this->TeamMember->setEvaluationFlag($member_id, 'ON');

        $options['conditions']['id'] = $member_id;
        $res = $this->TeamMember->find('first', $options);
        $this->assertEquals(1, $res['TeamMember']['evaluation_enable_flg']);
    }

    function testSetEvaluationEnableFlagPatternOFF()
    {
        $member_id = 999;
        $params = [
            'id'                    => $member_id,
            'first_name'            => 'test',
            'last_name'             => 'test',
            'evaluation_enable_flg' => 0,
        ];
        $this->TeamMember->save($params);
        $this->TeamMember->setEvaluationFlag($member_id, 'OFF');

        $options['conditions']['id'] = $member_id;
        $res = $this->TeamMember->find('first', $options);
        $this->assertEquals(0, $res['TeamMember']['evaluation_enable_flg']);
    }

    function testSelect2faStepMemberInfoTypeTrue()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
            '2fa_secret' => null,
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 999;
        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
        ];
        $this->TeamMember->save($params);

        $res = $this->TeamMember->select2faStepMemberInfo($team_id);
        $this->assertEquals($user_id, $res[0]['User']['id']);
    }

    function testSelect2faStepMemberInfoTypeFalse()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
            '2fa_secret' => 'test',
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 999;
        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
        ];
        $this->TeamMember->save($params);

        $res = $this->TeamMember->select2faStepMemberInfo($team_id);
        $this->assertCount(0, $res);
    }

    function testSelectAdminMemberInfoTypeTrue()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
        ];
        $res = $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 888;
        $params = [
            'user_id'   => $user_id,
            'team_id'   => $team_id,
            'admin_flg' => 1
        ];
        $this->TeamMember->save($params);
        $res = $this->TeamMember->selectAdminMemberInfo($team_id);
        $this->assertEquals($user_id, $res[0]['User']['id']);
    }

    function testSelectAdminMemberInfoTypeFalse()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 888;
        $params = [
            'user_id'   => $user_id,
            'team_id'   => $team_id,
            'admin_flg' => 0
        ];
        $this->TeamMember->save($params);
        $res = $this->TeamMember->selectAdminMemberInfo($team_id);
        $this->assertCount(0, $res);
    }

    function testSelectMemberInfo()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 888;
        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
        ];
        $this->TeamMember->save($params);
        $res = $this->TeamMember->selectMemberInfo($team_id);
        $this->assertCount(1, $res);
    }

    function testSelectGroupMemberInfo()
    {
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        $team_id = 888;
        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
        ];
        $this->TeamMember->save($params);

        $group_id = 1;
        $params = [
            'user_id'  => $user_id,
            'team_id'  => $team_id,
            'group_id' => $group_id,
        ];
        $this->TeamMember->User->MemberGroup->save($params);
        $res = $this->TeamMember->selectGroupMemberInfo($team_id, $group_id);
        $this->assertEquals($group_id, $res[0]['User']['MemberGroup'][0]['group_id']);
    }

    function testDefineTeamMemberOption()
    {
        $team_id = 999;
        $options = [
            'fields'     => ['id', 'status', 'admin_flg', 'coach_user_id', 'evaluation_enable_flg', 'created'],
            'conditions' => [
                'team_id' => $team_id,
            ],
            'order'      => ['TeamMember.created' => 'DESC'],
            'contain'    => [
                'User'      => [
                    'fields'      => ['id', 'first_name', 'last_name', '2fa_secret', 'photo_file_name'],
                    'MemberGroup' => [
                        'fields' => ['group_id'],
                        'Group'  => [
                            'fields' => ['name']
                        ]
                    ],
                ],
                'CoachUser' => [
                    'fields' => $this->TeamMember->User->profileFields
                ],
                'Email'     => [
                    'fields' => ['Email.id', 'Email.user_id', 'Email.email_verified']
                ]
            ]
        ];
        $res = $this->TeamMember->defineTeamMemberOption($team_id);
        $this->assertEquals($options, $res);
    }

    function testConvertMemberData()
    {
        // me
        $user_id = 999;
        $params = [
            'id'         => $user_id,
            'first_name' => 'test',
            'last_name'  => 'test',
        ];
        $this->TeamMember->User->save($params);

        $this->TeamMember->User->Email->save([
            'user_id'        => $user_id,
            'email_verified' => true
        ], false);

        // coach
        $coach_user_id = 777;
        $params = [
            'id'         => $coach_user_id,
            'first_name' => 'coach',
            'last_name'  => 'a'
        ];
        $this->TeamMember->User->save($params);

        $team_id = 888;
        $params = [
            'user_id'       => $user_id,
            'team_id'       => $team_id,
            'coach_user_id' => $coach_user_id,
        ];
        $this->TeamMember->save($params);

        $group_id = 1;
        $params = [
            'user_id'  => $user_id,
            'team_id'  => $team_id,
            'group_id' => $group_id,
        ];
        $this->TeamMember->User->MemberGroup->save($params);

        $group_name = 'SDG';
        $params = [
            'id'      => $group_id,
            'team_id' => $team_id,
            'name'    => $group_name
        ];
        $this->TeamMember->User->MemberGroup->Group->save($params);

        $res = $this->TeamMember->selectGroupMemberInfo($team_id, $group_id);
        $convert_data = $this->TeamMember->convertMemberData($res);
        $this->assertEquals($group_name, $convert_data[0]['TeamMember']['group_name']);
        $this->assertFalse($convert_data[0]['User']['two_step_flg']);
        $this->assertArrayHasKey('coach_name', $convert_data[0]['TeamMember']);
    }

    function testGetActiveTeamMembersList()
    {
        $this->setDefault();
        $res = $this->TeamMember->getActiveTeamMembersList();
        $this->assertNotEmpty($res);
        $res = $this->TeamMember->getActiveTeamMembersList();
        $this->assertNotEmpty($res);
    }

    function testCountActiveMembersByTeamId()
    {
        $members = $this->TeamMember->find('all', [
            'fields' => [
                'TeamMember.team_id',
                'TeamMember.status',
            ],
        ]);

        $counts = [];
        foreach ($members as $v) {
            if (!$v['TeamMember']['status']) {
                continue;
            }
            if (!isset($counts[$v['TeamMember']['team_id']])) {
                $counts[$v['TeamMember']['team_id']] = 0;
            }
            $counts[$v['TeamMember']['team_id']]++;
        }

        foreach ($counts as $team_id => $count) {
            $res = $this->TeamMember->countActiveMembersByTeamId($team_id);
            $this->assertEquals($count, $res);
        }
    }

    function testUpdateLastLogin()
    {
        $this->setDefault();
        $this->assertNotEmpty($this->TeamMember->updateLastLogin(1, 1));
    }

    function testDeleteCacheMember()
    {
        $this->setDefault();
        $this->assertFalse($this->TeamMember->deleteCacheMember(99999));
    }

    function testGetByMemberNo()
    {
        $this->setDefault();
        $this->assertNotEmpty($this->TeamMember->getByMemberNo('member_1'));
    }

    function testGetByUserId()
    {
        $this->setDefault();
        $this->assertNotEmpty($this->TeamMember->getByUserId(1));
    }

    function testGetLoginUserAdminFlag()
    {
        $this->setDefault();
        $this->assertTrue($this->TeamMember->getLoginUserAdminFlag(1, 1));
        $this->assertFalse($this->TeamMember->getLoginUserAdminFlag(1, 2));
    }

    function testGetAdminUserCount()
    {
        $this->setDefault();
        $actual = $this->TeamMember->getAdminUserCount(1);
        $this->assertEquals(3, $actual);
    }

    function testGetCoachId()
    {
        $this->setDefault();
        $actual = $this->TeamMember->getCoachId(1, 1);
        $this->assertEquals(2, $actual);

    }

    function testGetUserIdsByMemberNo()
    {
        $this->setDefault();
        $actual = $this->TeamMember->getUserIdsByMemberNos(['member_1', 'member_2']);
        $this->assertCount(2, $actual);
    }

    function testIsAdmin()
    {
        $this->setDefault();
        $actual = $this->TeamMember->isAdmin();
        $this->assertNotEmpty($actual);
    }

    function testGetIdByTeamAndUserId()
    {
        $this->setDefault();
        $this->assertNotEmpty($this->TeamMember->getIdByTeamAndUserId(1, 1));
    }

    function testGetAllTeamNotFormat()
    {
        $this->setDefault();
        $res = $this->TeamMember->getAllTeam(1);
        $this->assertNotEmpty($res);
        $this->assertArrayHasKey('TeamMember', $res[0]);
        $this->assertArrayHasKey('Team', $res[0]);
    }

    function testGetAllTeamFormat()
    {
        $this->setDefault();
        $res = $this->TeamMember->getAllTeam(1, true);
        $this->assertNotEmpty($res);
        $this->assertFalse(isset($res[0]['TeamMember']));
        $this->assertFalse(isset($res[0]['Team']));
    }

    function test_isActiveAdmin()
    {
        // Delete fixture data for testing
        $this->TeamMember->deleteAll(['TeamMember.team_id' => 1]);

        // TeamMember: active admin, User: active
        $this->TeamMember->save(
            ['id' => 1, 'user_id' => 1, 'team_id' => 1, 'admin_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE]
        );
        $this->TeamMember->save(
            ['id' => 2, 'user_id' => 2, 'team_id' => 1, 'admin_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE]
        );
        $this->TeamMember->User->save(['user_id' => 1, 'active_flg' => true]);
        $this->assertTrue($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertTrue($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: is not admin
        $this->TeamMember->save(
            [
                'id'        => 1,
                'user_id'   => 1,
                'team_id'   => 1,
                'admin_flg' => false,
                'status'    => TeamMember::USER_STATUS_ACTIVE
            ]
        );
        $this->TeamMember->User->save(['id' => 1, 'active_flg' => true]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertTrue($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: is not admin
        $this->TeamMember->save(
            [
                'id'        => 2,
                'user_id'   => 2,
                'team_id'   => 1,
                'admin_flg' => false,
                'status'    => TeamMember::USER_STATUS_ACTIVE
            ]
        );
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: active, User: not active
        $this->TeamMember->save(
            ['id' => 1, 'user_id' => 1, 'team_id' => 1, 'admin_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE]
        );
        $this->TeamMember->save(
            ['id' => 2, 'user_id' => 2, 'team_id' => 1, 'admin_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE]
        );
        $this->TeamMember->User->save(['id' => 1, 'active_flg' => false]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertTrue($this->TeamMember->isActiveAdmin(2, 1));
        $this->TeamMember->User->save(['id' => 2, 'active_flg' => false]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: not active, User: active
        $this->TeamMember->save(
            [
                'id'        => 1,
                'user_id'   => 1,
                'team_id'   => 1,
                'admin_flg' => true,
                'status'    => TeamMember::USER_STATUS_INACTIVE
            ]
        );
        $this->TeamMember->User->save(['id' => 1, 'active_flg' => true]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: not active, User: active
        $this->TeamMember->save(
            [
                'id'        => 2,
                'user_id'   => 2,
                'team_id'   => 1,
                'admin_flg' => true,
                'status'    => TeamMember::USER_STATUS_INACTIVE
            ]
        );
        $this->TeamMember->User->save(['id' => 2, 'active_flg' => true]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));

        // TeamMember: not active, User: not active
        $this->TeamMember->save(
            [
                'id'        => 1,
                'user_id'   => 1,
                'team_id'   => 1,
                'admin_flg' => true,
                'status'    => TeamMember::USER_STATUS_ACTIVE
            ]
        );
        $this->TeamMember->User->save(['id' => 1, 'active_flg' => false]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));


        // TeamMember: not active, User: not active
        $this->TeamMember->save(
            [
                'id'        => 2,
                'user_id'   => 2,
                'team_id'   => 1,
                'admin_flg' => true,
                'status'    => TeamMember::USER_STATUS_ACTIVE
            ]
        );
        $this->TeamMember->User->save(['id' => 2, 'active_flg' => false]);
        $this->assertFalse($this->TeamMember->isActiveAdmin(1, 1));
        $this->assertFalse($this->TeamMember->isActiveAdmin(2, 1));
    }

    function test_updateActiveFlgToStatus_success()
    {
        $this->TeamMember->save(['status' => TeamMember::USER_STATUS_ACTIVE], false);
        $teamMemberId = $this->TeamMember->getLastInsertId();
        $this->TeamMember->updateActiveFlgToStatus();
        $newStatus = Hash::get($this->TeamMember->getById($teamMemberId), 'status');
        $this->assertEqual($newStatus, TeamMember::USER_STATUS_ACTIVE);
    }

    function test_updateInActiveFlgToStatus_success()
    {
        $this->TeamMember->save(['status' => TeamMember::USER_STATUS_INACTIVE], false);
        $teamMemberId = $this->TeamMember->getLastInsertId();
        $this->TeamMember->updateInactiveFlgToStatus();
        $newStatus = Hash::get($this->TeamMember->getById($teamMemberId), 'status');
        $this->assertEqual($newStatus, TeamMember::USER_STATUS_INACTIVE);
    }

    function test_findAdminList()
    {
        // It's expected decrement from list when changed to inactive team member.
        $retBefore = $this->TeamMember->findAdminList(1);
        $userId = current($retBefore);
        $this->TeamMember->updateAll(['TeamMember.status' => TeamMember::USER_STATUS_INACTIVE],
            ['TeamMember.team_id' => 1, 'TeamMember.user_id' => $userId]);
        $afterOneInactivated = $this->TeamMember->findAdminList(1);
        $this->assertEquals(count($afterOneInactivated), count($retBefore) - 1);

        // It's expected decrement from list when changed to not admin team member.
        $userId = current($afterOneInactivated);
        $this->TeamMember->updateAll(['TeamMember.admin_flg' => false],
            ['TeamMember.team_id' => 1, 'TeamMember.user_id' => $userId]);
        $afterOneToNormalMember = $this->TeamMember->findAdminList(1);
        $this->assertEquals(count($afterOneToNormalMember), count($afterOneInactivated) - 1);

        // It's expected decrement from list when changed to inactive user.
        $userId = current($afterOneToNormalMember);
        $this->TeamMember->User->updateAll(['User.active_flg' => false],
            ['User.id' => $userId]);
        $afterOneToInactive = $this->TeamMember->findAdminList(1);
        $this->assertEquals(count($afterOneToInactive), count($afterOneToNormalMember) - 1);
    }

    function _saveEvaluations()
    {
        $records = [
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'term_id'           => 1,
                'evaluate_type'     => 0,
                'comment'           => 'あいうえお',
                'evaluate_score_id' => 1,
                'index_num'         => 0,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'term_id'           => 1,
                'evaluate_type'     => 0,
                'comment'           => 'かきくけこ',
                'evaluate_score_id' => 1,
                'index_num'         => 1,
                'goal_id'           => 1,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'term_id'           => 1,
                'evaluate_type'     => 0,
                'comment'           => 'さしすせそ',
                'evaluate_score_id' => 1,
                'index_num'         => 2,
                'goal_id'           => 2,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'term_id'           => 1,
                'evaluate_type'     => 0,
                'comment'           => 'たちつてと',
                'evaluate_score_id' => 1,
                'index_num'         => 3,
                'goal_id'           => 3,
            ],
            [
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'term_id'           => 2,
                'evaluate_type'     => 0,
                'comment'           => 'なにぬねの',
                'evaluate_score_id' => 1,
                'index_num'         => 0,
                'goal_id'           => 10,
            ],
            [
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'term_id'           => 2,
                'evaluate_type'     => 0,
                'comment'           => 'はひふへほ',
                'evaluate_score_id' => 1,
                'index_num'         => 1,
                'goal_id'           => 11,
            ],
            [
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'term_id'           => 2,
                'evaluate_type'     => 0,
                'comment'           => 'まみむめも',
                'evaluate_score_id' => 1,
                'index_num'         => 2,
                'goal_id'           => 12,
            ],
        ];

        $this->TeamMember->Team->Evaluation->saveAll($records);
    }

    public function test_countChargeTargetUsersEachTeam()
    {
        $this->TeamMember->deleteAll(['TeamMember.del_flg' => false]);
        $this->createActiveUser(1);
        $ret = $this->TeamMember->countChargeTargetUsersEachTeam([1, 2]);
        // TODO: Add other pattern tests
    }

    public function test_isTeamMember()
    {
        $teamId = 1;
        $userId = 1;
        $teamMemberId = $this->createTeamMember($teamId, $userId);
        $this->assertTrue($this->TeamMember->isTeamMember($teamId, $teamMemberId));

        $otherTeamId = 2;
        $userId = 2;
        $otherTeamMemberId = $this->createTeamMember($otherTeamId, $userId);
        $this->assertFalse($this->TeamMember->isTeamMember($teamId, $otherTeamMemberId));
    }

    public function test_isInactive()
    {
        $activeTeamMemberId = $this->createTeamMember(1, 1, TeamMember::USER_STATUS_ACTIVE);
        $this->assertFalse($this->TeamMember->isInactive($activeTeamMemberId));

        $inactiveTeamMemberId = $this->createTeamMember(2, 2, TeamMember::USER_STATUS_INACTIVE);
        $this->assertTrue($this->TeamMember->isInactive($inactiveTeamMemberId));
    }

    public function test_getUserById()
    {
        $userId = $this->createActiveUser(1);
        $tmId = $this->TeamMember->getLastInsertId();

        $res = $this->TeamMember->getUserById($tmId);
        $this->assertEquals($res['id'], $userId);
    }

    public function test_filterActiveMembers()
    {
        $teamId = 1;
        $allMemberIds = Hash::extract($this->TeamMember->find('all', [
            'fields'     => ['user_id'],
            'conditions' => ['team_id' => $teamId]
        ]), '{n}.TeamMember.user_id');

        $res = $this->TeamMember->filterActiveMembers($allMemberIds, $teamId);
        $this->assertEquals($res, $allMemberIds);

        $this->TeamMember->updateAll(
            ['status' => Enum\Model\TeamMember\Status::INACTIVE],
            ['user_id' => 3, 'team_id' => 1]
        );
        $res = $this->TeamMember->filterActiveMembers($allMemberIds, $teamId);
        $this->assertEquals(count($res), 4);
        $diff = array_values(array_diff($allMemberIds, $res));
        $this->assertEquals($diff, [3]);

        $this->TeamMember->updateAll(
            ['status' => Enum\Model\TeamMember\Status::INVITED],
            ['user_id' => 1, 'team_id' => 1]
        );
        $res = $this->TeamMember->filterActiveMembers($allMemberIds, $teamId);
        $this->assertEquals(count($res), 3);
        $this->assertEquals($res, [2, 12, 13]);

        $userId = $this->createActiveUser(1);
        $tmId = $this->TeamMember->getLastInsertId();

        $res = $this->TeamMember->filterActiveMembers($allMemberIds, $teamId);
        $this->assertEquals(count($res), 3);
        $this->assertEquals($res, [2, 12, 13]);

        $allMemberIds[] = $userId;
        $res = $this->TeamMember->filterActiveMembers($allMemberIds, $teamId);
        $this->assertEquals(count($res), 4);
        $this->assertEquals($res, [2, 12, 13, $userId]);
    }

    public function test_setDefaultTranslationLanguage_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userId = 1;
        $teamId = 1;
        $newLanguage = "de";

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, $newLanguage);
        $language = $TeamMember->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals($newLanguage, $language);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_setDefaultTranslationLanguageMemberNotExist_failure()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userId = 495;
        $teamId = 4713;

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, "de");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_setDefaultTranslationLanguageInvalidLanguage_failure()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userId = 1;
        $teamId = 1;

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, 'xx');
    }

    public function test_getDefaultTranslationLanguage_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userId = 1;
        $teamId = 1;
        $newLanguage = "es";

        $language = $TeamMember->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEmpty($language);

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, $newLanguage);
        $language = $TeamMember->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals($newLanguage, $language);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getDefaultTranslationLanguageMemberNotExist_failure()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $userId = 495;
        $teamId = 4713;

        $TeamMember->getDefaultTranslationLanguage($teamId, $userId);
    }

    public function test_deleteTeamMember_success()
    {
        $userId = 1;
        $teamId = 1;

        $res1 = $this->TeamMember->getIdByTeamAndUserId($teamId, $userId);
        $this->assertNotNull($res1);

        // exeute target function
        $this->TeamMember->deleteTeamMember($teamId, $userId);

        $res2 = $this->TeamMember->getIdByTeamAndUserId($teamId, $userId);
        $this->assertNull($res2);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteTeamMember_failure()
    {
        $userId = 1;
        $teamId = 2;

        $res1 = $this->TeamMember->getAllMemberUserIdList();

        // exeute target function
        $this->TeamMember->deleteTeamMember($teamId, $userId);

        $res2 = $this->TeamMember->getAllMemberUserIdList();
        $this->assertCount($res1, $res2);
    }

    public function test_getUnique()
    {
        $res = $this->TeamMember->getUnique(1, 1);
        $this->assertNotEmpty($res);
        $this->assertEqual($res['user_id'], 1);
        $this->assertEqual($res['team_id'], 1);

        $res = $this->TeamMember->getUnique(99999, 100);
        $this->assertEquals($res, []);

    }
}
