<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'KeyResultService');
App::import('Service/Api', 'ApiKeyResultService');
App::import('Service', 'GoalService');
App::uses('KeyResult', 'Model');
App::uses('Goal', 'Model');
App::uses('GoalLabel', 'Model');


/**
 * KeyResultServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property KeyResultService $KeyResultService
 */
class ApiKeyResultServiceTest extends GoalousTestCase
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
    }

    function testFindInDashboard()
    {
        $this->setupTestFindInDashboard();
        $this->KeyResult->my_uid = 1;
        $this->KeyResult->current_team_id = 1;

        // キャッシュは1次リリースでは使用しないのでコメントアウト
//        // まだ取得していないのでキャッシュが存在しないこと
//        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
//        $this->assertEmpty($cache);
//
//        // 取得時にキャッシュしているか
//        $res = $this->ApiKeyResultService->findInDashboard(10);
//        $res = Hash::extract($res, "{n}.key_result.id");
//        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
//        $cache = Hash::extract($cache, "{n}.KeyResult.id");
//        $this->assertEquals($res, $cache);
//
//        // キャッシュがされているか
//        $this->KeyResult->my_uid = 2;
//        // まだ取得していないのでキャッシュが存在しないこと
//        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
//        $this->assertEmpty($cache);
//
//        // 取得時にキャッシュしているか
//        $res = $this->ApiKeyResultService->findInDashboard(10);
//        $res = Hash::extract($res, "{n}.key_result.id");
//        $cache = Cache::read($this->KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
//        $cache = Hash::extract($cache, "{n}.KeyResult.id");
//        $this->assertEquals($res, $cache);
    }

    private function setupTestFindInDashboard()
    {
        $this->setDefaultTeamIdAndUid();
        $this->setupTerm();
        $goalId = $this->createGoal(1);
        $this->createGoalMember(['user_id' => 2, 'goal_id' => $goalId, 'team_id' => 1]);

    }
}
