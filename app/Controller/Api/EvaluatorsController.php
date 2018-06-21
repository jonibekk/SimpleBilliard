<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service', 'ExperimentService');
App::import('Service', "EvaluatorService");
App::uses('TeamMember', 'Model');

/**
 * Created by PhpStorm.
 * Example of controller using new design
 *
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 10:57
 *
 * @property NotifyBizComponent    $NotifyBiz
 * @property NotificationComponent Notification
 */
class EvaluatorsController extends BasePagingController
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
        switch ($this->getApiVersion()) {
            case '2':
                return $this->post_v2();
                break;
            default:
                break;
        }
    }

    private function post_v2()
    {
        //Validate data. If return errors, return them to REST consumer
        $errorReturn = $this->_validatePost();

        if (!empty($errorReturn)) {
            return $errorReturn;
        }

        $userId = $this->getUserId();
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

    protected function getPagingConditionFromRequest(CakeRequest $request): PagingCursor
    {
        // TODO: Implement getPagingConditionFromRequest() method.
    }

    protected function getResourceIdForCondition(): array
    {
        // TODO: Implement getResourceIdForCondition() method.
    }

    /**
     * Validate parameters prior to data manipulations
     *
     * @return  CakeResponse Return nothing if no error in validation
     */
    private function _validatePost(): CakeResponse
    {

        $data = $this->request['data'];

        //TODO get rule

        //TODO validate $data

        $userId = $this->getUserId();
        $evaluateeUserId = Hash::get($this->request->data, 'evaluatee_user_id');
        $evaluatorUserIds = Hash::get($this->request->data, 'evaluator_user_ids');

        //Check if team has evaluation feature
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");

        if (!$ExperimentService->isDefined(Experiment::NAME_ENABLE_EVALUATION_FEATURE)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage('Team has no evaluation feature')
                                                                       ->getResponse();
        }

        $teamId = $this->getTeamId();

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        //Check if user has authority to set evaluators
        if ($userId != $evaluateeUserId && $userId != $TeamMember->getCoachUserIdByMemberUserId($evaluateeUserId)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage(__('You have no permission.'))
                                                                       ->getResponse();
        }

        $inactiveUsersList = $this->User->filterUsersOnTeamActivity($teamId, $evaluatorUserIds, false);

        if (count($inactiveUsersList) > 0) {
            $connectorString = (count($inactiveUsersList) > 1) ? ' are ' : ' is ';
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage(__('%s %s inactive',
                implode(", ", Hash::extract($inactiveUsersList, '{n}.User.display_username')), $connectorString))
                                                                       ->getResponse();
        }
    }

}