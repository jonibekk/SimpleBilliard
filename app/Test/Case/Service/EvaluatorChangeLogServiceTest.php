<?php
App::import('Service', 'EvaluatorChangeLogService');
App::import('Model', 'EvaluatorChangeLog');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: raharjas
 * Date: 15/03/2018
 * Time: 18:28
 *
 * @property EvaluatorChangeLog        $EvaluatorChangeLog
 * @property EvaluatorChangeLogService $EvaluatorChangeLogService
 */
class EvaluatorChangeLogServiceTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.member_type',
        'app.local_name',
        'app.cake_session',
        'app.team',
        'app.user',
        'app.notify_setting',
        'app.oauth_token',
        'app.team_member',
        'app.evaluator',
        'app.evaluator_change_logs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluatorChangeLog = ClassRegistry::init("EvaluatorChangeLog");
        $this->EvaluatorChangeLogService = ClassRegistry::init("EvaluatorChangeLogService");
    }

    public function test_insertChangeLog_success()
    {
        $teamId = 1;
        $evaluateeId = 1;
        $updaterId = 6;

        $this->EvaluatorChangeLogService->saveLog($teamId, $evaluateeId, $updaterId);
        $queryResult = $this->EvaluatorChangeLog->getLatestLogByUserIdAndTeamId($teamId, $evaluateeId);

        $this->assertCount(1, $queryResult);
        $this->assertEquals($teamId, $queryResult['EvaluatorChangeLog']['team_id']);
        $this->assertEquals($evaluateeId, $queryResult['EvaluatorChangeLog']['evaluatee_user_id']);
        $this->assertEquals($updaterId, $queryResult['EvaluatorChangeLog']['last_update_user_id']);
    }

}