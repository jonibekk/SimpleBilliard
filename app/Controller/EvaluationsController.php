<?php
App::uses('AppController', 'Controller');
App::uses('Evaluation', 'Model');

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
                throw new RuntimeException(__d('gl', "チームの評価設定が有効になっておりません。チーム管理者にお問い合わせください。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        // Set selected term
        $current_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $previous_term_id = $this->Team->EvaluateTerm->getPreviousTermId();
        $term_param = viaIsSet($this->request->params['named']['term']);
        $selected_term_name = $term_param ? $term_param : 'previous';
        $selected_tab_term_id = '';
        if ($selected_term_name == 'present') {
            $selected_tab_term_id = $current_term_id;
        }
        elseif ($selected_term_name == 'previous') {
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

    function view($evaluateTermId = null, $evaluateeId = null)
    {

        $this->layout = LAYOUT_ONE_COLUMN;
        $my_uid = $this->Auth->user('id');

        try {
            // check authorities
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__d('gl', "チームの評価設定が有効になっておりません。チーム管理者にお問い合わせください。"));
            }
            $this->Evaluation->checkAvailParameterInEvalForm($evaluateTermId, $evaluateeId);

            // get evaluation list
            $evaluationList = array_values($this->Evaluation->getEvaluations($evaluateTermId, $evaluateeId));
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
        }
        else {
            $totalList = [];
        }
        $goalList = $evaluationList;

        // set progress
        foreach ($goalList as $goalIndex => $goal) {
            foreach ($goal as $evalKey => $eval) {
                $goalList[$goalIndex][$evalKey]['Goal']['progress'] = $this->Evaluation->Goal->getProgress($eval['Goal']);
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

    /**
     * @param $evaluateeId
     * @param $evaluateTermId
     */
    function add($evaluateeId, $evaluateTermId)
    {
        $this->request->allowMethod('post', 'put');

        $status = viaIsSet($this->request->data['status']);
        $evalType = viaIsSet($this->request->data['Evaluation']['evaluate_type']);
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
            $savedMsg = $successMsg = __d('gl', "下書きを保存しました。");
        }
        elseif ($status == Evaluation::TYPE_STATUS_DONE) {
            //次の評価へ通知
            $next_evaluation_id = $this->Evaluation->getCurrentTurnEvaluationId($evaluateeId, $evaluateTermId);
            if ($next_evaluation_id) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR,
                                                 $next_evaluation_id);
            }
            if ($evalType == Evaluation::TYPE_ONESELF) {
                $savedMsg = __d('gl', "自己評価を確定しました。");

            }
            elseif ($evalType == Evaluation::TYPE_EVALUATOR) {
                $savedMsg = __d('gl', "評価者の評価を確定しました。");
            }
        }
        $this->Pnotify->outSuccess($savedMsg);
        return $this->redirect($this->referer());
    }

    public function ajax_get_incomplete_evaluatees()
    {
        $this->_ajaxPreProcess();
        $incomplete_evaluatees = $this->Evaluation->getIncompleteEvaluatees($this->Team->EvaluateTerm->getLatestTermId());
        $this->set(compact('incomplete_evaluatees'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_evaluatees');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_incomplete_evaluators()
    {
        $this->_ajaxPreProcess();
        $incomplete_evaluators = $this->Evaluation->getIncompleteEvaluators($this->Team->EvaluateTerm->getLatestTermId());
        $this->set(compact('incomplete_evaluators'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_evaluators');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_evaluators_status($evaluatee_id)
    {
        $this->_ajaxPreProcess();
        $evaluatee = $this->Evaluation->EvaluateeUser->findById($evaluatee_id);

        $res = $this->Evaluation->getEvaluators($this->Team->EvaluateTerm->getLatestTermId(), $evaluatee_id);
        $evaluators = Hash::sort($res, '{n}.Evaluation.index_num', 'desc');

        $this->set(compact('evaluators', 'evaluatee'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_evaluators_status');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_evaluatees_by_evaluator($evaluator_id)
    {
        $this->_ajaxPreProcess();
        $evaluator = $this->Evaluation->EvaluatorUser->findById($evaluator_id);

        $res = $this->Evaluation->getEvaluateesByEvaluator($this->Team->EvaluateTerm->getLatestTermId(), $evaluator_id);
        $incomplete_evaluatees = Hash::sort($res, '{n}.Evaluation.index_num', 'desc');
        $this->set(compact('incomplete_evaluatees', 'evaluator'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_evaluatees_by_evaluator');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_incomplete_oneself()
    {
        $this->_ajaxPreProcess();

        $oneself_incomplete_users = $this->Evaluation->getIncompleteOneselfEvaluators($this->Team->EvaluateTerm->getLatestTermId());
        $this->set(compact('oneself_incomplete_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Evaluation/modal_incomplete_oneself_evaluators');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

}
