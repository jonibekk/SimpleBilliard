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
        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");
        /** @var  Term $Term */
        $Term = ClassRegistry::init('Term');

        $teamId = $this->current_team_id;

        try {
            if (!$EvaluationSetting->isEnabled()) {
                $this->_getResponseBadFail(__("Evaluation setting is not active."));
            }

            if ($Term->isStartedEvaluation($termId)) {
                $this->_getResponseBadFail(__("The evaluation for this term has already been started."));
            }

            $EvaluationService->startEvaluation($teamId, $termId);

            // 評価期間判定キャッシュ削除
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_IS_STARTED_EVALUATION, true), 'team_info');

            $this->Notification->outSuccess(__("Evaluation started."));
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_START, $termId);
            Cache::clear(false, 'team_info');
        } catch (Exception $e) {
            GoalousLog::error('failed on starting evaluation', [
                'message' => $e->getMessage(),
            ]);
            GoalousLog::error($e->getTraceAsString());
            $this->Notification->outError(__("Evaluation could not start."));
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }
}
