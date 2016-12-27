<?php App::uses('GoalousTestCase', 'Test');
App::uses('KeyResult', 'Model');

/**
 * KeyResult Test Case
 *
 * @property KeyResult $KeyResult
 */
class KeyResultTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',
        'app.evaluate_term',
        'app.key_result',
        'app.goal',
        'app.goal_category',
        'app.goal_member',
        'app.user',
        'app.team',
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
        $this->KeyResult = ClassRegistry::init('KeyResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KeyResult);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->setDefault();
        try {
            $this->KeyResult->add([], 1);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
        unset($e);
        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => '2014/7/7',
                'end_date'   => '2014/11/7',
                'name'       => 'test',
            ]
        ];
        $res = $this->KeyResult->add($data, 8);
        $this->assertTrue($res);

        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => '2014/7/7',
                'end_date'   => '2014/11/7',
                'name'       => null,
            ]
        ];
        try {
            $this->KeyResult->add($data, 1);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testGetKeyResults()
    {
        $this->setDefault();

        // 通常呼び出し
        $krs = $this->KeyResult->getKeyResults(1);
        $this->assertNotEmpty($krs);

        $krs = $this->KeyResult->getKeyResults(1, 'all', true);
        $this->assertNotEmpty($krs);

        // limit 指定
        $krs2 = $this->KeyResult->getKeyResults(1, 'all', false, [
            'limit' => 1,
        ]);
        $this->assertCount(1, $krs2);

        // limit + page 指定
        $krs3 = $this->KeyResult->getKeyResults(1, 'all', false, [
            'limit' => 1,
            'page'  => 2
        ]);
        $this->assertCount(1, $krs3);
        $this->assertNotEquals($krs2[0]['KeyResult']['id'], $krs3[0]['KeyResult']['id']);

    }

    function testIsPermitted()
    {
        $this->setDefault();
        $goal = [
            'user_id'          => 1,
            'team_id'          => 1,
            'name'             => 'test',
            'start_date'       => time(),
            'end_date'         => time(),
            'goal_category_id' => 1,

        ];
        $this->KeyResult->Goal->create();
        $this->KeyResult->Goal->save($goal);
        $goal_id = $this->KeyResult->Goal->getLastInsertID();
        $goalMember = [
            'goal_id' => $goal_id,
            'user_id' => 1,
            'team_id' => 1,
        ];
        $this->KeyResult->Goal->GoalMember->create();
        $this->KeyResult->Goal->GoalMember->save($goalMember);
        $kr = [
            'user_id'      => 1,
            'team_id'      => 1,
            'name'         => 'test',
            'goal_id'      => $goal_id,
            'start_date'   => time(),
            'end_date'     => time(),
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
        ];
        $this->KeyResult->create();
        $this->KeyResult->save($kr);
        $kr_id = $this->KeyResult->getLastInsertID();
        $res = $this->KeyResult->isPermitted($kr_id);
        $this->assertTrue($res, "コラボしている");

        $res = $this->KeyResult->isPermitted(9999999);
        $this->assertFalse($res, "存在しないKR");

        $kr = [
            'user_id'      => 1,
            'team_id'      => 1,
            'goal_id'      => 9999999,
            'name'         => 'test',
            'start_date'   => time(),
            'end_date'     => time(),
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
        ];
        $this->KeyResult->create();
        $this->KeyResult->save($kr);
        $kr_id = $this->KeyResult->getLastInsertID();
        $res = $this->KeyResult->isPermitted($kr_id);
        $this->assertFalse($res, "存在しないSKR");
    }

    function testGetProgress()
    {
        $this->assertEquals(0, $this->KeyResult->getProgress(0, 100, 0));
        $this->assertEquals(50, $this->KeyResult->getProgress(0, 100, 50));
        $this->assertEquals(50, $this->KeyResult->getProgress(100, 150, 125));
        $this->assertEquals(0, $this->KeyResult->getProgress(100, 150, 75));
    }

    function testComplete()
    {
        $this->setDefault();
        try {
            $this->KeyResult->complete(999999);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testIncomplete()
    {
        $this->setDefault();
        try {
            $this->KeyResult->incomplete(999999);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testGetIncompleteKrCount()
    {
        $this->setDefault();
        $goal_id = 1;

        // 現在の未完了件数
        $count1 = $this->KeyResult->getIncompleteKrCount($goal_id);

        // １件完了済に更新する
        $row = $this->KeyResult->find('first', [
            'conditions' => [
                'goal_id'   => $goal_id,
                'completed' => null,
            ],
        ]);
        $this->assertNotEmpty($row);
        $this->KeyResult->id = $row['KeyResult']['id'];
        $this->KeyResult->read();
        $res = $this->KeyResult->save(['completed' => 1111111], false);
        $this->assertNotEmpty($res);

        // 数が合うか確認
        $count2 = $this->KeyResult->getIncompleteKrCount($goal_id);
        $this->assertEquals($count1 - 1, $count2);
    }

    function testGetKrNameList()
    {
        $this->setDefault();
        $goal_id = 1;
        $team_id = 1;
        $user_id = 1;
        $this->KeyResult->deleteAll(['KeyResult.goal_id' => $goal_id]);
        $this->KeyResult->saveAll(
            [
                [
                    'goal_id'      => $goal_id,
                    'team_id'      => $team_id,
                    'user_id'      => $user_id,
                    'name'         => 'test1',
                    'value_unit'   => 1,
                    'start_value'  => 0,
                    'target_value' => 100,
                ],
                [
                    'goal_id'      => $goal_id,
                    'team_id'      => $team_id,
                    'user_id'      => $user_id,
                    'name'         => 'test2',
                    'completed'    => time(),
                    'value_unit'   => 1,
                    'start_value'  => 0,
                    'target_value' => 100,
                ],
            ]
        );
        $this->assertCount(3, $this->KeyResult->getKrNameList($goal_id, true));
        $this->assertCount(5, $this->KeyResult->getKrNameList($goal_id, true, true));
    }

    function testIsComplete()
    {
        $this->setDefault();
        $res = $this->KeyResult->isCompleted(1);
        $this->assertFalse($res);
        $res = $this->KeyResult->isCompleted(999999999);
        $this->assertFalse($res);
        $res = $this->KeyResult->isCompleted(4);
        $this->assertTrue($res);
    }

    function testGetKrRelatedUserAction()
    {
        $this->setDefault();
        $this->KeyResult->ActionResult->deleteAll(['ActionResult.user_id' => 1], false);
        $this->KeyResult->deleteAll(['KeyResult.user_id' => 1], false);
        $data_kr = [
            'name'         => 'test_kr',
            'goal_id'      => 1,
            'user_id'      => 1,
            'team_id'      => 1,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
        ];
        $this->KeyResult->create();
        $this->KeyResult->save($data_kr);
        $data = [
            'goal_id'       => 1,
            'user_id'       => 1,
            'team_id'       => 1,
            'key_result_id' => $this->KeyResult->getLastInsertID()
        ];
        $this->KeyResult->ActionResult->create();
        $this->KeyResult->ActionResult->save($data);
        $actual = $this->KeyResult->getKrRelatedUserAction(1, 1);
        $this->assertNotEmpty($actual);
    }

    function setDefault()
    {
        $this->KeyResult->my_uid = 1;
        $this->KeyResult->current_team_id = 1;
        $this->KeyResult->ActionResult->my_uid = 1;
        $this->KeyResult->ActionResult->current_team_id = 1;
        $this->KeyResult->Goal->my_uid = 1;
        $this->KeyResult->Goal->current_team_id = 1;
        $this->KeyResult->Team->my_uid = 1;
        $this->KeyResult->Team->current_team_id = 1;
        $this->KeyResult->Goal->GoalMember->my_uid = 1;
        $this->KeyResult->Goal->GoalMember->current_team_id = 1;
        $this->KeyResult->Team->EvaluateTerm->current_team_id = 1;
        $this->KeyResult->Team->EvaluateTerm->my_uid = 1;
        $this->KeyResult->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->KeyResult->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->KeyResult->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
    }

    function testValidateEditProgress()
    {
        $this->setDefault();
        $this->KeyResult->validate = am($this->KeyResult->validate, $this->KeyResult->updateValidate);
        $krs = $this->KeyResult->findAllByDelFlg(0);
        $krs = Hash::combine($krs, '{n}.KeyResult.id', '{n}.KeyResult');

        // フィールドなし
        $updateKr = ['id' => 1];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $expectErrMsg = __("Input is required.");
        $this->assertTrue(in_array($expectErrMsg, $err));


        // 単位が完了/未完了の場合はチェック不要
        $updateKr = ['id' => 3, 'value_unit'=> KeyResult::UNIT_BINARY, 'target_value' => 100];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertEmpty($err);

        /* 開始値必須チェック */
        // 単位変更無しの場合必須チェックはしない
        $updateKr = ['id' => 1, 'value_unit' => $krs[1]['value_unit']];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_value');
        $this->assertEmpty($err);

        // 単位変更したら必須チェック
        $updateKr = ['id' => 1, 'value_unit' => 1];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_value');
        $this->assertEmpty($err);

        $updateKr = ['id' => 1, 'value_unit' => 1, 'start_value' => ""];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_value');
        $expectErrMsg = __("Input is required.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        /* 目標値必須チェック */
        $updateKr = ['id' => 1, 'value_unit' => 0];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'target_value');
        $this->assertEmpty($err);

        $updateKr = ['id' => 1, 'value_unit' => 1, 'target_value' => ""];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'target_value');
        $expectErrMsg = __("Input is required.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 変更無し
        $updateKr = $krs[1];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertEmpty($err);

        // 開始値と目標値が同じ値でないか
        $updateKr = ['id' => 1, 'value_unit' => $krs[1]['value_unit'], 'start_value' => 10, 'target_value' => 10];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $expectErrMsg = __("You can not change start value and target value to the same value.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        /* 進捗方向チェック */
        // 進捗の値が増加から減少の方向に変更してないか
        $updateKr = ['id' => 1, 'value_unit' => $krs[1]['value_unit'], 'start_value' => 10, 'target_value' => 9.999];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $expectErrMsg = __("You can not change the values from increase to decrease.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 開始値・目標値マイナス
        $updateKr = ['id' => 1, 'value_unit' => 1, 'start_value' => -10, 'target_value' => -9.999];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertTrue(empty($err) || !in_array($expectErrMsg, $err));

        // 単位変更時は進捗方向チェック不要
        $updateKr = ['id' => 1, 'value_unit' => 1, 'start_value' => 10, 'target_value' => 9.999];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertTrue(empty($err) || !in_array($expectErrMsg, $err));

        // 進捗の値が減少から増加の方向に変更してないか
        $updateKr = ['id' => 2, 'value_unit' => $krs[2]['value_unit'], 'start_value' => -100, 'target_value' => -99.999];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $expectErrMsg = __("You can not change the values from decrease to increase.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 単位変更時は進捗方向チェック不要
        $updateKr = ['id' => 2, 'value_unit' => 0, 'start_value' => 100, 'target_value' => 99];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertTrue(empty($err) || !in_array($expectErrMsg, $err));

        // 目標値を現在値と同じ値への変更はOK
        $currentVal = Hash::get($this->KeyResult->getById(5), 'current_value');
        $updateKr = ['id' => 5, 'start_value' => 0.001, 'target_value' => $currentVal];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'value_unit');
        $this->assertEmpty($err);
    }

    /**
     * 現在値バリデーション
     */
    function testValidateCurrentValue()
    {
        $this->setDefault();

        $this->KeyResult->validate = am($this->KeyResult->validate, $this->KeyResult->updateValidate);
        // フィールドなし
        $updateKr = ['id' => 1, 'value_unit' => 0];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        // 空文字エラー
        $updateKr = ['id' => 1, 'value_unit' => 0, 'current_value' => ""];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertNotEmpty($err);

        // 文字列の場合
        $updateKr = ['id' => 1, 'value_unit' => 0, 'current_value' => 'a'];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $expectErrMsg = __("Only Numeric characters are allowed.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 単位が完了/未完了の場合
        $updateKr = ['id' => 1, 'value_unit' => KeyResult::UNIT_BINARY, 'current_value' => 0];
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        /* 現在値が開始値と終了値の間か */
        // 進捗方向：増加
        $updateKr = ['id' => 1, 'value_unit' => 0, 'start_value' => 0, 'target_value' => 1234567890];
        $this->KeyResult->set(am($updateKr, ['current_value' => -0.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $expectErrMsg = __("Please input current value between start value and target value.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        $updateKr = ['id' => 1, 'value_unit' => 0, 'start_value' => 0, 'target_value' => 1234567890];
        $this->KeyResult->set(am($updateKr, ['current_value' => 0]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 0.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567889.999]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567890]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567890.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $expectErrMsg = __("Please input current value between start value and target value.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        // 進捗方向：減少
        $updateKr = ['id' => 1, 'value_unit' => 0, 'start_value' => 1234567890, 'target_value' => 0];
        $this->KeyResult->set(am($updateKr, ['current_value' => -0.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $expectErrMsg = __("Please input current value between start value and target value.");
        $this->assertTrue(in_array($expectErrMsg, $err));

        $updateKr = ['id' => 1, 'value_unit' => 0, 'start_value' => 0, 'target_value' => 1234567890];
        $this->KeyResult->set(am($updateKr, ['current_value' => 0]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 0.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567889.999]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567890]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $this->assertEmpty($err);

        $this->KeyResult->set(am($updateKr, ['current_value' => 1234567890.001]));
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'current_value');
        $expectErrMsg = __("Please input current value between start value and target value.");
        $this->assertTrue(in_array($expectErrMsg, $err));
    }
}
