<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'KeyResultService');
App::import('Service', 'GoalService');
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
        'app.evaluate_term',
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
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->KeyResultService = ClassRegistry::init('KeyResultService');
        $this->ApiKeyResultService = ClassRegistry::init('ApiKeyResultService');
        $this->GoalService = ClassRegistry::init('GoalService');
        $this->Goal = ClassRegistry::init('Goal');
        $this->KeyResult = ClassRegistry::init('KeyResult');
        $this->ActionResult = ClassRegistry::init('ActionResult');
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
        $this->EvaluateTerm->current_team_id = 1;
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
        $this->EvaluateTerm->current_team_id = 1;
        $updateKr = $this->KeyResultService->buildUpdateKr(1, $data);
        $this->assertEquals($updateKr['start_value'], 0);
        $this->assertEquals($updateKr['current_value'], 0);
        $this->assertEquals($updateKr['target_value'], 1);

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
        $this->EvaluateTerm->current_team_id = 1;
        $ret = $this->KeyResultService->update(1, 1, $data);
        $this->assertTrue($ret);
    }

    function testRemoveGoalMembersCacheInDashboard()
    {
        $goalId = $this->setupTestRemoveGoalMembersCacheInDashboard();

        $this->KeyResult->my_uid = 1;
        $this->ApiKeyResultService->findInDashboard(10);
        $this->KeyResult->my_uid = 2;
        $this->ApiKeyResultService->findInDashboard(10);
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

    private function setupTestUpdateLatestActioned()
    {
        $this->setDefaultTeamIdAndUid();
        $this->ActionResult->my_uid = 1;
        $this->ActionResult->current_team_id = 1;
    }

}
