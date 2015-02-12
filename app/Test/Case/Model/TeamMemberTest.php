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

    function testValidateNewMemberCsvDataNoTitle()
    {
        $this->setDefault();

        $csv_data = [];
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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
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

    function testValidateNewMemberCsvDataValidateEmail()
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

    function testValidateNewMemberCsvDataMemberIdEmpty()
    {
        $this->setDefault();

        $csv_data = [];
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'aaa',
            '',
        ];

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
        $csv_data[1] = [
            'aaa@aaa.com',
            'aaa',
            'ああああ',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'あああ',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'aaaa',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            ''
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'aaaa'
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'aaaaa',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            'aaaaaaa',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'aaaa',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            'aaaaa',
            '',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            'aaaa',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            'aaaa',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            '',
            'group3',
            '',
            '',
            '',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            'group1',
            '',
            '',
            '',
            '',
            '',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            'group2',
            'group3',
            'group4',
            'group5',
            'group6',
            'group7',
            'member_id',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            'group2',
            'group3',
            'group4',
            'group5',
            'group6',
            'group7',
            'coach_id',
            'rater1',
            '',
            'rater3',
            'rater4',
            'rater5',
            'rater6',
            'rater7',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            'group2',
            'group3',
            'group4',
            'group5',
            'group6',
            'group7',
            'coach_id',
            'member_id',
            'rater2',
            'rater3',
            'rater4',
            'rater5',
            'rater6',
            'rater7',
        ];

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
        $csv_data[] = $this->TeamMember->_getCsvHeading();
        $csv_data[] = $this->getEmptyRowOnCsv();
        $csv_data[1] = [
            'aaa@aaa.com',
            'member_id',
            'firstname',
            'lastname',
            'ON',
            'ON',
            '',
            'jpn',
            'localfirstname',
            'locallastname',
            '000-0000-0000',
            'male',
            '1999',
            '11',
            '11',
            'group1',
            'group2',
            'group3',
            'group4',
            'group5',
            'group6',
            'group7',
            'coach_id',
            'rater2',
            'rater2',
            'rater3',
            'rater4',
            'rater5',
            'rater6',
            'rater7',
        ];

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

    function setDefault()
    {
        $uid = 1;
        $team_id = 1;
        $this->TeamMember->current_team_id = $team_id;
        $this->TeamMember->my_uid = $uid;
        $this->TeamMember->User->Email->current_team_id = $team_id;
        $this->TeamMember->User->Email->my_uid = $uid;
    }

    function getEmptyRowOnCsv($colum_count = 29)
    {
        $row = [];
        for ($i = 0; $i >= $colum_count; $i++) {
            $row[] = null;
        }
        return $row;
    }
}
