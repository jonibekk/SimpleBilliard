<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'EvaluationService');
App::import('Service', 'EvaluationSetting');

use Goalous\Enum as Enum;

/**
 * Class TermsController
 *
 * @property NotificationComponent Notification
 */
class TermsController extends ApiController
{
    public $uses = [
        'Notification',
    ];

    /**
     * Starting the specified terms.id evaluation on loggin user
     *
     * /api/v1/terms/:termId/start_evaluation
     * @param int $termId
     *
     * @return CakeResponse
     */
    function post_start_evaluation($termId)
    {
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");

        $teamId = $this->current_team_id;

        try {
            $err = $this->validateStartEvaluation($termId);

            if ($err !== null) {
                return $err;
            }
            return;

            $EvaluationService->startEvaluation($teamId, $termId);

            // 評価期間判定キャッシュ削除
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_IS_STARTED_EVALUATION, true), 'team_info');

            $this->Notification->outSuccess(__("Evaluation started."));
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_START, $termId);
            Cache::clear(false, 'team_info');
        } catch (Exception $e) {
            CustomLogger::getInstance()->logException($e);
            $this->Notification->outError(__("Evaluation could not start."));
            return $this->_getResponseInternalServerError();
        }
        return $this->_getResponseSuccess();
    }

    function validateStartEvaluation($termId) {
        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        if (!$EvaluationSetting->isEnabled()) {
            $err =  'Evaluation setting is not active.';
            $this->Notification->outError(__($err));
            return $this->_getResponseBadFail(__($err));
        }

        if ($Term->isStartedEvaluation($termId)) {
            $err = 'The evaluation for this term has already been started.';
            $this->Notification->outError(__($err));
            return $this->_getResponseBadFail(__($err));
        }

        $unapprovedGoals = $GoalMember->getUnapprovedForTerm($termId);

        if (count($unapprovedGoals) > 0) {
            $this->renderUnapprovedGoalsModal($termId, count($unapprovedGoals));
        }

        return null;
    }

    function renderUnapprovedGoalsModal(int $termId, int $countUnapprovedGoals)
    {
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
        $this->set(compact('termId', 'countUnapprovedGoals'));
        $response = $this->render('Evaluation/modal_unapproved_goals');
        $html = $response->__toString();
        return $this->_getResponse(400, ['modalContent' => $html]);
    }
}
