<?php
App::uses('AppController', 'Controller');
App::uses('Evaluation', 'Model');
App::import('Service', 'GoalService');

/**
 * Evaluations Controller
 *
 * @property Evaluation $Evaluation
 * @var                 $selected_tab_term_id
 */
class EvaluationsController extends AppController
{

    function beforeFilter()
    {
        parent::beforeFilter();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->Security->enabled = false;
    }

    function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__("Evaluation setting of the team is not enabled. Please contact the team administrator."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        // Set selected term
        $current_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $previous_term_id = $this->Team->EvaluateTerm->getPreviousTermId();
        $term_param = Hash::get($this->request->params, 'named.term');
        $selected_term_name = $term_param ? $term_param : 'previous';
        $selected_tab_term_id = '';
        if ($selected_term_name == 'present') {
            $selected_tab_term_id = $current_term_id;
        } elseif ($selected_term_name == 'previous') {
            $selected_tab_term_id = $previous_term_id;
        }

        $incomplete_number_list = $this->Evaluation->getIncompleteNumberList();
        $my_eval[] = $this->Evaluation->getEvalStatus($selected_tab_term_id, $this->Auth->user('id'));
        $my_evaluatees = $this->Evaluation->getEvaluateeEvalStatusAsEvaluator($selected_tab_term_id);

        // Get term frozen status
        $isFrozens = [];
        $isFrozens['present'] = $this->Team->EvaluateTerm->checkFrozenEvaluateTerm($current_term_id);
        $isFrozens['previous'] = $this->Team->EvaluateTerm->checkFrozenEvaluateTerm($previous_term_id);

        $this->set(compact('incomplete_number_list',
            'my_evaluatees',
            'my_eval',
            'selected_tab_term_id',
            'selected_term_name',
            'isFrozens'
        ));
    }

    function view()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        $evaluateeId = Hash::get($this->request->params, 'named.user_id');
        $evaluateTermId = Hash::get($this->request->params, 'named.evaluate_term_id');
        $this->layout = LAYOUT_ONE_COLUMN;
        $my_uid = $this->Auth->user('id');

        try {
            // check authorities
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__("Evaluation setting of the team is not enabled. Please contact the team administrator."));
            }
            $this->Evaluation->checkAvailParameterInEvalForm($evaluateTermId, $evaluateeId);

            // get evaluation list
            $evaluationList = array_values($this->Evaluation->getEvaluations($evaluateTermId, $evaluateeId));

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

            $isEditable = $this->Evaluation->getIsEditable($evaluateTermId, $evaluateeId);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $evaluateType = $this->Evaluation->getEvaluateType($evaluateTermId, $evaluateeId);
        $scoreList = $this->Evaluation->EvaluateScore->getScoreList($this->Session->read('current_team_id'));
        $status = $this->Evaluation->getStatus($evaluateTermId, $evaluateeId, $my_uid);
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
                $goalList[$goalIndex][$evalKey]['Goal']['progress'] = $GoalService->getProgress(Hash::get($eval,
                    'Goal.KeyResult'));
            }
        }
        //remove unnecessary KRs
        foreach ($goalList as $goalIndex => $goal) {
            foreach ($goal as $evalKey => $eval) {
                if (!empty($eval['Goal']['KeyResult'])) {
                    foreach ($eval['Goal']['KeyResult'] as $kr_k => $kr) {
                        if (empty($kr['ActionResult'])) {
                            unset($goalList[$goalIndex][$evalKey]['Goal']['KeyResult'][$kr_k]);
                        }
                    }
                }
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
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->Evaluation->commit();

        // Set saved message
        $savedMsg = "";
        if ($status == Evaluation::TYPE_STATUS_DRAFT) {
            $savedMsg = $successMsg = __("Saved the draft.");
        } elseif ($status == Evaluation::TYPE_STATUS_DONE) {
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
            $mixpanel_member_type = null;
            if ($evalType == Evaluation::TYPE_ONESELF) {
                $savedMsg = __("Submitted the self-evaluation.");
                $mixpanel_member_type = MixpanelComponent::PROP_EVALUATION_MEMBER_SELF;

            } elseif ($evalType == Evaluation::TYPE_EVALUATOR) {
                $savedMsg = __("Submitted the evaluation by evaluator.");
                $mixpanel_member_type = MixpanelComponent::PROP_EVALUATION_MEMBER_EVALUATOR;
            }
            $this->Mixpanel->trackEvaluation($mixpanel_member_type);
        }
        $this->Pnotify->outSuccess($savedMsg);
        return $this->redirect($this->referer());
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
