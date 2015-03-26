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

        $incomplete_count = 0;
        //get evaluation setting.
        $eval_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $my_eval_status = $this->Evaluation->getMyEvalStatus($eval_term_id);
        $is_myself_evaluations_incomplete = $this->Evaluation->isMySelfEvalIncomplete($eval_term_id);
        if ($is_myself_evaluations_incomplete) {
            $incomplete_count++;
        }

        $this->set(compact('eval_term_id', 'incomplete_count', 'is_myself_evaluations_incomplete', 'my_eval_status'));
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
        
        $evaluationList = array_values($this->Evaluation->getEvaluations($evaluateTermId, $evaluateeId));
        
        $teamId = $this->Session->read('current_team_id');
        $evaluateType = $this->Evaluation->getEvaluateType($evaluateTermId, $evaluateeId);
        $scoreList = [null => "選択してください"] + $this->Evaluation->EvaluateScore->getScoreList($teamId);
        $status = $this->Evaluation->getStatus($evaluateTermId, $evaluateeId, $this->Auth->user('id'));
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
                $goalList[$goalIndex][$evalKey]['Goal']['progress'] = $this->Evaluation->Goal->getProgress($eval['Goal']);
            }
        }
        $this->set(compact('scoreList', 'totalList', 'goalList', 'evaluateTermId', 'evaluateeId', 'evaluateType', 'status', 'saveIndex'));
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
