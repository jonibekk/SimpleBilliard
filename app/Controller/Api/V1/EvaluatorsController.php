<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'ExperimentService');
App::import('Service', "EvaluatorService");
App::import('Service', "EvaluatorChangeLogService");
App::uses('TeamMember', 'Model');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 10:57
 *
 * @property NotifyBizComponent $NotifyBiz
 */
class EvaluatorsController extends ApiController
{

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
        $evaluatorUserIds = Hash::get($this->request->data, 'evaluator_user_ids');

        //Check if team has evaluation feature
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");

        if (!$ExperimentService->isDefined(Experiment::NAME_ENABLE_EVALUATION_FEATURE)) {
            return $this->_getResponseForbidden('Team has no evaluation feature');
        }

        // Validate parameters
        if (empty($evaluateeUserId)) {
            return $this->_getResponseBadFail('Invalid Parameters');
        }
        //If evaluatee user_id in evaluator array, send error
        if (in_array($userId, $evaluatorUserIds)) {
            return $this->_getResponseBadFail('Evaluatee ID in Evaluator IDs');
        }
        if (count($evaluatorUserIds) > self::MAX_NUMBER_OF_EVALUATORS) {
            return $this->_getResponseBadFail('Invalid evaluators ID; More than 7 IDs');
        }
        //Check duplicate
        foreach (array_values(array_count_values($evaluatorUserIds)) as $count) {
            if ($count > 1) {
                return $this->_getResponseBadFail('Invalid evaluators ID; More than 7 IDs');
            }
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
                return $this->_getResponseForbidden('User have no permission');
            }
        }

        /** @var EvaluatorService $EvaluatorService */
        $EvaluatorService = ClassRegistry::init("EvaluatorService");

        /** @var EvaluatorChangeLogService $EvaluatorChangeLogService */
        $EvaluatorChangeLogService = ClassRegistry::init("EvaluatorChangeLogService");

        $teamId = $this->current_team_id;

        $EvaluatorChangeLogService->saveLog($teamId, $evaluateeUserId, $userId);

        $EvaluatorService->setEvaluators($teamId, $evaluateeUserId, $evaluatorUserIds);

        if ($setByCoachFlag) {
            $this->_notifyUserOfEvaluatorToEvaluatee($teamId, $evaluateeUserId, $coachId);
        } else {
            $this->_notifyUserOfEvaluatorToCoach($teamId, $evaluateeUserId, $coachId);
        }

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
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUEE, null, null,
            (array)$userId, $coachId,
            $teamId);
    }

}