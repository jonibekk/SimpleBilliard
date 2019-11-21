<?php App::uses('GoalousTestCase', 'Test');
App::uses('GoalMember', 'Model');

/**
 * GoalMember Test Case
 *
 * @property GoalMember $GoalMember
 */
class GoalMemberTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_member',
        'app.follower',
        'app.team',
        'app.term',
        'app.user',
        'app.local_name',
        'app.goal',
        'app.goal_category',
        'app.approval_history',
        'app.team_member',
        'app.key_result',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalMember = ClassRegistry::init('GoalMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalMember);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->_setDefault();
        $res = $this->GoalMember->add(1);
        $this->assertTrue(!empty($res));
    }

    function testEdit()
    {
        $this->_setDefault();
        $data = [
            'goal_id' => 1,
            'user_id' => 1,
            'team_id' => 1,
            'role'    => 'test'
        ];
        $post_data = $this->GoalMember->save($data);
        $first_saved_id = $post_data['GoalMember']['id'];
        $post_data['GoalMember']['role'] = 'edited';
        $res = $this->GoalMember->edit($post_data);
        $secound_saved_id = $res['GoalMember']['id'];

        $this->assertEquals('edited', $res['GoalMember']['role']);
        $this->assertEquals($first_saved_id, $secound_saved_id);

    }

    function testGetOwnersStatus()
    {
        $this->_setDefault();
        $res = $this->GoalMember->getOwnersStatus(1);
        $this->assertNotEmpty($res);
    }

    function testGetCollabeGoalDetail()
    {
        $this->_setDefault();
        $team_id = 1;

        $current_term = $this->GoalMember->Goal->Team->Term->getCurrentTermData();

        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->GoalMember->User->save($params);
        $user_id = $this->GoalMember->User->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'photo_file_name'  => 'aa.png',
            'start_date'       => AppUtil::dateBefore($current_term['end_date'],2),
            'end_date'         => AppUtil::dateBefore($current_term['end_date'],1),
        ];

        $this->GoalMember->Goal->save($params);
        $current_goal_id = $this->GoalMember->Goal->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'photo_file_name'  => 'aa.png',
            'start_date'       => AppUtil::dateAfter($current_term['end_date'],1),
            'end_date'         => AppUtil::dateAfter($current_term['end_date'],2),
            'goal_category_id' => 1,
        ];

        $this->GoalMember->Goal->create();
        $this->GoalMember->Goal->save($params);
        $next_goal_id = $this->GoalMember->Goal->getLastInsertID();

        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $current_goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->GoalMember->create();
        $this->GoalMember->save($params);

        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $next_goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->GoalMember->create();
        $this->GoalMember->save($params);

        // 評価期間の絞り込み無し
        $goal_description = $this->GoalMember->getCollaboGoalDetail($team_id, $user_id, $approval_status);
        $ids = [];
        foreach ($goal_description as $v) {
            $ids[$v['Goal']['id']] = true;
        }
        $this->assertTrue(isset($ids[$current_goal_id]));
        $this->assertTrue(isset($ids[$next_goal_id]));

        // 今期で絞る
        $goal_description = $this->GoalMember->getCollaboGoalDetail($team_id, $user_id, $approval_status, true,
            Term::TYPE_CURRENT);
        $ids = [];
        foreach ($goal_description as $v) {
            $ids[$v['Goal']['id']] = true;
        }
        $this->assertTrue(isset($ids[$current_goal_id]));
        $this->assertFalse(isset($ids[$next_goal_id]));

    }

    function testGetCollabeGoalDetailExcludePriorityZero()
    {
        $this->_setDefault();

        $team_id = 1;

        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->GoalMember->User->save($params);
        $user_id = $this->GoalMember->User->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'end_date'         => '1427813999',
            'photo_file_name'  => 'aa.png'
        ];
        $this->GoalMember->Goal->save($params);
        $goal_id = $this->GoalMember->Goal->getLastInsertID();

        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 0,
        ];
        $this->GoalMember->save($params);

        $goal_description = $this->GoalMember->getCollaboGoalDetail($team_id, $user_id, $approval_status, false);
        $this->assertEmpty($goal_description);
    }

    function testChangeApprovalStatus()
    {
        $this->_setDefault();

        $user_id = 1;
        $team_id = 1;
        $goal_id = 999;
        $approval_status = 0;

        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
        ];
        $this->GoalMember->save($params);
        $id = $this->GoalMember->getLastInsertID();
        $this->GoalMember->changeApprovalStatus($id, 1);

        $res = $this->GoalMember->findById($id);
        $this->assertEquals(1, $res['GoalMember']['approval_status']);
    }

    function testGetLeaderUidNotNull()
    {
        $this->_setDefault();

        $this->GoalMember->save(['goal_id' => 1, 'team_id' => 1, 'user_id' => 1, 'type' => GoalMember::TYPE_OWNER]);

        $actual = $this->GoalMember->getLeaderUid(1);
        $this->assertEquals(1, $actual);
    }

    function testGetLeaderUidNull()
    {
        $this->_setDefault();
        $actual = $this->GoalMember->getLeaderUid(111111);
        $this->assertEquals(null, $actual);
    }

    function testFindActiveByGoalId()
    {
        $this->_setDefault();
        $data = [
            'user_id' => 100,
            'goal_id' => 200,
            'team_id' => 1,
            'type'    => GoalMember::TYPE_COLLABORATOR
        ];
        $this->GoalMember->save($data);
        $userData = [
            'id'         => 100,
            'active_flg' => true
        ];
        $this->GoalMember->User->save($userData, false);
        $teamMemberData = [
            'team_id'    => 1,
            'user_id'    => 100,
            'status' => TeamMember::USER_STATUS_ACTIVE
        ];
        $this->GoalMember->User->TeamMember->save($teamMemberData, false);
        $actual = $this->GoalMember->findActiveByGoalId(200, GoalMember::TYPE_COLLABORATOR);
        $this->assertNotEmpty($actual);
    }

    function testGetGoalMemberByGoalId()
    {
        $this->_setDefault();

        $goal_id = 1;

        // ゴールに紐づくコラボレーター全て
        $res = $this->GoalMember->getGoalMemberByGoalId($goal_id);
        $this->assertNotEmpty($res);

        // limit 指定
        $res2 = $this->GoalMember->getGoalMemberByGoalId($goal_id, ['limit' => 1]);
        $this->assertCount(1, $res2);

        // limit + page 指定
        $res3 = $this->GoalMember->getGoalMemberByGoalId($goal_id, ['limit' => 1, 'page' => 2]);
        $this->assertCount(1, $res3);
        $this->assertNotEquals($res2[0]['User']['id'], $res3[0]['User']['id']);
    }

    function testGetGoalMemberOwnerTypeTrue()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 100;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => GoalMember::TYPE_OWNER
        ];
        $this->GoalMember->save($data);
        $res = $this->GoalMember->getGoalMember($team_id, $user_id, $goal_id);
        $this->assertCount(1, $res);
    }

    function testGetGoalMemberOwnerTypeFalse()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => GoalMember::TYPE_OWNER
        ];
        $this->GoalMember->save($data);
        $res = $this->GoalMember->getGoalMember($team_id, $user_id, $goal_id, false);
        $this->assertCount(0, $res);
    }

    function testGetCount()
    {
        $this->_setDefault();

        $this->GoalMember->create();
        $this->GoalMember->save(
            [
                'team_id' => 1,
                'user_id' => 1,
                'goal_id' => 1,
                'type'    => GoalMember::TYPE_OWNER
            ]);
        $this->GoalMember->create();
        $this->GoalMember->save(
            [
                'team_id' => 1,
                'user_id' => 2,
                'goal_id' => 1,
                'type'    => GoalMember::TYPE_COLLABORATOR
            ]);
        $this->GoalMember->create();
        $this->GoalMember->save(
            [
                'team_id' => 1,
                'user_id' => 3,
                'goal_id' => 1,
                'type'    => GoalMember::TYPE_COLLABORATOR
            ]);
        $this->GoalMember->create();
        $this->GoalMember->save(
            [
                'team_id' => 1,
                'user_id' => 3,
                'goal_id' => 2,
                'type'    => GoalMember::TYPE_COLLABORATOR
            ]);
        $now = time();
        $count = $this->GoalMember->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals(4, $count);

        $count = $this->GoalMember->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
                'type'  => GoalMember::TYPE_OWNER
            ]);
        $this->assertEquals(1, $count);

        $count = $this->GoalMember->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
                'type'  => GoalMember::TYPE_COLLABORATOR
            ]);
        $this->assertEquals(3, $count);

        $count = $this->GoalMember->getCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'user_id' => 3,
            ]);
        $this->assertEquals(2, $count);
    }

    function testGoalIdOrderByPriority()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $this->GoalMember->deleteAll(['GoalMember.user_id' => $user_id], false);
        $prepare_data = [
            [
                'user_id'  => $user_id,
                'team_id'  => $team_id,
                'goal_id'  => 5,
                'priority' => 5,
            ],
            [
                'user_id'  => $user_id,
                'team_id'  => $team_id,
                'goal_id'  => 4,
                'priority' => 4,
            ],
            [
                'user_id'  => $user_id,
                'team_id'  => $team_id,
                'goal_id'  => 3,
                'priority' => 3,
            ],
        ];
        $this->GoalMember->saveAll($prepare_data);
        $actual = $this->GoalMember->goalIdOrderByPriority($user_id, [4, 3, 5]);
        $expected = array(
            (int)5 => '5',
            (int)4 => '4',
            (int)3 => '3'
        );
        $this->assertEquals($expected, $actual);
        $actual = $this->GoalMember->goalIdOrderByPriority($user_id, [4, 3, 5], 'asc');
        $expected = array(
            (int)3 => '3',
            (int)4 => '4',
            (int)5 => '5'
        );
        $this->assertEquals($expected, $actual);
    }

    function testFindAllMemberUserIds()
    {
        $this->_setDefault();
        $this->_saveActiveMembersWithGoal([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $res = $this->GoalMember->findAllMemberUserIds(1);
        $this->assertEqual(array_values($res), [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
    }

    function testFindAllMemberUserIdsEmpty()
    {
        $this->_setDefault();
        $this->_saveActiveMembersWithGoal([]);
        $res = $this->GoalMember->findAllMemberUserIds(1);
        $this->assertEqual($res, []);
    }

    function _saveActiveMembersWithGoal($userIds)
    {
        $this->GoalMember->deleteAll(['GoalMember.id >' => 0], false);
        $this->GoalMember->Goal->deleteAll(['Goal.id >' => 0], false);
        $this->GoalMember->User->deleteAll(['User.id >' => 0], false);
        $this->GoalMember->Team->TeamMember->deleteAll(['TeamMember.id >' => 0], false);

        $this->GoalMember->Goal->save(['id' => 1, 'team_id' => 1], false);
        foreach ($userIds as $userId) {
            $this->GoalMember->Team->TeamMember->create();
            $this->GoalMember->Team->TeamMember->save(['team_id' => 1, 'user_id' => $userId, 'status' => TeamMember::USER_STATUS_ACTIVE],
                false);
            $this->GoalMember->User->create();
            $this->GoalMember->User->save(['id' => $userId, 'team_id' => 1, 'active_flg' => true], false);
            $this->GoalMember->create();
            $this->GoalMember->save(['team_id' => 1, 'goal_id' => 1, 'user_id' => $userId], false);
        }
    }

    function testFindGoalPriorities()
    {
        $this->_setDefault();
        $expected = [
            (int)1 => '3',
            (int)7 => '3'
        ];
        $actual = $this->GoalMember->findGoalPriorities(1, '1980-01-01', '2020-01-01');
        $this->assertEquals($expected, $actual);
    }

    /**
     * ゴール重要度の取得時にリーダーとコラボの両方が含まれるか？
     */
    function testFindGoalPrioritiesContainCollabo()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $this->createGoalKrs(Term::TYPE_CURRENT, [0], 1, 1, GoalMember::TYPE_OWNER);
        $this->createGoalKrs(Term::TYPE_CURRENT, [0], 1, 1, GoalMember::TYPE_COLLABORATOR);
        $term = $this->Term->getCurrentTermData();

        $ret = $this->GoalMember->findGoalPriorities(1, $term['start_date'], $term['end_date']);
        $this->assertCount(2, $ret);
    }

    /**
     * @group getCollaborationGoalIds
     */
    public function testGetCollaborationGoalIds()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();

        $goalIds = [];
        $goal_count = rand(0, 2);
        for ($i = 1; $i <= $goal_count; $i++) {
            $goalIds[] = $this->createGoalKrs(Term::TYPE_CURRENT, [0], 1, 1, GoalMember::TYPE_COLLABORATOR);
        }
        $actual = $this->GoalMember->getCollaborationGoalIds($goalIds, 1);

        $this->assertSame($goalIds, $actual);
    }

    function _setDefault()
    {
        $this->GoalMember->current_team_id = 1;
        $this->GoalMember->my_uid = 1;
        $this->GoalMember->Goal->current_team_id = 1;
        $this->GoalMember->Goal->my_uid = 1;
        $this->GoalMember->Goal->Team->current_team_id = 1;
        $this->GoalMember->Goal->Team->my_uid = 1;
        $this->GoalMember->Goal->Team->Term->current_team_id = 1;
        $this->GoalMember->Goal->Team->Term->my_uid = 1;

        $this->GoalMember->Goal->Team->Term->addTermData(Term::TYPE_CURRENT);
        $this->GoalMember->Goal->Team->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->GoalMember->Goal->Team->Term->addTermData(Term::TYPE_NEXT);

    }

}
