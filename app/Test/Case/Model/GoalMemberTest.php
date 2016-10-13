<?php App::uses('GoalousTestCase', 'Test');
App::uses('Collaborator', 'Model');

/**
 * Collaborator Test Case
 *
 * @property Collaborator $Collaborator
 */
class CollaboratorTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.collaborator',
        'app.follower',
        'app.team',
        'app.evaluate_term',
        'app.user',
        'app.local_name',
        'app.goal',
        'app.goal_category',
        'app.approval_history',

        'app.team_member',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Collaborator = ClassRegistry::init('Collaborator');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Collaborator);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->_setDefault();
        $res = $this->Collaborator->add(1);
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
        $post_data = $this->Collaborator->save($data);
        $first_saved_id = $post_data['Collaborator']['id'];
        $post_data['Collaborator']['role'] = 'edited';
        $res = $this->Collaborator->edit($post_data);
        $secound_saved_id = $res['Collaborator']['id'];

        $this->assertEquals('edited', $res['Collaborator']['role']);
        $this->assertEquals($first_saved_id, $secound_saved_id);

    }

    function testGetOwnersStatus()
    {
        $this->_setDefault();
        $res = $this->Collaborator->getOwnersStatus(1);
        $this->assertNotEmpty($res);
    }

    function testGetCollabeGoalDetail()
    {
        $this->_setDefault();
        $team_id = 1;

        $current_term = $this->Collaborator->Goal->Team->EvaluateTerm->getCurrentTermData();

        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->Collaborator->User->save($params);
        $user_id = $this->Collaborator->User->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'photo_file_name'  => 'aa.png',
            'start_date'       => $current_term['end_date'] - 20,
            'end_date'         => $current_term['end_date'] - 10,
            'goal_category_id' => 1,
        ];
        $this->Collaborator->Goal->save($params);
        $current_goal_id = $this->Collaborator->Goal->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'photo_file_name'  => 'aa.png',
            'start_date'       => $current_term['end_date'] + 10,
            'end_date'         => $current_term['end_date'] + 20,
            'goal_category_id' => 1,
        ];
        $this->Collaborator->Goal->create();
        $this->Collaborator->Goal->save($params);
        $next_goal_id = $this->Collaborator->Goal->getLastInsertID();

        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $current_goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->Collaborator->create();
        $this->Collaborator->save($params);

        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $next_goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->Collaborator->create();
        $this->Collaborator->save($params);

        // 評価期間の絞り込み無し
        $goal_description = $this->Collaborator->getCollaboGoalDetail($team_id, $user_id, $approval_status);
        $ids = [];
        foreach ($goal_description as $v) {
            $ids[$v['Goal']['id']] = true;
        }
        $this->assertTrue(isset($ids[$current_goal_id]));
        $this->assertTrue(isset($ids[$next_goal_id]));

        // 今期で絞る
        $goal_description = $this->Collaborator->getCollaboGoalDetail($team_id, $user_id, $approval_status, true,
            EvaluateTerm::TYPE_CURRENT);
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
        $this->Collaborator->User->save($params);
        $user_id = $this->Collaborator->User->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'end_date'         => '1427813999',
            'photo_file_name'  => 'aa.png'
        ];
        $this->Collaborator->Goal->save($params);
        $goal_id = $this->Collaborator->Goal->getLastInsertID();

        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 0,
        ];
        $this->Collaborator->save($params);

        $goal_description = $this->Collaborator->getCollaboGoalDetail($team_id, $user_id, $approval_status, false);
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
        $this->Collaborator->save($params);
        $id = $this->Collaborator->getLastInsertID();
        $this->Collaborator->changeApprovalStatus($id, 1);

        $res = $this->Collaborator->findById($id);
        $this->assertEquals(1, $res['Collaborator']['approval_status']);
    }

    function testCountCollaboGoal()
    {
        $this->_setDefault();

        $team_id = 1;
        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->Collaborator->User->save($params);
        $user_id = $this->Collaborator->User->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'end_date'         => '1427813999',
            'photo_file_name'  => 'aa.png'
        ];
        $this->Collaborator->Goal->save($params);
        $goal_id = $this->Collaborator->Goal->getLastInsertID();

        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $approval_status);
        $this->assertEquals(0, $cnt);
    }

    function testCountCollaboGoalModifyStatus()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $goal_id = 777;
        $approval_status = 3;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 1,
        ];
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $approval_status);
        $this->assertEquals(0, $cnt);
    }

    function testCountCollaboRequestModify()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $goal_id = 1;
        $approval_status = 3;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
        ];
