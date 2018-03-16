<?php
App::uses('AppController', 'Controller');
App::uses('User', 'Model');
App::import('Service', 'EvaluationService');

/**
 * EvaluatorSettingsController Controller
 */
class EvaluatorSettingsController extends AppController
{
    public $uses = [
    ];

    function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Evaluation list of
     * self evaluation and coachee evaluation
     */
    function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        /** @var  EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init('EvaluationService');

        $termId = $this->Team->Term->getCurrentTermId();
        $userId = $this->Auth->user('id');

        $selfEvaluation = $EvaluationService->getEvalStatus($termId, $userId);
        $evaluateesEvaluation = $EvaluationService->getEvaluateeFromCoachUserId($termId, $userId);

        // Reconstruct evaluation flow data structure
        $selfEvaluation = $this->reconstructFlows([$selfEvaluation])[0];
        $evaluateesEvaluation = $this->reconstructFlows($evaluateesEvaluation);

        // Count zero evaluatee users
        $countOfZeroEvaluateeUsers = 0;
        foreach ($evaluateesEvaluation as $key => $evaluateeEvaluation) {
            if (0 === count($evaluateeEvaluation['flow'])) {
                $countOfZeroEvaluateeUsers++;
            }
        }

        $isFixedEvaluationOrder = $this->Team->EvaluationSetting->isFixedEvaluationOrder();

        $this->set('termId', $termId);
        $this->set('selfEvaluation', $selfEvaluation);
        $this->set('evaluateesEvaluation', $evaluateesEvaluation);
        $this->set('isFrozen', false);
        $this->set('isFixedEvaluationOrder', $isFixedEvaluationOrder);
        $this->set('countOfZeroEvaluateeUsers', $countOfZeroEvaluateeUsers);
    }

    /**
     * Filtering for using to showing only evaluators
     *
     * @param array $evaluations
     *
     * @return array
     */
    private function reconstructFlows(array $evaluations): array
    {
        foreach ($evaluations as $key => $evaluation) {
            $flow = $evaluation['flow'] ?? [];
            $evaluations[$key]['flow'] = array_values(
                // leave the leader and evaluator in the flow array
                // removing "self" and "final evaluator"
                array_filter($flow, function ($evaluateFlow) {
                    return in_array($evaluateFlow['evaluate_type'], [
                        Evaluation::TYPE_EVALUATOR,
                        Evaluation::TYPE_LEADER,
                    ]);
                })
            );
        }
        return $evaluations;
    }

    /**
     * TODO: implement here
     */
    function detail()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        $userId = $this->request->params['user_id'];

        /** @var  User $User */
        $User = ClassRegistry::init('User');
        $userEvaluatee = $User->findById($userId);

        // TODO: fetch evaluators
        $userEvaluators = [$userEvaluatee, $userEvaluatee, $userEvaluatee];

        $this->set('userEvaluatee', $userEvaluatee);
        $this->set('userEvaluateeCoach', $userEvaluatee);
        $this->set('userEvaluators', $userEvaluators);
    }
}
