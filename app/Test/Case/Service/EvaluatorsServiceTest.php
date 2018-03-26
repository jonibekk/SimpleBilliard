<?php
App::import('Service', 'EvaluatorService');
App::import('Model', 'Evaluator');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 15:36
 *
 * @property Evaluator        $Evaluator
 * @property EvaluatorService $EvaluatorService
 */
class EvaluatorsServiceTest extends GoalousTestCase
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
        $this->EvaluatorService = ClassRegistry::init('EvaluatorService');
    }

    /**
     * Test inserting  evaluators of an user
     */
    public function test_setEvaluators_success()
    {
        $teamId = 2;
        $evaluateeUserId = 1;
        $evaluatorsUserIds = [6, 7, 8];

        $this->EvaluatorService->setEvaluators($teamId, $evaluateeUserId, $evaluatorsUserIds);
        $this->assertCount(count($evaluatorsUserIds),
            $this->Evaluator->getExistingEvaluatorsIds($teamId, $evaluateeUserId));
    }

    /**
     * Test getting the evaluators of an user
     */
    public function test_getEvaluatorsByTeamIdAndEvaluateeUserId()
    {
        $teamId = 2;
        $evaluateeUserId = 1;
        $evaluatorsUserIds = [6, 7, 8];

        $this->EvaluatorService->setEvaluators($teamId, $evaluateeUserId, $evaluatorsUserIds);
        $queryResult = $this->EvaluatorService->getEvaluatorsByTeamIdAndEvaluateeUserId($teamId, $evaluateeUserId);
        $this->assertCount(count($evaluatorsUserIds), $queryResult);
    }
}
