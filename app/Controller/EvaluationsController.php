<?php
App::uses('AppController', 'Controller');
App::uses('Evaluation', 'Model');

/**
 * Evaluations Controller
 *
 * @property Evaluation $Evaluation
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

        //get evaluation term
        $term_param = viaIsSet($this->request->params['named']['term']);
        $term_name = $term_param ? $term_param : 'previous';
        switch ($term_name) {
            case 'present':
                $selected_tab_term_id = $current_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
                break;
            case 'previous':
                $selected_tab_term_id = $this->Team->EvaluateTerm->getPreviousTermId();
                break;
        }

        $incomplete_number_list = $this->Evaluation->getIncompleteNumberList();
        $my_eval[] = $this->Evaluation->getEvalStatus($selected_tab_term_id, $this->Auth->user('id'));
        $my_evaluatees = $this->Evaluation->getEvaluateeEvalStatusAsEvaluator($selected_tab_term_id);
        $this->set(compact('incomplete_number_list',
                           'my_evaluatees',
                           'my_eval',
                           'selected_tab_term_id',
                           'term_name'
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

    function add()
    {
        $this->request->allowMethod('post', 'put');

        // case of saving draft
        if (isset($this->request->data['is_draft'])) {
            $saveType = "draft";
            unset($this->request->data['is_draft']);
            $successMsg = __d('gl', "下書きを保存しました。");

            // case of registering
        }
        else {
            $saveType = "register";
            unset($this->request->data['is_register']);
            $successMsg = __d('gl', "自己評価を登録しました。");
        }

        // 保存処理実行
        try {
            $this->Evaluation->begin();
            $this->Evaluation->add($this->request->data, $saveType);
        } catch (RuntimeException $e) {
            $this->Evaluation->rollback();
            // saving as draft
            if ($saveType === "register") {
                $this->Evaluation->add($this->request->data, "draft");
            }
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->Evaluation->commit();
        $this->Pnotify->outSuccess($successMsg);
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

}
