<?php
App::uses('AppController', 'Controller');
App::uses('Evaluation', 'Model');
App::import('Service', 'GoalService');
App::import('Service', 'EvaluationService');

/**
 * Evaluations Controller
 *
 * @property Evaluation        $Evaluation
 * @property EvaluationSetting $EvaluationSetting
 * @var                        $selectedTabTermId
 */
class EvaluationsController extends AppController
{
    public $uses = [
        'Evaluation',
        'EvaluationSetting'
    ];

    function beforeFilter()
    {
        parent::beforeFilter();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->Security->enabled = false;
    }

    function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $userId = $this->Auth->user('id');
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__("Evaluation setting of the team is not enabled. Please contact the team administrator."));
            }

            // 評価期間ID取得
            $termId = Hash::get($this->request->query, 'term_id');

            // 全評価期間取得
            $termsForFilter = $this->Team->Term->findEvaluationStartedTerms();
            $currentTermId = $this->Team->Term->getCurrentTermId();
            // if current evaluation is not started, add current term
            if (!$this->Team->Term->isStartedEvaluation($currentTermId)) {
                $currentTerm = $this->Team->Term->getCurrentTermData();
                array_unshift($termsForFilter, $currentTerm);
            }
            $allTermIds = Hash::extract($termsForFilter, '{n}.id');

            // 存在しない評価期間を指定した場合エラー
            if (!empty($termId) && !in_array($termId, $allTermIds)) {
                throw new RuntimeException(__("The specified period is incorrect."));
            }

            // decide default term id
            if (empty($termId)) {
                // as temporary, set current term id.
                $termId = $this->Team->Term->getCurrentTermId();
                // if previous my turn count, previous term is default. otherwise, current term is default
                $prevTermId = $this->Team->Term->getPreviousTermId();
                if ($prevTermId) {
                    $prevMyTurnCount = $this->Evaluation->getMyTurnCount(null, $prevTermId);
                    if ($prevMyTurnCount > 0) {
                        $termId = $prevTermId;
                    }
                }
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        // 評価期間選択用ラベル取得
        $termLabels = $this->_getTermLabels($termsForFilter);

        /** @var  EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init('EvaluationService');

        $incompSelfEvalCnt = (int)$this->Evaluation->getMyTurnCount(Evaluation::TYPE_ONESELF, $termId, false);
        $incompEvaluateeEvalCnt = (int)$this->Evaluation->getMyTurnCount(Evaluation::TYPE_EVALUATOR, $termId, false);
        $selfEval = $EvaluationService->getEvalStatus($termId, $userId);
        $evaluateesEval = $EvaluationService->getEvaluateeEvalStatusAsEvaluator($termId);
        // 該当期間が評価開始されているか
        $isStartedEvaluation = $this->Team->Term->isStartedEvaluation($termId);

        // Get term frozen status
        $isFrozen = $this->Team->Term->checkFrozenEvaluateTerm($termId);
        $isFixedEvaluationOrder = $this->Team->EvaluationSetting->isFixedEvaluationOrder();

        $this->set(compact(
            'termId',
            'termLabels',
            'incompSelfEvalCnt',
            'incompEvaluateeEvalCnt',
            'selfEval',
            'evaluateesEval',
            'isFrozen',
            'isStartedEvaluation',
            'isFixedEvaluationOrder'
        ));
    }

    /**
     * 評価期間選択用ラベルを取得
     *
     * @param $terms
     *
     * @return array
     */
    private function _getTermLabels($terms)
    {
        if (!is_array($terms)) {
            return [];
        }
        $termLabels = [];

        $currentTermId = $this->Team->Term->getCurrentTermId();
        $previousTermId = $this->Team->Term->getPreviousTermId();

        foreach ($terms as $term) {
            $termId = $term['id'];
            if ($termId == $currentTermId) {
                $termLabels[$termId] = __("Current Term");
            } elseif ($termId == $previousTermId) {
                $termLabels[$termId] = __("Previous Term");
            } else {
                $fmtStartDate = AppUtil::dateYmdReformat($term['start_date'], "/");
                $fmtEndDate = AppUtil::dateYmdReformat($term['end_date'], "/");
                $termLabels[$term['id']] = $fmtStartDate . " - " . $fmtEndDate;
            }
        }
        return $termLabels;
    }

    function view()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");
        $evaluateeId = Hash::get($this->request->params, 'named.user_id');
        $evaluateTermId = Hash::get($this->request->params, 'named.evaluate_term_id');
        $this->layout = LAYOUT_ONE_COLUMN;
        $userId = $this->Auth->user('id');

        try {
            // check authorities
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__("Evaluation setting of the team is not enabled. Please contact the team administrator."));
            }
            $this->Evaluation->checkAvailParameterInEvalForm($evaluateTermId, $evaluateeId);

            $evaluationList = array_values($EvaluationService->findEvaluations($userId, $evaluateeId, $evaluateTermId));

            // order by priority
            //TODO: このコードは一時的なもの(今後は評価開始時に既にソート済になるので削除予定)
            $order_priority_list = [];
            foreach ($evaluationList as $k => $v) {
                $order_priority_list[$k] = 0;
                if ($k === 0) {
                    //first item is total evaluation
                    $order_priority_list[$k] = 999;
                    continue;
                }
                if (isset(reset($v)['Goal']['MyCollabo'][0]['priority'])) {
                    $order_priority_list[$k] = reset($v)['Goal']['MyCollabo'][0]['priority'];
                }
            }
            array_multisort($order_priority_list, SORT_DESC, SORT_NUMERIC, $evaluationList);
            //TODO 削除ここまで

            $isEditable = $EvaluationService->isEditable($evaluateTermId, $evaluateeId, $userId);
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $evaluateType = $this->Evaluation->getEvaluateType($evaluateTermId, $evaluateeId);
        $scoreList = $this->Evaluation->EvaluateScore->getScoreList($this->Session->read('current_team_id'));
        $status = $this->Evaluation->getStatus($evaluateTermId, $evaluateeId, $userId);
        $saveIndex = 0;

        $existTotalEval = in_array(null, Hash::extract($evaluationList[0], '{n}.Evaluation.goal_id'));
        if ($existTotalEval) {
            $totalList = array_shift($evaluationList);
        } else {
            $totalList = [];
        }
        $goalList = $evaluationList;

        // set progress
        foreach ($goalList as $goalIndex => $goal) {
            foreach ($goal as $evalKey => $eval) {
                $keyResults = Hash::get($eval, 'Goal.KeyResult');
                $goalList[$goalIndex][$evalKey]['Goal']['progress'] = $GoalService->calcProgressByOwnedPriorities($keyResults);
            }
        }

        $this->set(compact('scoreList',
            'totalList',
            'goalList',
            'evaluateTermId',
            'evaluateeId',
            'evaluateType',
            'status',
            'saveIndex',
            'isEditable'
        ));
    }

    function add()
    {
        $evaluateeId = Hash::get($this->request->params, 'named.user_id');
        $evaluateTermId = Hash::get($this->request->params, 'named.evaluate_term_id');

        $this->request->allowMethod('post', 'put');

        $status = Hash::get($this->request->data, 'status');
        $evalType = Hash::get($this->request->data, 'Evaluation.evaluate_type');
        unset($this->request->data['status']);
        unset($this->request->data['Evaluation']);
        // 保存処理実行
        try {
            $this->Evaluation->begin();
            $this->Evaluation->add($this->request->data, $status);
        } catch (RuntimeException $e) {
            $this->Evaluation->rollback();
            // saving as draft
            if ($status === Evaluation::TYPE_STATUS_DONE) {
                $this->Evaluation->add($this->request->data, Evaluation::TYPE_STATUS_DRAFT);
            }
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->Evaluation->commit();

        // Set saved message
        $savedMsg = "";
        $redirectDestination = "";
        if ($status == Evaluation::TYPE_STATUS_DRAFT) {
            $savedMsg = $successMsg = __("Saved the draft.");
            $redirectDestination = "/evaluations/view/evaluate_term_id:$evaluateTermId/user_id:$evaluateeId";
        } elseif ($status == Evaluation::TYPE_STATUS_DONE) {
            $this->notifyAndDelCache($evaluateeId, $evaluateTermId);
            if ($evalType == Evaluation::TYPE_ONESELF) {
                $savedMsg = __("Submitted the self-evaluation.");
            } elseif ($evalType == Evaluation::TYPE_EVALUATOR) {
                $savedMsg = __("Submitted the evaluation by evaluator.");
            }
            $redirectDestination = "/evaluations?term_id=$evaluateTermId";
        }
        $this->Notification->outSuccess($savedMsg);
        return $this->redirect($redirectDestination ?: $this->referer());
    }

    /**
     * @param int $evaluateeId
     * @param int $evaluateTermId
     */
    private function notifyAndDelCache(int $evaluateeId, int $evaluateTermId)
    {
        if ($this->EvaluationSetting->isFixedEvaluationOrder()) {
            $this->notifyAndDelCacheIfFixedEvalOrder($evaluateeId, $evaluateTermId);
        } else {
            $this->notifyAndDelCacheIfNotFixedEvalOrder($evaluateeId, $evaluateTermId);
        }
    }

    /**
     * @param int $evaluateeId
     * @param int $evaluateTermId
     */
    private function notifyAndDelCacheIfFixedEvalOrder(int $evaluateeId, int $evaluateTermId)
    {
        //次の評価へ通知
        $next_evaluation_id = $this->Evaluation->getCurrentTurnEvaluationId($evaluateeId, $evaluateTermId);
        $is_final_evaluation = $this->Evaluation->isThisEvaluateType($next_evaluation_id,
            Evaluation::TYPE_FINAL_EVALUATOR);
        //キャッシュ削除
        $next_evaluator_id = $this->Evaluation->getNextEvaluatorId($evaluateTermId, $evaluateeId);
        Cache::delete($this->Evaluation->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true), 'team_info');
        Cache::delete($this->Evaluation->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true, $next_evaluator_id),
            'team_info');

        if ($next_evaluation_id && !$is_final_evaluation) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR,
                $next_evaluation_id);
        }
    }

    /**
     * @param int $evaluateeId
     * @param int $evaluateTermId
     */
    private function notifyAndDelCacheIfNotFixedEvalOrder(int $evaluateeId, int $evaluateTermId)
    {
        $evaluatorEvals = $this->Evaluation->getEvaluatorsByEvaluatee($evaluateTermId, $evaluateeId);
        if ($evaluateeId == $this->Auth->user('id')) {
            foreach ($evaluatorEvals as $v) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR, $v['id']);
            }

        }
        Cache::delete($this->Evaluation->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true), 'team_info');

        foreach ($evaluatorEvals as $v) {
            Cache::delete($this->Evaluation->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true, $v['evaluator_user_id']),
                'team_info');
        }
    }

    public function ajax_get_incomplete_evaluatees()
    {
        $this->_ajaxPreProcess();
        $evaluate_term_id = $this->request->params['named']['evaluate_term_id'];
        $incomplete_evaluatees = $this->Evaluation->getIncompleteEvaluatees($evaluate_term_id);
        $this->set(compact('incomplete_evaluatees', 'evaluate_term_id'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_evaluatees');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_incomplete_evaluators()
    {
        $this->_ajaxPreProcess();
        $evaluate_term_id = $this->request->params['named']['evaluate_term_id'];
        $incomplete_evaluators = $this->Evaluation->getIncompleteEvaluators($evaluate_term_id);
        $this->set(compact('incomplete_evaluators', 'evaluate_term_id'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_evaluators');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_evaluators_status()
    {
        $evaluatee_id = Hash::get($this->request->params, 'named.user_id');
        $this->_ajaxPreProcess();
        $evaluatee = $this->Evaluation->EvaluateeUser->findById($evaluatee_id);

        $evaluate_term_id = $this->request->params['named']['evaluate_term_id'];
        $res = $this->Evaluation->getEvaluators($evaluate_term_id, $evaluatee_id);
        $evaluators = Hash::sort($res, '{n}.Evaluation.index_num', 'desc');

        $this->set(compact('evaluators', 'evaluatee'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_evaluators_status');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_evaluatees_by_evaluator()
    {
        $evaluator_id = Hash::get($this->request->params, 'named.user_id');
        $this->_ajaxPreProcess();
        $evaluator = $this->Evaluation->EvaluatorUser->findById($evaluator_id);

        $evaluate_term_id = $this->request->params['named']['evaluate_term_id'];
        $res = $this->Evaluation->getEvaluateesByEvaluator($evaluate_term_id, $evaluator_id);
        $incomplete_evaluatees = Hash::sort($res, '{n}.Evaluation.index_num', 'desc');
        $this->set(compact('incomplete_evaluatees', 'evaluator', 'evaluate_term_id'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_evaluatees_by_evaluator');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_incomplete_oneself()
    {
        $this->_ajaxPreProcess();

        $term_id = $this->request->params['named']['evaluate_term_id'];
        $oneself_incomplete_users = $this->Evaluation->getIncompleteOneselfEvaluators($term_id);
        $this->set(compact('oneself_incomplete_users', 'evaluate_term_id'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_oneself_evaluators');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

}
