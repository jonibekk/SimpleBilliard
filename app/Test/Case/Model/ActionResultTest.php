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
        'app.action_result_file',
        'app.kr_progress_log',
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

    public function test_editActionWithTranslation_success(){
        // TODO check whether translation is deleted when editing an action
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

    function testValidatePost()
    {
        $this->_setDefault();

        $base = [
            'goal_id'       => 1,
            'name'          => 'test',
            'key_result_id' => 1,
        ];

        $this->ActionResult->validate = $this->ActionResult->postValidate;

        // フィールドなし
        $updateAction = $base;
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("Input is required.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 空文字エラー
        $updateAction = array_merge($base, ['key_result_current_value' => ""]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("Input is required.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 現在値が数値でないエラー
        $updateAction = array_merge($base, [
            'key_result_id' => 3,
            'key_result_current_value' => 'a'
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
    $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("Invalid value");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 存在しないKRのId
        $updateAction = array_merge($base, [
            'key_result_id' => 99999999999]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_id');
        $expectErrMsg = __("Please select");
        $this->assertTrue(in_array($expectErrMsg, $err));

        /* 単位が完了/未完了の場合 */
        $updateAction = array_merge($base, [
            'key_result_id' => 3,
            'key_result_current_value' => 0
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $this->assertEmpty($err);

        $updateAction = array_merge($base, [
            'key_result_id' => 3,
            'key_result_current_value' => 1
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $this->assertEmpty($err);

        $updateAction = array_merge($base, [
            'key_result_id' => 3,
            'key_result_current_value' => 0.1
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("Invalid Request.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        $updateAction = array_merge($base, [
            'key_result_id' => 3,
            'key_result_current_value' => 1.1
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("Invalid Request.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 進捗が変わらない場合はOK
        $updateAction = array_merge($base, [
            'key_result_id' => 1,
            'key_result_current_value' => 11
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $this->assertEmpty($err);


        // 現在値が減っていないか 進捗方向：増加
        $updateAction = array_merge($base, [
            'key_result_id' => 1,
            'key_result_current_value' => 10.999
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("You can not decrease current value.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 現在値が減っていないか 進捗方向：減少
        $updateAction = array_merge($base, [
            'key_result_id' => 2,
            'key_result_current_value' => 99.001
        ]);
        $this->ActionResult->set($updateAction);
        $this->ActionResult->validates();
        $err = Hash::get($this->ActionResult->validationErrors, 'key_result_current_value');
        $expectErrMsg = __("You can not increase current value.");
        $this->assertTrue(in_array($expectErrMsg, $err));
    }

    function testGetLatestAction()
    {
        $this->_setDefault();
        $krId = 1;
        $this->saveActionsWithKr($krId, [['created' => 1111], ['created' => 3333], ['created' => 1111]]);

        $latestAction = $this->ActionResult->getLatestAction($krId);
        $this->assertEqual(Hash::get($latestAction, 'ActionResult.created'), 3333);
    }

    function testGetLatestActionEmpty()
    {
        $this->_setDefault();
        $krId = 1;
        $this->saveActionsWithKr($krId, []);

        $latestAction = $this->ActionResult->getLatestAction($krId);
        $this->assertEqual($latestAction, []);
    }

    function saveActionsWithKr(int $krId, array $actions)
    {
        $this->ActionResult->KeyResult->deleteAll(['KeyResult.id >' => 0], false);
        $this->ActionResult->deleteAll(['ActionResult.id >' => 0], false);

        foreach($actions as $action) {
            $action = ['team_id' => 1, 'key_result_id' => $krId] + $action;
            $this->ActionResult->create();
            $this->ActionResult->save($action, false);
        }
    }

}
