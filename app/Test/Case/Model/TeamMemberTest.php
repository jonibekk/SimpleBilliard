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
}
