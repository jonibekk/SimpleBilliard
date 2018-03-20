<?php
App::import('Service', 'EvaluatorChangeLogService');
App::import('Model', 'EvaluatorChangeLog');
App::import('Model', 'Evaluator');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: raharjas
 * Date: 15/03/2018
 * Time: 18:28
 *
 * @property Evaluator                 $Evaluator
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
        $this->Evaluator = ClassRegistry::init('Evaluator');
        $this->EvaluatorChangeLog = ClassRegistry::init("EvaluatorChangeLog");
        $this->EvaluatorChangeLogService = ClassRegistry::init("EvaluatorChangeLogService");
    }

    public function test_saveLog_success()
    {
        $teamId = 1;
        $evaluateeId = 2;
        $evaluatorIds = [3, 4, 5];
        $updaterId = 6;

        $this->Evaluator->insertEvaluators($teamId, $evaluateeId, $evaluatorIds);
        $this->EvaluatorChangeLogService->saveLog($teamId, $evaluateeId, $updaterId);
        $queryResult = $this->EvaluatorChangeLog->getLatestLogByUserIdAndTeamId($teamId, $evaluateeId);

        $this->assertEquals($teamId, $queryResult['team_id']);
        $this->assertEquals($evaluateeId, $queryResult['evaluatee_user_id']);
        $this->assertEquals($updaterId, $queryResult['last_update_user_id']);
        $this->assertEquals(implode(",", $evaluatorIds), $queryResult['evaluator_user_ids']);
    }

    public function test_saveEmptyLog_success()
    {
        $teamId = 1;
        $evaluateeId = 3;
        $updaterId = 5;

        $this->EvaluatorChangeLogService->saveLog($teamId, $evaluateeId, $updaterId);
        $queryResult = $this->EvaluatorChangeLog->getLatestLogByUserIdAndTeamId($teamId, $evaluateeId);

        $this->assertEquals($teamId, $queryResult['team_id']);
        $this->assertEquals($evaluateeId, $queryResult['evaluatee_user_id']);
        $this->assertEquals($updaterId, $queryResult['last_update_user_id']);
    }
}