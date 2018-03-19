<?php
App::uses('AppController', 'Controller');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluator', 'Model');
App::import('Service', 'ExperimentService');
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
        $this->layout = LAYOUT_ONE_COLUMN;

        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");
        if (!$ExperimentService->isDefined("EnableEvaluationFeature")) {
            throw new RuntimeException(__("Evaluation setting of the team is not enabled. Please contact the team administrator."));
        }

        parent::beforeFilter();
    }

    /**
     * Evaluation list of
     * self evaluation and coachee evaluation
     */
    function index()
    {

        /** @var  EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init('EvaluationService');

        $termId = $this->Team->Term->getCurrentTermId();
        $userId = $this->Auth->user('id');

        $selfEvaluation = $EvaluationService->getEvalStatus($termId, $userId);
        $evaluateesEvaluation = $EvaluationService->getEvaluateesFromCoachUserId($termId, $userId);

        $selfEvaluation = $this->extractEvaluatorsInFlow([$selfEvaluation])[0];
        $evaluateesEvaluation = $this->extractEvaluatorsInFlow($evaluateesEvaluation);

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
    private function extractEvaluatorsInFlow(array $evaluations): array
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
     * Evaluator setting page
     */
    function detail()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        $userId = $this->request->params['user_id'];
        $teamId = $this->current_team_id;

        /** @var  User $User */
        $User = ClassRegistry::init('User');
        /** @var  TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var  Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');

        $userEvaluatee = $User->findById($userId);

        // Fetching coach User
        $coachUserId = $TeamMember->getCoachUserIdByMemberUserId($userId);
        $userCoach = null;
        if (!empty($coachUserId)) {
            $userCoach = $User->findById($coachUserId);
        }

        // Fetching evaluatee's evaluators
        $evaluatorsIds = $Evaluator->getExistingEvaluatorsIds($teamId, $userId);
        $userEvaluators = [];
        foreach ($evaluatorsIds as $evaluatorsId) {
            $evaluator = $Evaluator->getById($evaluatorsId);
            $user = $User->findById($evaluator['evaluator_user_id']);
            if (!empty($user)) {
                $userEvaluators[] = $user;
            }
        }

        $this->set('userEvaluatee', $userEvaluatee);
        $this->set('userEvaluateeCoach', $userCoach);
        $this->set('userEvaluators', $userEvaluators);
    }
}