//        $this->Collaborator->deleteAll(['Collaborator.team_id' => $team_id], false);
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $approval_status);
        $this->assertEquals(0, $cnt);
    }

    function testCountCollaboPriorityZero()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $goal_id = 1;
        $approval_status = 0;
        $params = [
            'user_id'         => $user_id,
            'team_id'         => $team_id,
            'goal_id'         => $goal_id,
            'approval_status' => $approval_status,
            'type'            => 0,
            'priority'        => 0,
        ];
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $approval_status);
        $this->assertEquals(0, $cnt);
    }

    function testGetLeaderUidNotNull()
    {
        $this->_setDefault();

        $this->Collaborator->save(['goal_id' => 1, 'team_id' => 1, 'user_id' => 1, 'type' => Collaborator::TYPE_OWNER]);

        $actual = $this->Collaborator->getLeaderUid(1);
        $this->assertEquals(1, $actual);
    }

    function testGetLeaderUidNull()
    {
        $this->_setDefault();
        $actual = $this->Collaborator->getLeaderUid(111111);
        $this->assertEquals(null, $actual);
    }

    function testGetCollaboratorListByGoalId()
    {
        $this->_setDefault();
        $data = [
            'user_id' => 100,
            'goal_id' => 200,
            'team_id' => 1,
            'type'    => Collaborator::TYPE_COLLABORATOR
        ];
        $this->Collaborator->save($data);
        $actual = $this->Collaborator->getCollaboratorListByGoalId(200, Collaborator::TYPE_COLLABORATOR);
        $this->assertNotEmpty($actual);
    }

    function testGetCollaboratorByGoalId()
    {
        $this->_setDefault();

        $goal_id = 1;

        // ゴールに紐づくコラボレーター全て
        $res = $this->Collaborator->getCollaboratorByGoalId($goal_id);
        $this->assertNotEmpty($res);

        // limit 指定
        $res2 = $this->Collaborator->getCollaboratorByGoalId($goal_id, ['limit' => 1]);
        $this->assertCount(1, $res2);

        // limit + page 指定
        $res3 = $this->Collaborator->getCollaboratorByGoalId($goal_id, ['limit' => 1, 'page' => 2]);
        $this->assertCount(1, $res3);
        $this->assertNotEquals($res2[0]['User']['id'], $res3[0]['User']['id']);
    }

    function testGetCollaboratorOwnerTypeTrue()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 100;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => Collaborator::TYPE_OWNER
        ];
        $this->Collaborator->save($data);
        $res = $this->Collaborator->getCollaborator($team_id, $user_id, $goal_id);
        $this->assertCount(1, $res);
    }

    function testGetCollaboratorOwnerTypeFalse()
    {
        $this->_setDefault();

        $team_id = 1;
        $user_id = 1;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => Collaborator::TYPE_OWNER
        ];
        $this->Collaborator->save($data);
        $res = $this->Collaborator->getCollaborator($team_id, $user_id, $goal_id, false);
        $this->assertCount(0, $res);
    }

    function testGetCount()
    {
        $this->_setDefault();

        $this->Collaborator->create();
        $this->Collaborator->save(
            [
                'team_id' => 1,
                'user_id' => 1,
                'goal_id' => 1,
                'type'    => Collaborator::TYPE_OWNER
            ]);
        $this->Collaborator->create();
        $this->Collaborator->save(
            [
                'team_id' => 1,
                'user_id' => 2,
                'goal_id' => 1,
                'type'    => Collaborator::TYPE_COLLABORATOR
            ]);
        $this->Collaborator->create();
        $this->Collaborator->save(
            [
                'team_id' => 1,
                'user_id' => 3,
                'goal_id' => 1,
                'type'    => Collaborator::TYPE_COLLABORATOR
            ]);
        $this->Collaborator->create();
        $this->Collaborator->save(
            [
                'team_id' => 1,
                'user_id' => 3,
                'goal_id' => 2,
                'type'    => Collaborator::TYPE_COLLABORATOR
            ]);
        $now = time();
        $count = $this->Collaborator->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals(4, $count);

        $count = $this->Collaborator->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
                'type'  => Collaborator::TYPE_OWNER
            ]);
        $this->assertEquals(1, $count);

        $count = $this->Collaborator->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
                'type'  => Collaborator::TYPE_COLLABORATOR
            ]);
        $this->assertEquals(3, $count);

        $count = $this->Collaborator->getCount(
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
        $this->Collaborator->deleteAll(['Collaborator.user_id' => $user_id], false);
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
        $this->Collaborator->saveAll($prepare_data);
        $actual = $this->Collaborator->goalIdOrderByPriority($user_id, [4, 3, 5]);
        $expected = array(
            (int)5 => '5',
            (int)4 => '4',
            (int)3 => '3'
        );
        $this->assertEquals($expected, $actual);
        $actual = $this->Collaborator->goalIdOrderByPriority($user_id, [4, 3, 5], 'asc');
        $expected = array(
            (int)3 => '3',
            (int)4 => '4',
            (int)5 => '5'
        );
        $this->assertEquals($expected, $actual);
    }

    function _setDefault()
    {
        $this->Collaborator->current_team_id = 1;
        $this->Collaborator->my_uid = 1;
        $this->Collaborator->Goal->current_team_id = 1;
        $this->Collaborator->Goal->my_uid = 1;
        $this->Collaborator->Goal->Team->current_team_id = 1;
        $this->Collaborator->Goal->Team->my_uid = 1;
        $this->Collaborator->Goal->Team->EvaluateTerm->current_team_id = 1;
        $this->Collaborator->Goal->Team->EvaluateTerm->my_uid = 1;

        $this->Collaborator->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Collaborator->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->Collaborator->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);

    }

}
