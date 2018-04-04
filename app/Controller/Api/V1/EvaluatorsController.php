<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'ExperimentService');
App::import('Service', "EvaluatorService");
App::uses('TeamMember', 'Model');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 10:57
 *
 * @property NotifyBizComponent $NotifyBiz
 * @property NotificationComponent Notification
 */
class EvaluatorsController extends ApiController
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
        $userId = $this->Auth->user('id');
        $evaluateeUserId = Hash::get($this->request->data, 'evaluatee_user_id');
        $evaluatorUserIds = Hash::get($this->request->data, 'evaluator_user_ids') ?? [];

        //Check if team has evaluation feature
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");

        if (!$ExperimentService->isDefined(Experiment::NAME_ENABLE_EVALUATION_FEATURE)) {
            return $this->_getResponseForbidden('Team has no evaluation feature');
        }

        // Validate parameters
        if (!$this->User->validateUserId($evaluateeUserId)) {
            return $this->_getResponseBadFail(__('Parameter is invalid'));
        }
        //If evaluatee user_id in evaluator array, send error
        if (in_array($evaluateeUserId, $evaluatorUserIds)) {
            return $this->_getResponseBadFail(__('Evaluatee cannot be assigned as his/her own evaluator.'));
        }
        if (count($evaluatorUserIds) > self::MAX_NUMBER_OF_EVALUATORS) {
            return $this->_getResponseBadFail(__('Evaluator setting cannot be saved.'));
        }
        // Check for invalid evaluator user IDs
        foreach ($evaluatorUserIds as $evaluatorId) {
            if (!$this->User->validateUserId($evaluatorId)) {
                return $this->_getResponseBadFail(__('Parameter is invalid'));
            }
        }
        //Check duplicate
        foreach (array_values(array_count_values($evaluatorUserIds)) as $count) {
            if ($count > 1) {
                return $this->_getResponseBadFail(__('Evaluator has duplicates.'));
            }
        }

        $teamId = $this->current_team_id;

        $inactiveUsersList = $this->User->filterUsersOnTeamActivity($teamId, $evaluatorUserIds, false);

        if (count($inactiveUsersList) > 0) {
            $connectorString = (count($inactiveUsersList) > 1) ? ' are ' : ' is ';
            return $this->_getResponseBadFail(__('%s %s inactive',
                implode(", ", Hash::extract($inactiveUsersList, '{n}.User.display_username')), $connectorString));
        }

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $coachId = $TeamMember->getCoachUserIdByMemberUserId($evaluateeUserId);
        $setByCoachFlag = false;

        //Check if user has authority to set evaluators
        if ($userId != $evaluateeUserId) {
            if ($userId == $coachId) {
                $setByCoachFlag = true;
            } else {
                return $this->_getResponseForbidden(__('You have no permission.'));
            }
        }

        /** @var EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init("EvaluatorService");

        $EvaluatorService->setEvaluators($teamId, $evaluateeUserId, $evaluatorUserIds, $userId);

        if (!empty($coachId)) {
            if ($setByCoachFlag) {
                $this->_notifyUserOfEvaluatorToEvaluatee($teamId, $evaluateeUserId, $coachId);
            } else {
                $this->_notifyUserOfEvaluatorToCoach($teamId, $evaluateeUserId, $coachId);
            }
        }
        $this->Notification->outSuccess(__("Evaluator setting saved."));

        return $this->_getResponseSuccess();

    }

    /**
     * Send notification to evaluatee's coach when evaluaee set his/her evaluators
     *
     * @param int $teamId
     * @param int $userId
     * @param int $coachId
     */
    private
    function _notifyUserOfEvaluatorToCoach(
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
    private
    function _notifyUserOfEvaluatorToEvaluatee(
        int $teamId,
        int $userId,
        int $coachId
    ) {
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUATEE, null, null,
            (array)$userId, $coachId,
            $teamId);
    }

}