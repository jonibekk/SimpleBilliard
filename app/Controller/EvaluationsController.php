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

        //get evaluation setting.
        $eval_term_id = $this->Team->EvaluateTerm->getLatestTermId();
        $my_eval_status = $this->Evaluation->getEvalStatus($eval_term_id, $this->Auth->user('id'));
        $total_incomplete_count = $this->Team->Evaluation->getMyTurnCount();
        $is_myself_evaluations_incomplete = $this->Evaluation->isMySelfEvalIncomplete($eval_term_id);
        $total_incomplete_count_as_evaluator = $total_incomplete_count;
        if ($is_myself_evaluations_incomplete) {
            $total_incomplete_count_as_evaluator--;
        }
        $evaluatees = $this->Evaluation->getEvaluateeEvalStatusAsEvaluator($eval_term_id);
        $this->set(compact('total_incomplete_count_as_evaluator', 'eval_term_id', 'evaluatees',
                           'total_incomplete_count', 'is_myself_evaluations_incomplete',
                           'my_eval_status'));
    }

    function view($evaluateTermId = null, $evaluateeId = null)
    {

        $this->layout = LAYOUT_ONE_COLUMN;

        try {
            $this->Evaluation->checkAvailViewEvaluationList();
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__d('gl', "チームの評価設定が有効になっておりません。チーム管理者にお問い合わせください。"));
            }
            $this->Evaluation->checkAvailParameterInEvalForm($evaluateTermId, $evaluateeId);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $teamId = $this->Session->read('current_team_id');
        $scoreList = [null => "選択してください"] + $this->Evaluation->EvaluateScore->getScoreList($teamId);
        $evaluationList = $this->Evaluation->getEditableEvaluations($evaluateTermId, $evaluateeId);
        $status = $this->Evaluation->getStatus($evaluateTermId, $evaluateeId, $this->Auth->user('id'));
        if (empty($evaluationList[0]['Evaluation']['goal_id'])) {
            $total = $evaluationList[0];
            unset($evaluationList[0]);
            $goalList = $evaluationList;
        }
        else {
            $total = [];
            $goalList = $evaluationList;
        }

        // set progress
        foreach ($goalList as $key => $val) {
            $goalList[$key]['Goal']['progress'] = $this->Evaluation->Goal->getProgress($val['Goal']);
        }
        $this->set(compact('scoreList', 'total', 'goalList', 'evaluateTermId', 'evaluateeId', 'status'));
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

}
