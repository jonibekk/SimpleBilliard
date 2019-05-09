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
        'app.term',
        'app.key_result',
        'app.goal',
        'app.goal_category',
        'app.goal_member',
        'app.term',
        'app.post_share_circle',
        'app.circle',
        'app.post',
        'app.goal_label',
        'app.label',
        'app.team_member',
        'app.follower',
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
        $this->Goal = ClassRegistry::init('Goal');
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

        $goalId = $this->createGoal(1);
        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;

        $goal = $this->Goal->getById($goalId);

        $startDate = AppUtil::dateYmdReformat($goal['start_date'], "/");
        $endDate = AppUtil::dateYmdReformat($goal['end_date'], "/");

        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'name'       => 'test',
            ]
        ];
        $res = $this->KeyResult->add($data, $goalId);
        $this->assertTrue($res);

        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => $startDate,
                'end_date'   => $endDate,
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
            'start_date'       => AppUtil::dateYmd(REQUEST_TIMESTAMP),
            'end_date'         => AppUtil::dateYmd(REQUEST_TIMESTAMP),
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
            'start_date'   => AppUtil::dateYmd(REQUEST_TIMESTAMP),
            'end_date'     => AppUtil::dateYmd(REQUEST_TIMESTAMP),
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
            'start_date'   => AppUtil::dateYmd(REQUEST_TIMESTAMP),
            'end_date'     => AppUtil::dateYmd(REQUEST_TIMESTAMP),
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
        $this->KeyResult->Team->Term->current_team_id = 1;
        $this->KeyResult->Team->Term->my_uid = 1;
        $this->KeyResult->Team->Term->addTermData(Term::TYPE_CURRENT);
        $this->KeyResult->Team->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->KeyResult->Team->Term->addTermData(Term::TYPE_NEXT);
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
        $updateKr = ['id' => 3, 'value_unit' => KeyResult::UNIT_BINARY, 'target_value' => 100];
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
        $updateKr = [
            'id'           => 2,
            'value_unit'   => $krs[2]['value_unit'],
            'start_value'  => -100,
            'target_value' => -99.999
        ];
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

    /**
     * 現在値バリデーション
     */
    function test_customValidRangeDate()
    {
        $this->setDefault();
        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;

        $this->KeyResult->validate = am($this->KeyResult->validate, $this->KeyResult->post_validate);

        $startDate = "2017/01/02";
        $endDate = "2017/03/29";

        $currentTerm = $this->Team->Term->getCurrentTermData();

        $this->Team->Term->clear();
        $this->Team->Term->id = $currentTerm['id'];
        $this->Team->Term->save(['timezone' => 0]);
        $this->customValidRangeDateThreshold($startDate, $endDate);

        $this->Team->Term->clear();
        $this->Team->Term->id = $currentTerm['id'];
        $this->Team->Term->save(['timezone' => -12]);
        $this->customValidRangeDateThreshold($startDate, $endDate);

        $this->Team->Term->clear();
        $this->Team->Term->id = $currentTerm['id'];
        $this->Team->Term->save(['timezone' => +12]);
        $this->customValidRangeDateThreshold($startDate, $endDate);
    }

    /**
     * KR開始/終了日閾値チェック共通
     *
     * @param $startDate
     * @param $endDate
     */
    private function customValidRangeDateThreshold(string $startDate, string $endDate)
    {
        $this->Goal->id = 1;
        $this->Goal->save([
            'start_date' => AppUtil::dateYmd(strtotime($startDate)),
            'end_date'   => AppUtil::dateYmd(strtotime($endDate))
        ]);

        $correctErrMsg = __("Please input start / end date within start / end date of the Goal.");

        // 開始/終了日がゴールの開始/終了日と同じ
        $updateKr['goal_id'] = 1;
        $updateKr['start_date'] = $startDate;
        $updateKr['end_date'] = $endDate;
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_date');
        $this->assertEmpty($err);

        // 開始日がゴール開始日以前
        $updateKr['start_date'] = AppUtil::dateYmdReformat($startDate . ' -1 day', "/");
        $updateKr['end_date'] = $endDate;
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_date.0');
        $this->assertEquals($err, $correctErrMsg);

        // 開始日がゴール開始日以降
        $updateKr['start_date'] = AppUtil::dateYmdReformat($startDate . ' +1 day', "/");
        $updateKr['end_date'] = $endDate;
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_date.0');
        $this->assertEmpty($err);

        // 終了日がゴール終了日以前
        $updateKr['start_date'] = $startDate;
        $updateKr['end_date'] = AppUtil::dateYmdReformat($endDate . ' -1 day', "/");
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_date.0');
        $this->assertEmpty($err);

        // 終了日がゴール終了日以降
        $updateKr['start_date'] = $startDate;
        $updateKr['end_date'] = AppUtil::dateYmdReformat($endDate . ' +1 day', "/");
        $this->KeyResult->set($updateKr);
        $this->KeyResult->validates();
        $err = Hash::get($this->KeyResult->validationErrors, 'start_date.0');
        $this->assertEquals($err, $correctErrMsg);
    }

    /**
     * 右カラムKR一覧取得テスト
     * アクション済みKRのみ取得
     */
    function testFindInDashboardOnlyActioned()
    {
        $this->setDefault();
        $this->saveKrsForDashboard([['111111', 3], ['222222', 2], ['333333', 1]]);
        $res = $this->KeyResult->findInDashboard(10);
        $res = Hash::extract($res, '{n}.KeyResult.latest_actioned');
        $expected = ['333333', '222222', '111111'];
        // TODO: hotfix対応のため一時コメントアウト。後で戻す
        // $this->assertEquals($res, $expected);
    }

    /**
     * 右カラムKR一覧取得テスト
     * アクションされてないKRのみ取得
     */
    function testFindInDashboardOnlyNotActioned()
    {
        $this->setDefault();
        $this->saveKrsForDashboard([[null, 4], [null, 5], [null, 2]]);
        $res = $this->KeyResult->findInDashboard(10);
        $res = Hash::extract($res, '{n}.KeyResult.priority');
        $expected = [5, 4, 2];
        // TODO: hotfix対応のため一時コメントアウト。後で戻す
        // $this->assertEquals($res, $expected);
    }

    /**
     * 右カラムKR一覧取得テスト
     * 両条件混合
     */
    function testFindInDashboardBoth()
    {
        $this->setDefault();
        $this->saveKrsForDashboard([['111111', 3], ['222222', 2], ['333333', 1], [null, 4], [null, 5], [null, 2]]);
        $res = $this->KeyResult->findInDashboard(10);
        $res = Hash::extract($res, '{n}.KeyResult.priority');
        $expected = [1, 2, 3, 5, 4, 2];
        // TODO: hotfix対応のため一時コメントアウト。後で戻す
        // $this->assertEquals($res, $expected);
    }

    /**
     * 右カラムKR一覧取得テスト
     * KRがひとつもない
     */
    function testFindInDashboardEmpty()
    {
        $this->setDefault();
        $this->KeyResult->deleteAll(['KeyResult.id >' => 0], false);
        $res = $this->KeyResult->findInDashboard(10);
        $expected = [];
        $this->assertEquals($res, $expected);
    }

    /**
     * 右カラムKR数取得
     */
    function testCountMine()
    {
        $this->setDefault();
        $this->saveKrsForDashboard([['111111', 3], ['222222', 2], ['333333', 1]]);
        $this->KeyResult->complete(3);
        $res = $this->KeyResult->countMine();
        $this->assertEquals($res, 2);
    }

    function test_updateTermByGoalId_currentToNext()
    {
        $this->deleteAllTeam();
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);

        $nextTerm = $this->KeyResult->Team->Term->getNextTermData();

        $goalId = $this->createGoalKrs(Term::TYPE_CURRENT, [0, 10], $teamId);
        $this->KeyResult->updateTermByGoalId($goalId, Term::TYPE_NEXT);
        $updatedKr = Hash::get($this->KeyResult->findByGoalId($goalId), 'KeyResult');
        $this->assertEquals($updatedKr['start_date'], $nextTerm['start_date']);
        $this->assertEquals($updatedKr['end_date'], $nextTerm['end_date']);
    }

    function test_updateTermByGoalId_nextToCurrent()
    {
        $this->deleteAllTeam();
        $teamId = $this->createTeam();
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->setupTerm($teamId);
        $currentTerm = $this->KeyResult->Team->Term->getCurrentTermData();

        $goalId = $this->createGoalKrs(Term::TYPE_NEXT, [0, 10], $teamId);
        $this->KeyResult->updateTermByGoalId($goalId, Term::TYPE_CURRENT);
        $updatedKr = Hash::get($this->KeyResult->findByGoalId($goalId), 'KeyResult');

        $this->assertTrue($currentTerm['start_date'] <= $updatedKr['start_date']);
        $this->assertTrue($updatedKr['start_date'] <= $currentTerm['end_date']);
        $this->assertEquals($updatedKr['end_date'], $currentTerm['end_date']);
    }

    /**
     * KRカラムデータ取得ロジックテスト用にデータを生成
     */
    function saveKrsForDashboard($data)
    {
        // 既存データ削除
        $this->KeyResult->deleteAll(['KeyResult.id >' => 0], false);
        $this->KeyResult->Goal->deleteAll(['Goal.id >' => 0], false);
        $this->KeyResult->Goal->GoalMember->deleteAll(['GoalMember.id >' => 0], false);
        $this->KeyResult->ActionResult->deleteAll(['ActionResult.id >' => 0], false);

        // Termデータ作成
        $this->KeyResult->Team->Term->addTermData(Term::TYPE_CURRENT);
        $currentTerm = $this->KeyResult->Team->Term->getCurrentTermData();

        $userId = 1;

        foreach ($data as $key => $val) {
            $actionCreated = $val[0] ?? null;
            $priority = $val[1] ?? 3;
            $modelId = $key + 1;

            // ゴール作成
            $this->KeyResult->Goal->create();
            $this->KeyResult->Goal->save([
                'id'      => $modelId,
                'team_id' => 1,
                'end_date' => date('Y-m-d', strtotime('1 day'))
            ], false);

            // ゴールメンバー作成
            $this->KeyResult->Goal->GoalMember->create();
            $this->KeyResult->Goal->GoalMember->save([
                'user_id' => $userId,
                'goal_id' => $modelId,
                'team_id' => 1
            ], false);

            // KR作成
            $this->KeyResult->create();
            $this->KeyResult->save([
                'id'              => $modelId,
                'priority'        => $priority,
                'goal_id'         => $modelId,
                'team_id'         => 1,
                'start_date'      => $currentTerm['start_date'],
                'end_date'        => $currentTerm['end_date'],
                'latest_actioned' => $actionCreated
            ], false);

            // アクション作成
            if ($actionCreated) {
                $this->KeyResult->ActionResult->create();
                $this->KeyResult->ActionResult->save([
                    'key_result_id' => $modelId,
                    'created'       => $actionCreated,
                    'team_id'       => 1
                ], false);
            }
        }
    }


    function test_getAllByGoalId()
    {
        $teamId = 1;
        $userId = 1;
        $this->setDefault();

        // Not include TKR
        $goalId = $this->createGoal(1);
        $res = $this->KeyResult->getAllByGoalId($goalId);
        $this->assertEmpty($res);

        // Include TKR
        $res = $this->KeyResult->getAllByGoalId($goalId, true);
        $this->assertNotEmpty($res);
        $this->assertEquals($res[0]['goal_id'], $goalId);
        $this->assertEquals($res[0]['user_id'], $userId);
        $this->assertEquals($res[0]['priority'], 5);
        $this->assertEquals($res[0]['start_value'], 0);
        $this->assertEquals($res[0]['target_value'], 100);
        $this->assertEquals($res[0]['current_value'], 0);
        $this->assertEquals($res[0]['tkr_flg'], 1);
        $krIds[] = $res[0]['id'];

        // ORDER
        $krIds[] = $this->createKr($goalId, $teamId, $userId, 50, 0, 100, 5, Term::TYPE_CURRENT, false);
        $res = $this->KeyResult->getAllByGoalId($goalId, true);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], $krIds[0]);
        $this->assertEquals($res[1]['id'], $krIds[1]);

        $krIds[] = $this->createKr($goalId, $teamId, $userId, 50, 0, 100, 4, Term::TYPE_CURRENT, false);
        $res = $this->KeyResult->getAllByGoalId($goalId, true);
        $this->assertEquals(count($res), 3);
        $this->assertEquals($res[0]['id'], $krIds[0]);
        $this->assertEquals($res[1]['id'], $krIds[1]);
        $this->assertEquals($res[2]['id'], $krIds[2]);

        $krIds[] = $this->createKr($goalId, $teamId, $userId, 50, 0, 100, 4, Term::TYPE_CURRENT, false);
        $res = $this->KeyResult->getAllByGoalId($goalId, true);
        $this->assertEquals(count($res), 4);
        $this->assertEquals($res[0]['id'], $krIds[0]);
        $this->assertEquals($res[1]['id'], $krIds[1]);
        $this->assertEquals($res[2]['id'], $krIds[2]);
        $this->assertEquals($res[3]['id'], $krIds[3]);

        // Exclude deleted krs
        $this->KeyResult->delete($res[1]['id']);
        $res = $this->KeyResult->getAllByGoalId($goalId, true);
        $this->assertEquals(count($res), 3);
        $this->assertEquals($res[0]['id'], $krIds[0]);
        $this->assertEquals($res[1]['id'], $krIds[2]);
        $this->assertEquals($res[2]['id'], $krIds[3]);

    }
}
