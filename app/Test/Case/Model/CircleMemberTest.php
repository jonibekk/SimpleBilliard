<?php App::uses('GoalousTestCase', 'Test');
App::uses('CircleMember', 'Model');

/**
 * CircleMember Test Case
 *
 * @property CircleMember $CircleMember
 * @property TeamMember   $TeamMember
 */
class CircleMemberTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.circle_member',
        'app.circle',
        'app.circle_pin',
        'app.team',
        'app.user',
        'app.team_member',
        'app.group',
        'app.local_name',
        'app.member_group',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CircleMember);

        parent::tearDown();
    }

    function _setDefault($uid, $team_id)
    {
        $this->CircleMember->my_uid = $uid;
        $this->CircleMember->current_team_id = $team_id;
        $this->CircleMember->Circle->my_uid = $uid;
        $this->CircleMember->Circle->current_team_id = $team_id;
        $this->CircleMember->Team->TeamMember->my_uid = $uid;
        $this->CircleMember->Team->TeamMember->current_team_id = $team_id;

    }

    function testGetMemberList()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getMemberList(1, true);
        $this->CircleMember->getMemberList(1, true, false);
    }

    function testGetAdminMemberList()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getAdminMemberList(1);
        $this->CircleMember->getAdminMemberList(1, true);
    }

    function testGetMemberListNotWithMe()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getMemberList(1, false);
    }

    public function testGetCircleInitMemberSelect2()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->getCircleInitMemberSelect2(1, true);
    }

    public function testIncrementUnreadCount()
    {
        $uid = 1;
        $team_id = 1;
        $this->_setDefault($uid, $team_id);
        $this->CircleMember->incrementUnreadCount([]);
    }

    public function testUpdateModified()
    {
        $this->CircleMember->my_uid = 1;
        $circle_list = [1, 2];
        $this->CircleMember->current_team_id = 1;
        $now = time();
        $this->CircleMember->updateModified($circle_list);
        $res = $this->CircleMember->find('all', ['conditions' => ['CircleMember.circle_id' => $circle_list]]);
        $this->assertGreaterThanOrEqual($now * 2,
            $res[0]['CircleMember']['modified'] + $res[1]['CircleMember']['modified']);
    }

    public function testUpdateModifiedIfEmpty()
    {
        $circle_list = [];
        $res = $this->CircleMember->updateModified($circle_list);
        $this->assertFalse($res);
    }

    public function testUnjoinMember()
    {
        $circle_id = '1';
        $this->CircleMember->my_uid = 1;
        $this->CircleMember->current_team_id = 1;

        // 既存のメンバー
        $users = $this->CircleMember->find(
            'list',
            [
                'fields'     => [
                    'CircleMember.user_id',
                    'CircleMember.user_id'
                ],
                'conditions' => [
                    'CircleMember.circle_id' => $circle_id
                ],
            ]
        );
        $this->assertTrue(isset($users[$this->CircleMember->my_uid]));

        // メンバーから外す
        $res = $this->CircleMember->unjoinMember($circle_id);
        $this->assertTrue($res);

        // 自身が消えているか確認
        $users = $this->CircleMember->find(
            'list',
            [
                'fields'     => [
                    'CircleMember.user_id',
                    'CircleMember.user_id'
                ],
                'conditions' => [
                    'CircleMember.circle_id' => $circle_id
                ],
            ]
        );
        $this->assertFalse(isset($users[$this->CircleMember->my_uid]));
    }

    public function testUnjoinMemberWithUserId()
    {
        $this->CircleMember->current_team_id = 1;
        $this->CircleMember->my_uid = 1;
        $circle_id = '1';
        $user_id = '2';

        // 既存のメンバー
        $users = $this->CircleMember->find(
            'list',
            [
                'fields'     => [
                    'CircleMember.user_id',
                    'CircleMember.user_id'
                ],
                'conditions' => [
                    'CircleMember.circle_id' => $circle_id
                ],
            ]
        );
        $this->assertTrue(isset($users[$user_id]));

        // ユーザーID指定でサークルから削除
        $res = $this->CircleMember->unjoinMember($circle_id, $user_id);
        $this->assertTrue($res);

        // 指定ユーザーが消えているか確認
        $users = $this->CircleMember->find(
            'list',
            [
                'fields'     => [
                    'CircleMember.user_id',
                    'CircleMember.user_id'
                ],
                'conditions' => [
                    'CircleMember.circle_id' => $circle_id
                ],
            ]
        );
        $this->assertFalse(isset($users[$user_id]));
        $this->assertTrue(isset($users[$this->CircleMember->my_uid]));
    }

    public function testUnjoinMemberWithInvalidUser()
    {
        $this->CircleMember->current_team_id = 1;
        $this->CircleMember->my_uid = 1;
        $circle_id = '1';
        $user_id = '9999';

        // ユーザーID指定でサークルから削除
        $res = $this->CircleMember->unjoinMember($circle_id, $user_id);
        $this->assertEmpty($res);
    }

    public function testShowHideStats()
    {
        $this->_setDefault(1, 1);
        $result = $this->CircleMember->getShowHideStatus(1, 1);
        $this->assertTrue($result);
    }

    public function testGetMyCircle()
    {
        $this->CircleMember->my_uid = 1;
        $this->CircleMember->current_team_id = 1;

        $result = $this->CircleMember->getMyCircle();
        $this->assertNotEmpty($result);
        $result = $this->CircleMember->getMyCircle();
        $this->assertNotEmpty($result);
        // 先頭はチーム全体サークル
        $this->assertEquals(1, $result[0]['Circle']['team_all_flg']);

        $result = $this->CircleMember->getMyCircle(['circle_created_start' => 500]);
        foreach ($result as $circle) {
            $this->assertGreaterThanOrEqual(500, $circle['Circle']['created']);
        }

        $result = $this->CircleMember->getMyCircle(['circle_created_end' => 500]);
        foreach ($result as $circle) {
            $this->assertLessThan(500, $circle['Circle']['created']);
        }

        $result = $this->CircleMember->getMyCircle(['order' => ['Circle.created desc']]);
        $prev_created = PHP_INT_MAX;
        foreach ($result as $circle) {
            $this->assertLessThanOrEqual($prev_created, $circle['Circle']['created']);
            $prev_created = $circle['Circle']['created'];
        }

    }

    public function testEditAdminStatus()
    {
        $this->CircleMember->current_team_id = 1;
        $circle_id = 1;
        $user_id = 2;

        // 管理者でないことを確認
        $this->assertEmpty($this->CircleMember->isAdmin($user_id, $circle_id));

        // 管理者に変更に変更
        $res = $this->CircleMember->editAdminStatus($circle_id, $user_id, 1);
        $this->assertTrue($res);
        $this->assertEquals(1, $this->CircleMember->getAffectedRows());
        $this->assertNotEmpty($this->CircleMember->isAdmin($user_id, $circle_id));

        // 通常ユーザーに変更に変更
        $res = $this->CircleMember->editAdminStatus($circle_id, $user_id, 0);
        $this->assertTrue($res);
        $this->assertEquals(1, $this->CircleMember->getAffectedRows());
        $this->assertEmpty($this->CircleMember->isAdmin($user_id, $circle_id));
    }

    function testGetActiveMemberCount()
    {
        $this->CircleMember->current_team_id = 1;
        $this->CircleMember->my_uid = 1;
        $this->CircleMember->Team->current_team_id = 1;
        $this->CircleMember->Team->my_uid = 1;
        $this->CircleMember->Team->TeamMember->current_team_id = 1;
        $this->CircleMember->Team->TeamMember->my_uid = 1;

        $res = $this->CircleMember->getActiveMemberCount(1);
        $this->assertEquals(3, $res);
    }

    function testGetActiveMemberCountList()
    {
        $this->CircleMember->current_team_id = 1;
        $this->CircleMember->my_uid = 1;
        $this->CircleMember->Circle->current_team_id = 1;
        $this->CircleMember->Circle->my_uid = 1;
        $this->CircleMember->User->TeamMember->current_team_id = 1;
        $this->CircleMember->User->TeamMember->my_uid = 1;

        $count_list = $this->CircleMember->getActiveMemberCountList(array_keys($this->CircleMember->Circle->getList()));
        foreach ($count_list as $id => $count) {
            $this->assertEquals($this->CircleMember->getActiveMemberCount($id), $count);
        }
    }

    function testGetNonCircleMemberSelect2()
    {
        $this->_setDefault(1, 1);
        $data = [
            'Circle' => [
                'name'       => 'test',
                'public_flg' => true,
                'team_id'    => 1,
            ]
        ];
        $this->CircleMember->Circle->save($data);
        $res = $this->CircleMember->getNonCircleMemberSelect2($this->CircleMember->Circle->getLastInsertID(), 'test');
        $this->assertNotEmpty($res);

        $res = $this->CircleMember->getNonCircleMemberSelect2(3, 'first', 10);
        $this->assertNotEmpty($res['results']);
        $user1_found = false;
        foreach ($res['results'] as $v) {
            if ($v['id'] == 'user_1') {
                $user1_found = true;
            }
        }
        $this->assertFalse($user1_found);

        $res = $this->CircleMember->getNonCircleMemberSelect2(5, 'first', 10);
        $this->assertNotEmpty($res['results']);
        $user1_found = false;
        foreach ($res['results'] as $v) {
            if ($v['id'] == 'user_1') {
                $user1_found = true;
            }
        }
        $this->assertTrue($user1_found);

        $res = $this->CircleMember->getNonCircleMemberSelect2(3, 'first', 10, true);
        $this->assertNotEmpty($res['results']);
        $user1_found = false;
        $group_found = false;
        foreach ($res['results'] as $v) {
            if (strpos($v['id'], 'group_') === 0) {
                $group_found = true;
                $this->assertNotEmpty($v['users']);
                foreach ($v['users'] as $user) {
                    $this->assertNotEquals($this->CircleMember->my_uid, $user['id']);
                    if ($user['id'] == 'user_1') {
                        $user1_found = true;
                    }
                }
            }
        }
        $this->assertTrue($group_found);
        $this->assertFalse($user1_found);

        $res = $this->CircleMember->getNonCircleMemberSelect2(5, 'first', 10, true);
        $this->assertNotEmpty($res['results']);
        $user1_found = false;
        $group_found = false;
        foreach ($res['results'] as $v) {
            if (strpos($v['id'], 'group_') === 0) {
                $group_found = true;
                $this->assertNotEmpty($v['users']);
                foreach ($v['users'] as $user) {
                    $this->assertNotEquals($this->CircleMember->my_uid, $user['id']);
                    if ($user['id'] == 'user_1') {
                        $user1_found = true;
                    }
                }
            }
        }
        $this->assertTrue($group_found);
        $this->assertTrue($user1_found);
    }

    function testUpdateUnreadCount()
    {
        $this->_setDefault(1, 1);
        $res = $this->CircleMember->updateUnreadCount(1);
        $this->assertTrue($res);
    }

    function testCircleStatusToggle()
    {
        $this->_setDefault(1, 1);
        $res = $this->CircleMember->circleStatusToggle(1, 1);
        $this->assertTrue($res);
    }

    function testEditCircleSetting()
    {
        $this->_setDefault(1, 1);
        $circle_member = $this->CircleMember->isBelong(1, 1);

        $res = $this->CircleMember->editCircleSetting(1, 1, [
            'CircleMember' => [
                'show_for_all_feed_flg' => 1,
                'get_notification_flg'  => 1,
            ]
        ]);
        $this->assertTrue($res);
        $row = $this->CircleMember->findById($circle_member['CircleMember']['id']);
        $this->assertEquals(1, $row['CircleMember']['show_for_all_feed_flg']);
        $this->assertEquals(1, $row['CircleMember']['get_notification_flg']);

        $res = $this->CircleMember->editCircleSetting(1, 1, [
            'CircleMember' => [
                'show_for_all_feed_flg' => 1,
                'get_notification_flg'  => 0,
            ]
        ]);
        $this->assertTrue($res);
        $row = $this->CircleMember->findById($circle_member['CircleMember']['id']);
        $this->assertEquals(1, $row['CircleMember']['show_for_all_feed_flg']);
        $this->assertEquals(0, $row['CircleMember']['get_notification_flg']);

        $res = $this->CircleMember->editCircleSetting(1, 1, [
            'CircleMember' => [
                'show_for_all_feed_flg' => 0,
            ]
        ]);
        $this->assertTrue($res);
        $row = $this->CircleMember->findById($circle_member['CircleMember']['id']);
        $this->assertEquals(0, $row['CircleMember']['show_for_all_feed_flg']);
        $this->assertEquals(0, $row['CircleMember']['get_notification_flg']);

        $res = $this->CircleMember->editCircleSetting(1, 1, [
            'CircleMember' => [
                'circle_id' => 0,
            ]
        ]);
        $this->assertFalse($res);
    }

    function testGetNotificationEnableUserList()
    {
        $this->_setDefault(1, 1);
        $rows = $this->CircleMember->getNotificationEnableUserList(1);
        $this->assertNotEmpty($rows);
        $user_id = current($rows);
        $this->CircleMember->updateAll(['get_notification_flg' => 0], [
            'CircleMember.user_id'   => $user_id,
            'CircleMember.team_id'   => 1,
            'CircleMember.circle_id' => 1,
        ]);
        $rows = $this->CircleMember->getNotificationEnableUserList(1);
        $this->assertFalse(isset($rows[$user_id]));
    }

    function testIsJoinedForSetupBy()
    {
        $this->_setDefault(1, 1);

        // In case that user don't join any circle
        $this->CircleMember->deleteAll([
            'CircleMember.user_id' => $this->CircleMember->my_uid
        ]);
        $res = $this->CircleMember->isJoinedForSetupBy($this->CircleMember->my_uid);
        $this->assertFalse($res);

        // In case that user join only team-all-circle
        $this->CircleMember->Circle->saveAll([
            [
                'id'           => 1,
                'name'         => 'circle1',
                'description'  => 'test',
                'public_flg'   => true,
                'team_id'      => 1,
                'team_all_flg' => true
            ],
            [
                'id'          => 2,
                'name'        => 'circle2',
                'description' => 'test',
                'public_flg'  => true,
                'team_id'     => 1
            ],
        ]);
        $this->CircleMember->save([
            'CircleMember' => [
                'id'        => 1,
                'user_id'   => 1,
                'circle_id' => 1,
                'team_id'   => 1,
            ]
        ]);
        $res = $this->CircleMember->isJoinedForSetupBy($this->CircleMember->my_uid);
        $this->assertFalse($res);

        // In case that user join or create more one circle
        $this->CircleMember->save([
            'CircleMember' => [
                'id'        => 2,
                'user_id'   => 1,
                'circle_id' => 2,
                'team_id'   => 1,
            ]
        ]);
        $res = $this->CircleMember->isJoinedForSetupBy($this->CircleMember->my_uid);
        $this->assertTrue($res);

    }

    public function test_getCircleMember_success()
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleId = 1;
        $userId = 1;

        $res = $CircleMember->getCircleMember($circleId, $userId);

        $this->assertNotEmpty($res);
        $this->assertEquals($circleId, $res['circle_id']);
        $this->assertEquals($userId, $res['user_id']);
    }

    public function test_getEmptyCircleMember_success()
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleId = 1232323;
        $userId = 1141414;

        $res = $CircleMember->getCircleMember($circleId, $userId);

        $this->assertEmpty($res);
    }

    public function test_getMemberCount_success()
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $this->assertEquals(3, $CircleMember->getMemberCount(1));
        $this->assertEquals(3, $CircleMember->getMemberCount(1, true));
    }

    public function test_countEachCircle()
    {
        // Normal
        $res = $this->CircleMember->countEachCircle([1, 3, 4]);
        $this->assertEquals($res, [1 => 3, 3 => 2, 4 => 2]);

        // Specify circle doesn't exist
        $res = $this->CircleMember->countEachCircle([1, 3, 99, 4]);
        $this->assertEquals($res, [1 => 3, 3 => 2, 99 => 0, 4 => 2]);

        // Count after inactivate uesr
        $this->TeamMember->inactivate(2);
        $res = $this->CircleMember->countEachCircle([1, 3, 4]);
        $this->assertEquals($res, [1 => 2, 3 => 1, 4 => 1]);

        // Count after delete circle_member
        $this->CircleMember->softDeleteAll(['circle_id' => 1, 'user_id' => 12], false);
        $res = $this->CircleMember->countEachCircle([1, 3, 4]);
        $this->assertEquals($res, [1 => 1, 3 => 1, 4 => 1]);
    }

    public function test_incrementUnreadCount_success()
    {
        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $unreadCount = $CircleMember->getUnreadCount(1, 1);
        $CircleMember->incrementUnreadCount($circleId, false, $teamId);
        $newUnreadCount = $CircleMember->getUnreadCount(1, 1);
        $this->assertEquals($unreadCount + 1, $newUnreadCount);

        $CircleMember->incrementUnreadCount($circleId, true, $teamId, $userId);
        $this->assertEquals($newUnreadCount, $CircleMember->getUnreadCount(1, 1));
    }
}
