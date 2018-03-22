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

        $authUserId = $this->Auth->user('id');
        $evaluateeUserId = $this->request->params['user_id'];
        $teamId = $this->current_team_id;

        /** @var  User $User */
        $User = ClassRegistry::init('User');
        /** @var  TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var  Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');
        /** @var  EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init('EvaluatorService');
        /** @var  EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init('EvaluationService');

        // Check auth user have authority to see this page.
        $termId = $this->Team->Term->getCurrentTermId();
        $coachees = $EvaluationService->getEvaluateesFromCoachUserId($termId, $authUserId);
        $usersIdsCanView = array_merge(Hash::extract($coachees, '{n}.User.id'), [$authUserId]);
        if (!in_array($evaluateeUserId, $usersIdsCanView)) {
            throw new RuntimeException(__("You have no permission."));
        }

        $userEvaluatee = $User->findById($evaluateeUserId);

        // Fetching coach User
        $coachUserId = $TeamMember->getCoachUserIdByMemberUserId($evaluateeUserId);
        $userCoach = null;
        if (!empty($coachUserId)) {
            $userCoach = $User->findById($coachUserId);
        }

        // Fetching evaluatee's evaluators
        $userEvaluators = $EvaluatorService->getEvaluatorsByTeamIdAndEvaluateeUserId($teamId, $evaluateeUserId);

        /**@var EvaluatorChangeLog $EvaluatorChangeLog */
        $EvaluatorChangeLog = ClassRegistry::init('EvaluatorChangeLog');
        $latestEvaluatorChangeLog = $EvaluatorChangeLog->getLatestLogByUserIdAndTeamId($teamId, $evaluateeUserId);
        if (!empty($latestEvaluatorChangeLog['last_update_user_id'])) {
            $latestUpdateUser = $User->getById($latestEvaluatorChangeLog['last_update_user_id']);
            if (!empty($latestUpdateUser)) {
                $latestEvaluatorChangeLog['User'] = $latestUpdateUser;
            }
            $updateTime = GoalousDateTime::createFromTimestamp($latestEvaluatorChangeLog['created']);
            $updateTime->setTimeZoneTeam();
            $latestEvaluatorChangeLog['display_update_time'] = $updateTime->format('Y-m-d H:i:s');
        }

        $this->set('userEvaluatee', $userEvaluatee);
        $this->set('userEvaluateeCoach', $userCoach);
        $this->set('userEvaluators', $userEvaluators);
        $this->set('latestEvaluatorChangeLog', $latestEvaluatorChangeLog);
    }
}
