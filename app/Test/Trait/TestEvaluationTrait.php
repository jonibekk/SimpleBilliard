<?php
App::uses('Evaluator', 'Model');
App::uses('Term', 'Model');

use Goalous\Model\Enum as Enum;

trait TestEvaluationTrait
{
    /**
     * Create evaluator for test
     *
     * @param int $teamId
     * @param int $evaluateeUserId
     * @param int $evaluatorUserId
     * @param int $indexNum
     *
     * @return array
     */
    protected function createEvaluator(int $teamId, int $evaluateeUserId, int $evaluatorUserId, int $indexNum): array
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');

        $Evaluator->create();
        $evaluator = $Evaluator->save([
            'evaluatee_user_id' => $evaluateeUserId,
            'evaluator_user_id' => $evaluatorUserId,
            'team_id'           => $teamId,
            'index_num'         => $indexNum,
        ]);

        return reset($evaluator);
    }
}