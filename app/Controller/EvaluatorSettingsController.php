<?php
App::uses('AppController', 'Controller');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluator', 'Model');
App::import('Service', 'ExperimentService');
App::import('Service', 'EvaluationService');
App::import('Service', 'EvaluatorService');

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
        /** @var  EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init('EvaluatorService');
        /** @var  User $User */
        $User = ClassRegistry::init('User');

        $termId = $this->Team->Term->getCurrentTermId();
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        $selfUser = $User->findById($userId);
        $selfUser['evaluators'] = $EvaluatorService->getEvaluatorsByTeamIdAndEvaluateeUserId($teamId, $userId);
        $coachees = $EvaluationService->getEvaluateesFromCoachUserId($termId, $userId);

        // Count zero evaluatee users
        $countOfZeroEvaluateeUsers = 0;
        foreach ($coachees as $key => $coachee) {
            $evaluators = $EvaluatorService->getEvaluatorsByTeamIdAndEvaluateeUserId($teamId, $coachee['User']['id']);
            $coachees[$key]['evaluators'] = $evaluators;
            if (0 === count($evaluators)) {
                $countOfZeroEvaluateeUsers++;
            }
        }

        $isFixedEvaluationOrder = $this->Team->EvaluationSetting->isFixedEvaluationOrder();

        $this->set('termId', $termId);
        $this->set('selfUser', $selfUser);
        $this->set('coachees', $coachees);
        $this->set('isFrozen', false);
        $this->set('isFixedEvaluationOrder', $isFixedEvaluationOrder);
        $this->set('countOfZeroEvaluateeUsers', $countOfZeroEvaluateeUsers);
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
        /** @var  EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init('EvaluatorService');

        $userEvaluatee = $User->findById($userId);

        // Fetching coach User
        $coachUserId = $TeamMember->getCoachUserIdByMemberUserId($userId);
        $userCoach = null;
        if (!empty($coachUserId)) {
            $userCoach = $User->findById($coachUserId);
        }

        // Fetching evaluatee's evaluators
        $userEvaluators = $EvaluatorService->getEvaluatorsByTeamIdAndEvaluateeUserId($teamId, $userId);

        /**@var EvaluatorChangeLog $EvaluatorChangeLog */
        $EvaluatorChangeLog = ClassRegistry::init('EvaluatorChangeLog');
        //$evaluatorChangeLog = $EvaluatorChangeLog->getLatestLogByUserIdAndTeamId($userId, $teamId);

        $this->set('userEvaluatee', $userEvaluatee);
        $this->set('userEvaluateeCoach', $userCoach);
        $this->set('userEvaluators', $userEvaluators);
        //$this->set('EvaluatorChangeLog', $evaluatorChangeLog);
    }
}
