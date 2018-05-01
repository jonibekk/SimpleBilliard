<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'ExperimentService');
App::import('Service', "EvaluatorService");
App::uses('TeamMember', 'Model');

/**
 * Created by PhpStorm.
 *
 * Example of controller extending ApiV2Controller
 *
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 10:57
 *
 * @property NotifyBizComponent    $NotifyBiz
 * @property NotificationComponent Notification
 */
class EvaluatorsController extends ApiV2Controller
{
    public $components = [
        'Notification',
    ];

    const MAX_NUMBER_OF_EVALUATORS = 7;

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     *
     */
    public function post()
    {
        try {
            $this->_validatePost();
        } catch (Exception $exception) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->setMessage($exception->getMessage())
                                                                       ->setExceptionTrace($exception->getTrace())
                                                                       ->getResponse();
        }

        $userId = $this->getUser()['id'];
        $teamId = $this->getTeamId();
        $evaluateeUserId = Hash::get($this->request->data, 'evaluatee_user_id');
        $evaluatorUserIds = Hash::get($this->request->data, 'evaluator_user_ids');

        /** @var EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init("EvaluatorService");

        $EvaluatorService->setEvaluators($teamId, $evaluateeUserId, $evaluatorUserIds, $userId);

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $coachId = $TeamMember->getCoachUserIdByMemberUserId($evaluateeUserId);
        $isUserCoach = $userId == $coachId;

        if (!empty($coachId)) {
            if ($isUserCoach) {
                $this->_notifyUserOfEvaluatorToEvaluatee($teamId, $evaluateeUserId, $coachId);
            } else {
                $this->_notifyUserOfEvaluatorToCoach($teamId, $evaluateeUserId, $coachId);
            }
        }
        $this->Notification->outSuccess(__("Evaluator setting saved."));

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))
            ->getResponse();

    }

    /**
     * Send notification to evaluatee's coach when evaluaee set his/her evaluators
     *
     * @param int $teamId
     * @param int $userId
     * @param int $coachId
     */
    private function _notifyUserOfEvaluatorToCoach(
        int $teamId,
        int $userId,
        int $coachId
    ) {
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_EVALUATOR_SET_TO_COACH, null, null,
            (array)$coachId,
            $userId,
            $teamId);
    }

    /**
     * Send to notification to evaluatee when evaluatee's coach set his/her evaluators
     *
     * @param int $teamId
     * @param int $userId
     * @param int $coachId
     */
    private function _notifyUserOfEvaluatorToEvaluatee(
        int $teamId,
        int $userId,
        int $coachId
    ) {
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUATEE, null, null,
            (array)$userId, $coachId,
            $teamId);
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function _validatePost(): bool
    {

        $data = $this->request['data'];

        //TODO get rule

        //TODO validate $data

        $userId = $this->getUser()['id'];
        $evaluateeUserId = Hash::get($this->request->data, 'evaluatee_user_id');
        $evaluatorUserIds = Hash::get($this->request->data, 'evaluator_user_ids');

        //Check if team has evaluation feature
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");

        if (!$ExperimentService->isDefined(Experiment::NAME_ENABLE_EVALUATION_FEATURE)) {
            throw new Exception('Team has no evaluation feature');
        }

        $teamId = $this->getTeamId();

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        //Check if user has authority to set evaluators
        if ($userId != $evaluateeUserId && $userId != $TeamMember->getCoachUserIdByMemberUserId($evaluateeUserId)) {
            throw new Exception(__('You have no permission.'));
        }

        $inactiveUsersList = $this->User->filterUsersOnTeamActivity($teamId, $evaluatorUserIds, false);

        if (count($inactiveUsersList) > 0) {
            $connectorString = (count($inactiveUsersList) > 1) ? ' are ' : ' is ';
            throw new Exception(__('%s %s inactive',
                implode(", ", Hash::extract($inactiveUsersList, '{n}.User.display_username')), $connectorString));
        }

        //TODO return result

    }

}