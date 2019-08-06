<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'KeyResultService');
App::import('Service', 'GoalService');
App::import('Service', 'ActionService');
App::import('Service', 'KrValuesDailyLogService');
App::import('Service/Api', 'ApiKeyResultService');
App::uses('KeyResult', 'Model');
App::uses('Goal', 'Model');
App::uses('GoalLabel', 'Model');
App::uses('ActionResult', 'Model');

/**
 * KeyResultServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property KeyResultService $KeyResultService
 */
class KeyResultServiceTest extends GoalousTestCase
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
        'app.kr_change_log',
        'app.kr_progress_log',
        'app.goal',
        'app.goal_member',
        'app.team_member',
        'app.goal_label',
        'app.label',
        'app.post',
        'app.circle',
        'app.goal_category',
        'app.user',
        'app.team',
        'app.attached_file',
        'app.action_result',
        'app.action_result_file',
        'app.kr_values_daily_log',
        'app.team_translation_language',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalService = ClassRegistry::init('GoalService');
        $this->KeyResultService = ClassRegistry::init('KeyResultService');
        $this->ApiKeyResultService = ClassRegistry::init('ApiKeyResultService');
        $this->ActionService = ClassRegistry::init('ActionService');
        $this->KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');
        $this->ActionResult = ClassRegistry::init('ActionResult');
        $this->Goal = ClassRegistry::init('Goal');
        $this->KeyResult = ClassRegistry::init('KeyResult');
        $this->KrValuesDailyLog = ClassRegistry::init('KrValuesDailyLog');
    }

    function test_get()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildKrUnitsSelectList()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processKeyResults()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processKeyResult()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_findByGoalId()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_exchangeTkr()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_formatBigFloat()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    /**
     * 更新バリデーション
     */
    function testValidateUpdate()
    {
        // KRが存在するか
        $err = $this->KeyResultService->validateUpdate(1, 999, []);
        $this->assertEquals($err, ["status_code" => 400, "message" => __("Not exist")]);

        // ゴールメンバーか
        $err = $this->KeyResultService->validateUpdate(999, 1, []);
        $this->assertEquals($err, ["status_code" => 403, "message" => __("You have no permission.")]);

        // KRが既に完了していないか
        $err = $this->KeyResultService->validateUpdate(1, 6, []);
        $this->assertEquals($err, ["status_code" => 400, "message" => __("You can't edit achieved KR.")]);

        /* フォームバリデーション */
        $err = $this->KeyResultService->validateUpdate(1, 1, [
            'id'            => '1',
            'name'          => 'test',
            'value_unit'    => 0,
            'start_value'   => 10,
            'target_value'  => 100,
            'current_value' => 11,
            'description'   => "This is test."
        ]);
        $this->assertEmpty($err);

        $err = $this->KeyResultService->validateUpdate(1, 1, [
            'id'            => '1',
            'name'          => '',
            'value_unit'    => 0,
            'start_value'   => 10,
            'target_value'  => 100,
            'current_value' => 11,
            'description'   => "This is test."
        ]);
        $this->assertEquals(Hash::get($err, 'status_code'), 400);
        $this->assertNotEmpty(Hash::get($err, 'validation_errors'));
    }

    /**
     * 更新データ作成
     */
    function testBuildUpdateKr()
    {
        $data = [
            'id'            => '1',
            'name'          => 'test',
            'value_unit'    => 1,
            'start_value'   => 10,
            'target_value'  => 100,
            'current_value' => 11,
            'description'   => "This is test.",
            'start_date'    => date('Y/m/d', 10000),
            'end_date'      => date('Y/m/d', 19999),
        ];
        $this->Term->current_team_id = 1;
        $updateKr = $this->KeyResultService->buildUpdateKr(1, $data);
        foreach ($data as $k => $v) {
            if (!in_array($k, ['start_date', 'end_date'])) {
                $this->assertEquals($updateKr[$k], $v);
            } else {
                $this->assertTrue(Hash::check($updateKr, $k));
            }
        }

        $data = [
            'id'          => '1',
            'name'        => 'test',
            'value_unit'  => KeyResult::UNIT_BINARY,
            'description' => "This is test.",
            'start_date'  => date('Y/m/d', 10000),
            'end_date'    => date('Y/m/d', 19999),
        ];
        $this->Term->current_team_id = 1;
        $updateKr = $this->KeyResultService->buildUpdateKr(1, $data);
        $this->assertEquals($updateKr['start_value'], 0);
        $this->assertEquals($updateKr['current_value'], 0);
        $this->assertEquals($updateKr['target_value'], 1);

        // TKR が完了状態から 編集されて 目標値未達になった場合 (completed -> null)
        $data = [
            'id'          => '7',
            'name'        => 'test',
            'start_value'   => 10,
            'target_value'  => 100,
            'current_value' => 50,
            'completed'   => time(),
            'value_unit'  => KeyResult::UNIT_PERCENT,
            'description' => "This is test.",
            'start_date'  => date('Y/m/d', 10000),
            'end_date'    => date('Y/m/d', 19999),
        ];
        $this->Term->current_team_id = 1;
        $updateKr = $this->KeyResultService->buildUpdateKr(7, $data);
        $this->assertNull($updateKr['completed']);

        // TKR が完了状態から 編集されて 目標値未達になった場合 (completed -> null)
        $data = [
            'id'          => '8',
            'name'        => 'weight diet (completed)',
            'start_value'   => 80,
            'target_value'  => 70,
            'current_value' => 75,
            'completed'   => time(),
            'value_unit'  => KeyResult::UNIT_NUMBER,
            'description' => "This is test.",
            'start_date'  => date('Y/m/d', 10000),
            'end_date'    => date('Y/m/d', 19999),
        ];
        $this->Term->current_team_id = 1;
        $updateKr = $this->KeyResultService->buildUpdateKr(8, $data);
        $this->assertNull($updateKr['completed']);

    }

    /**
     * 更新
     */
    function testUpdate()
    {
        $data = [
            'id'            => '1',
            'name'          => 'test',
            'value_unit'    => 0,
            'start_value'   => 10,
            'target_value'  => 100,
            'current_value' => 11,
            'description'   => "This is test.",
            'start_date'    => date('Y/m/d', 10000),
            'end_date'      => date('Y/m/d', 19999),
        ];
        $this->Term->current_team_id = 1;
        $ret = $this->KeyResultService->update(1, 1, $data);
        $this->assertTrue($ret);
    }

    function testRemoveGoalMembersCacheInDashboard()
    {
        $goalId = $this->setupTestRemoveGoalMembersCacheInDashboard();

        $this->KeyResult->my_uid = 1;
        // TODO: hotfix対応のため一時コメントアウト。後で戻す
        // $this->ApiKeyResultService->findInDashboard(10);
        $this->KeyResult->my_uid = 2;
        // TODO: hotfix対応のため一時コメントアウト。後で戻す
        // $this->ApiKeyResultService->findInDashboard(10);
        $this->KeyResultService->removeGoalMembersCacheInDashboard($goalId, false);

        /* キャッシュが削除されていること */
        // メンバー1
        $this->KeyResult->my_uid = 1;
        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
        $this->assertEmpty($cache);
        // メンバー2
        $this->KeyResult->my_uid = 2;
        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
        $this->assertEmpty($cache);
    }

    private function setupTestRemoveGoalMembersCacheInDashboard()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $uid = 1;
        $goalId = $this->createGoal($uid);
        $this->createGoalMember(['user_id' => 2, 'goal_id' => $goalId, 'team_id' => 1]);
        return $goalId;
    }

    function testUpdateLatestActioned()
    {
        $this->setupTestUpdateLatestActioned();

        $time = time();
        $this->ActionResult->save([
            'id'      => 1,
            'created' => $time
        ]);

        // KRのアクション最新日時が更新されているか
        $oldKr = $this->KeyResult->getById(1);
        $this->KeyResultService->updateLatestActioned(1);
        $updatedKr = $this->KeyResult->getById(1);
        $this->assertTrue($oldKr['latest_actioned'] < $updatedKr['latest_actioned']);
        $this->assertEquals($updatedKr['latest_actioned'], $time);

        $time2 = time() + 1;
        $this->ActionResult->clear();
        $this->ActionResult->save([
            'id'      => 2,
            'created' => $time2
        ]);

        // KRのアクション最新日時が更新されているか
        $this->KeyResult->clear();
        $this->KeyResultService->updateLatestActioned(1);
        $updatedKr2 = $this->KeyResult->getById(1);
        $this->assertTrue($updatedKr['latest_actioned'] < $updatedKr2['latest_actioned']);
        $this->assertEquals($updatedKr2['latest_actioned'], $time2);
    }

    /**
     * 右カラムに表示するアクションメッセージ
     */
    function testGenerateActionMessage()
    {
        $this->setDefaultTeamIdAndUid();
        App::import('View', 'Helper/TimeExHelper');
        $TimeEx = new TimeExHelper(new View());

        // 完了KR
        $kr = $this->_getKrForGenerateActionMessage($latestActioned = 1485310914, $completed = 1485310914,
            $actions = []);
        $res = $this->KeyResultService->generateActionMessage($kr);
        $expected = __('Completed this on %s.', $TimeEx->formatDateI18n(1485310914));
        $this->assertEquals($res, $expected);

        // 最近アクションがあった未完了KR
        $kr = $this->_getKrForGenerateActionMessage($latestActioned = 1485310914, $completed = null, $actions = [1, 2]);
        $res = $this->KeyResultService->generateActionMessage($kr);
        $expected = __('%s member(s) actioned recently.', '<span class="font_verydark font_bold">2</span>');
        $this->assertEquals($res, $expected);

        // 最近アクションが無い未完了KR
        $kr = $this->_getKrForGenerateActionMessage($latestActioned = 1485310914, $completed = null, $actions = []);
        $res = $this->KeyResultService->generateActionMessage($kr);
        $expected = __("Take action since %s !", $TimeEx->formatDateI18n(1485310914));
        $this->assertEquals($res, $expected);

        // 一度もアクションが無い未完了KR
        $kr = $this->_getKrForGenerateActionMessage($latestActioned = null, $completed = null, $actions = []);
        $res = $this->KeyResultService->generateActionMessage($kr);
        $expected = __('Take first action to this !');
        $this->assertEquals($res, $expected);
    }

    private function _getKrForGenerateActionMessage($latestActioned, $completed, $actions)
    {
        $kr = [
            'key_result'     => [
                'latest_actioned' => $latestActioned,
                'completed'       => $completed
            ],
            'action_results' => $actions
        ];
        return $kr;
    }

    private function setupTestUpdateLatestActioned()
    {
        $this->setDefaultTeamIdAndUid();
        $this->ActionResult->my_uid = 1;
        $this->ActionResult->current_team_id = 1;
    }

    /**
     * ゴール削除
     */
    function test_delete()
    {
        /* テストデータ準備 */
        $krId = $this->setupTestDelete();
        // KR削除
        $this->KeyResultService->delete($krId);

        // KR削除できているか
        $ret = $this->KeyResult->getById($krId);
        $this->assertEmpty($ret);

        // KRとアクションの紐付けを解除できているか
        $ret = $this->ActionResult->findByKeyResultId($krId);
        $this->assertEmpty($ret);

        // KR進捗日次ログ削除できているか
        $ret = $this->KrValuesDailyLog->findByKeyResultId($krId);
        $this->assertEmpty($ret);

        // TODO:キャッシュ削除できているか

    }

    /**
     * test_delete用テストデータ準備
     */
    private function setupTestDelete()
    {
        $uid = 1;
        $teamId = 1;
        $this->setupTerm();
        $this->KeyResult->my_uid = $uid;
        $this->KeyResult->current_team_id = $teamId;
        $goalId = $this->createGoal($uid);
        $kr = Hash::get($this->KeyResult->getTkr($goalId), 'KeyResult');
        $fileIds = $this->prepareUploadImages();
        // アクション登録
        $saveAction = [
            "goal_id" => $goalId,
            "team_id" => $teamId,
            "user_id" => $uid,
            "name" => "ああああ\nいいいいいいい",
            "key_result_id" => $kr['id'],
            "key_result_current_value" => $kr['current_value'],
        ];
        $this->ActionService->create($saveAction, $fileIds, null);
        // KR進捗日次ログ保存
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $this->KrValuesDailyLogService->saveAsBulk(1, $yesterday);
        return $kr['id'];
    }

}
