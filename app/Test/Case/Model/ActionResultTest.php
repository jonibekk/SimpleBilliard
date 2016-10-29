<?php App::uses('GoalousTestCase', 'Test');
App::uses('ActionResult', 'Model');

/**
 * ActionResult Test Case
 *
 * @property ActionResult $ActionResult
 */
class ActionResultTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',

        'app.team',
        'app.user',
        'app.email',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.attached_file',
        'app.action_result_file'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResult = ClassRegistry::init('ActionResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResult);

        parent::tearDown();
    }

    function testGetCount()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 101;

        // 自分
        $res = $this->ActionResult->getCount('me', null, null);
        $this->assertEquals(2, $res);

        // ユーザID指定
        $res = $this->ActionResult->getCount(102, null, null);
        $this->assertEquals(1, $res);
    }

    function testActionEdit()
    {
        $this->_setDefault();
        $before_save = [
            'ActionResult' => [
                'name'    => 'test',
                'team_id' => 1,
                'user_id' => 1,
            ]
        ];
        $save_data = $this->ActionResult->save($before_save);
        $save_data['photo_delete'][1] = 1;
        $res = $this->ActionResult->actionEdit($save_data);
        $this->assertTrue(!empty($res));
    }

    function testActionEditEmptyData()
    {
        $this->_setDefault();
        $res = $this->ActionResult->actionEdit([]);
        $this->assertFalse($res);
    }

    function testAddCompletedAction()
    {
        $this->_setDefault();
        $data = [
            'ActionResult' => [
                'name'          => 'test',
                'key_result_id' => 1
            ]
        ];
        $res = $this->ActionResult->addCompletedAction($data, 1);
        $this->assertTrue(!empty($res));
    }

    function testAddCompletedActionFail()
    {
        $this->_setDefault();
        $res = $this->ActionResult->addCompletedAction([], 1);
        $this->assertFalse($res);
    }

    function testGetCountByGoalId()
    {
        $this->_setDefault();
        $res = $this->ActionResult->getCountByGoalId(6);
        $this->assertEquals(1, $res);
    }

    function testGetWithAttachedFiles()
    {
        $this->_setDefault();
        $row = $this->ActionResult->getWithAttachedFiles(1);
        $this->assertArrayHasKey('ActionResultFile', $row);
    }

    function testActionEditWithFile()
    {
        $this->_setDefault();
        // 通常 edit
        $data = [
            'ActionResult' => [
                'id'   => 1,
                'name' => 'edit string',
            ]
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertTrue($res);
        $row = $this->ActionResult->findById(1);
        $this->assertEquals($row['ActionResult']['name'], $data['ActionResult']['name']);

        // 添付ファイルあり
        $this->ActionResult->ActionResultFile->AttachedFile = $this->getMockForModel('AttachedFile',
            array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->ActionResult->ActionResultFile->AttachedFile->expects($this->any())
                                                           ->method('updateRelatedFiles')
                                                           ->will($this->returnValue(true));
        $data = [
            'ActionResult' => [
                'id'   => 1,
                'name' => 'edit string2',
            ],
            'file_id'      => ['aaa', 'bbb']
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertTrue($res);
        $row = $this->ActionResult->findById(1);
        $this->assertEquals($row['ActionResult']['name'], $data['ActionResult']['name']);

        // rollback
        $this->ActionResult->ActionResultFile->AttachedFile = $this->getMockForModel('AttachedFile',
            array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->ActionResult->ActionResultFile->AttachedFile->expects($this->any())
                                                           ->method('updateRelatedFiles')
                                                           ->will($this->returnValue(false));
        $data = [
            'ActionResult' => [
                'id'   => 1,
                'name' => 'edit string3',
            ],
            'file_id'      => ['aaa', 'bbb']
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertFalse($res);
        $row = $this->ActionResult->findById(1);
        $this->assertNotEquals($row['ActionResult']['name'], $data['ActionResult']['name']);
    }

    function testGetUniqueUserCount()
    {
        $this->_setDefault();
        $now = time();
        $test_data = [
            ['goal_id' => 10, 'num' => 1, 'user_id' => 1],
            ['goal_id' => 20, 'num' => 1, 'user_id' => 2],
            ['goal_id' => 30, 'num' => 1, 'user_id' => 1],
        ];
        foreach ($test_data as $v) {
            for ($i = 0; $i < $v['num']; $i++) {
                $this->ActionResult->create();
                $this->ActionResult->save(
                    [
                        'team_id'       => 1,
                        'goal_id'       => $v['goal_id'],
                        'key_result_id' => null,
                        'user_id'       => $v['user_id'],
                        'name'          => 'test',
                        'type'          => ActionResult::TYPE_GOAL,
                        'created'       => $now,
                    ]);
            }
        }

        $count = $this->ActionResult->getUniqueUserCount(['start' => $now - HOUR, 'end' => $now + HOUR]);
        $this->assertEquals(2, $count);

        $count = $this->ActionResult->getUniqueUserCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1
        ]);
        $this->assertEquals(1, $count);

        $count = $this->ActionResult->getUniqueUserCount(['start' => $now + HOUR]);
        $this->assertEquals(0, $count);
    }

    function testGetGoalRanking()
    {
        $this->_setDefault();
        $now = time();
        $test_data = [
            ['goal_id' => 10, 'num' => 1],
            ['goal_id' => 20, 'num' => 3],
            ['goal_id' => 100, 'num' => 2],
        ];
        foreach ($test_data as $v) {
            for ($i = 0; $i < $v['num']; $i++) {
                $this->ActionResult->create();
                $this->ActionResult->save(
                    [
                        'team_id'       => 1,
                        'goal_id'       => $v['goal_id'],
                        'key_result_id' => null,
                        'user_id'       => 1,
                        'name'          => 'test',
                        'type'          => ActionResult::TYPE_GOAL,
                        'created'       => $now,
                    ]);
            }
        }

        $ranking = $this->ActionResult->getGoalRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals([20 => 3, 100 => 2, 10 => 1], $ranking);

        $ranking = $this->ActionResult->getGoalRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'limit' => 2
        ]);
        $this->assertEquals([20 => 3, 100 => 2], $ranking);

        $ranking = $this->ActionResult->getGoalRanking([
            'start'        => $now - HOUR,
            'end'          => $now + HOUR,
            'goal_user_id' => 100
        ]);
        $this->assertEquals([100 => 2], $ranking);

        $count = $this->ActionResult->getGoalRanking(['start' => $now + HOUR]);
        $this->assertEquals([], $count);
    }

    function testGetUserRanking()
    {
        $this->_setDefault();
        $now = time();
        $test_data = [
            ['goal_id' => 10, 'num' => 1, 'user_id' => 1],
            ['goal_id' => 20, 'num' => 3, 'user_id' => 2],
            ['goal_id' => 100, 'num' => 2, 'user_id' => 3],
        ];
        foreach ($test_data as $v) {
            for ($i = 0; $i < $v['num']; $i++) {
                $this->ActionResult->create();
                $this->ActionResult->save(
                    [
                        'team_id'       => 1,
                        'goal_id'       => $v['goal_id'],
                        'key_result_id' => null,
                        'user_id'       => $v['user_id'],
                        'name'          => 'test',
                        'type'          => ActionResult::TYPE_GOAL,
                        'created'       => $now,
                    ]);
            }
        }

        $ranking = $this->ActionResult->getUserRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals([2 => 3, 3 => 2, 1 => 1], $ranking);

        $ranking = $this->ActionResult->getUserRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'limit' => 2
        ]);
        $this->assertEquals([2 => 3, 3 => 2], $ranking);

        $ranking = $this->ActionResult->getUserRanking([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 3
        ]);
        $this->assertEquals([3 => 2], $ranking);

        $count = $this->ActionResult->getUserRanking(['start' => $now + HOUR]);
        $this->assertEquals([], $count);
    }

    function testGetCollaboGoalActionCount()
    {
        $this->_setDefault();
        $now = time();
        $test_data = [
            ['goal_id' => 1, 'num' => 1, 'user_id' => 1],
            ['goal_id' => 1, 'num' => 3, 'user_id' => 2],
            ['goal_id' => 100, 'num' => 2, 'user_id' => 1],
        ];
        foreach ($test_data as $v) {
            for ($i = 0; $i < $v['num']; $i++) {
                $this->ActionResult->create();
                $this->ActionResult->save(
                    [
                        'team_id'       => 1,
                        'goal_id'       => $v['goal_id'],
                        'key_result_id' => null,
                        'user_id'       => $v['user_id'],
                        'name'          => 'test',
                        'type'          => ActionResult::TYPE_GOAL,
                        'created'       => $now,
                    ]);
            }
        }

        $count = $this->ActionResult->getCollaboGoalActionCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(5, $count);

        $count = $this->ActionResult->getCollaboGoalActionCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1
        ]);
        $this->assertEquals(2, $count);

        $count = $this->ActionResult->getCollaboGoalActionCount(['start' => $now + HOUR]);
        $this->assertEquals(0, $count);
    }

    function testGetKrIdsByGoalId()
    {
        $this->_setDefault();
        $this->ActionResult->deleteAll(['ActionResult.user_id' => 1], false);
        $data = [
            'goal_id'       => 1,
            'user_id'       => 1,
            'team_id'       => 1,
            'key_result_id' => 100
        ];
        $this->ActionResult->create();
        $this->ActionResult->save($data);
        $expected = [
            (int)100 => '100'
        ];
        $actual = $this->ActionResult->getKrIdsByGoalId(1, 1);
        $this->assertEquals($expected, $actual);
    }

    function _setDefault()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 1;
    }

}
