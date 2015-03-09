<?php
App::uses('TeamMember', 'Model');

/**
 * TeamMember Test Case
 *
 * @property TeamMember $TeamMember
 */
class TeamMemberTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_member',
        'app.member_group',
        'app.rater',
        'app.email',
        'app.local_name',
        'app.member_type',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.group',
        'app.job_category'
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
            'TeamMember' => [['user_id' => $uid,]],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $before_cunt = count($this->TeamMember->getActiveTeamList($uid));
        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 2);

        $this->TeamMember->delete();
        $this->TeamMember->myTeams = null;
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->myTeams = null;
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 2);

        $this->TeamMember->saveField('active_flg', false);
        $this->TeamMember->myTeams = null;
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), $before_cunt + 1);

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
                    'user_id'    => $uid,
                    'active_flg' => false,
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
                    'user_id'    => $uid,
                    'active_flg' => true,
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
        $data = [
            'TeamMember' => [
                [
                    'user_id'    => 1,
                    'active_flg' => true,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        $this->TeamMember->my_uid = 1;
        $this->TeamMember->getWithTeam();
    }

    function testAdminCheck()
    {
        $this->TeamMember->myStatusWithTeam = null;
        $uid = '1';

        $data = [
            'TeamMember' => [
                [
                    'user_id'    => $uid,
                    'active_flg' => true,
                    'admin_flg'  => false,
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
                    'user_id'    => $uid,
                    'active_flg' => true,
                    'admin_flg'  => true,
                ]
            ],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        $res = $this->TeamMember->adminCheck($this->TeamMember->Team->getLastInsertID(), $uid);
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
        $this->assertTrue($res['TeamMember']['active_flg'], "[正常]メンバー追加でアクティブフラグon");
        $this->assertArrayHasKey("id", $res['TeamMember'], "[正常]メンバー追加が正常に完了");
    }

    function testGetAllMemberUserIdList()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->getAllMemberUserIdList(false);
    }

    function testIncrementNotifyUnreadCount()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->incrementNotifyUnreadCount([1]);
        $this->TeamMember->incrementNotifyUnreadCount([]);
    }

    function testSaveNewMembersFromCsvSuccessChangeLocalName()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = [
            'csv_test@email.com', 'aaa', 'first', 'last', 'on', 'off', null, 'jpn', 'ふぁーすと', 'ラスト'
        ];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->saveNewMembersFromCsv($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => false,
            'error_line_no' => 0,
            'error_msg'     => null,
            'success_count' => 1,
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataDifferenceTitle()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[0]['name'] = 'xxx';
        $csv_data[] = $this->getEmptyRowOnCsv();

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 0
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataDifferenceColumnCount()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        unset($csv_data[1][0]);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 0
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEmptyEmail()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataValidateEmail()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1][0] = 'aaa';

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataMemberIdEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1][0] = 'aaa@aaa.com';
        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataFirstNameEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $csv_data[1][0] = 'aaa@aaa.com';
        $csv_data[1][1] = 'aaa';

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataFirstNameOnlyRoman()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'aaa', 'ああああ',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataLastNameEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', '',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataLastNameOnlyRoman()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'あああ',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataAdminEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', '',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataAdminNotOnOrOff()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'aaaa',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEvaluateEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', ''];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEvaluateNotOnOrOff()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'aaaa'];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataLangCodeNotSupport()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'aaaaa',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataValidatePhone()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', 'aaaaaaa',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataValidateGender()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'aaaa',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataBirthDayAllOrNothing()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '', '',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataBirthYearValidate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', 'aaaaa', '1', '1',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataBirthMonthValidate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', 'aaaa', '1',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataBirthDayValidate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', 'aaaa',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataGroupAlignLeft()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', '', 'group3', '', '', '', '',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataGroupDuplicate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group1', '', '', '', '', '',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataCoachIdIsNotMemberId()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_id',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataRaterIdAlignLeft()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'coach_id', 'rater1', '', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataRaterIdNotIncludeMemberId()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'coach_id', 'member_id', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataRaterIdDuplicate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'coach_id', 'rater2', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEmailDuplicate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $csv_data[2] = $this->getEmptyRowOnCsv();
        $test_data_a = [
            'aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data_a);

        $test_data_b = [
            'aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[2] = Hash::merge($csv_data[2], $test_data_b);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataEmailAlreadyJoined()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();

        $test_data_a = [
            'from@email.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data_a);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataMemberIdDuplicate()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $csv_data[2] = $this->getEmptyRowOnCsv();

        $test_data_a = [
            'aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data_a);

        $test_data_b = [
            'bbb@bbb.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[2] = Hash::merge($csv_data[2], $test_data_b);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataMemberIdExists()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading();
        $csv_data[1] = $this->getEmptyRowOnCsv();
        $test_data_a = [
            'aaa@aaa.com', 'member_1', 'firstname', 'lastname', 'ON', 'ON',
        ];
        $csv_data[1] = Hash::merge($csv_data[1], $test_data_a);

        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataCoachIdExists()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();

        $csv_data[1] = ['aaa@aaa.com', 'member_id', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_1', 'rater1', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $csv_data[2] = ['aaax@aaa.com', 'member_2', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'not_exists_coach_id', 'rater1', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 3
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateNewMemberCsvDataRaterIdExists()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();

        $csv_data[1] = ['aaa@aaa.com', 'abc', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', '', 'member_1', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $csv_data[2] = ['aaax@aaa.com', 'member_2', 'firstname', 'lastname', 'ON', 'ON', '', 'jpn', 'localfirstname', 'locallastname', '000-0000-0000', 'male', '1999', '11', '11', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', '', 'abc', 'rater2', 'rater3', 'rater4', 'rater5', 'rater6', 'rater7',];
        $actual = $this->TeamMember->validateNewMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
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
        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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
        if (viaIsSet($actual['error_msg'])) {
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
        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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

        if (viaIsSet($actual['error_msg'])) {
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
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_1']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataRaterAlignLeftError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', '', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataRaterMemberIdError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'member_1', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataRaterDuplicateError()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'rater1', 'rater1']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
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
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'OFF', 'OFF', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'rater1', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'OFF', 'OFF', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'OFF', 'OFF', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'OFF', 'OFF', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
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
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'rater1', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
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
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'rater1', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);
        if (viaIsSet($actual['error_msg'])) {
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
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'xxxxxxxxxx', 'rater1', 'rater2']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
            unset($actual['error_msg']);
        }
        $excepted = [
            'error'         => true,
            'error_line_no' => 2
        ];
        $this->assertEquals($excepted, $actual);
    }

    function testValidateUpdateMemberCsvDataNotExistsRater()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[0] = $this->TeamMember->_getCsvHeading(false);
        $csv_data[1] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['from@email.com', 'firstname', 'lastname', 'member_1', 'ON', 'ON', 'ON', '', 'group1', 'group2', 'group3', 'group4', 'group5', 'group6', 'group7', 'member_2', 'rater1', 'rater2', 'xxxxxxxxxx']);
        $csv_data[2] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['test@aaa.com', 'firstname', 'lastname', 'member_2', 'ON', 'ON', 'ON']);
        $csv_data[3] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['to@email.com', 'firstname', 'lastname', 'member_3', 'ON', 'ON', 'ON']);
        $csv_data[4] = Hash::merge($this->getEmptyRowOnCsv(23),
                                   ['xxxxxxx@email.com', 'firstname', 'lastname', 'member_4', 'ON', 'ON', 'ON']);

        $actual = $this->TeamMember->validateUpdateMemberCsvData($csv_data);

        if (viaIsSet($actual['error_msg'])) {
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

    function setDefault()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->User->Email->current_team_id = $team_id;
        $this->TeamMember->User->Email->my_uid = $uid;
    }

}
