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
        if (!$evaluateTermId || !$evaluateeId) {
            $this->Pnotify->outError(__d('gl', "パラメータが不正です。"));
            return $this->redirect($this->referer());
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        $teamId = $this->Session->read('current_team_id');
        $scoreList = [null => "選択してください"] + $this->Evaluation->EvaluateScore->getScoreList($teamId);
        $evaluationList = $this->Evaluation->getEvaluations($evaluateTermId, $evaluateeId);

        if (empty($evaluationList)) {
            $this->Pnotify->outError(__d('gl', "このメンバーの評価は完了しています。"));
            return $this->redirect($this->referer());
        }

        if(empty(Hash::extract($evaluationList, '0.{n}.Evaluation.goal_id')[0]))
        {
            $totalList = $evaluationList[0];
        } else {
            $totalList = [];
        }

        unset($evaluationList[0]);
        $goalList = $evaluationList;

        $evaluateType = $this->Evaluation->getEvaluateType($evaluateTermId, $evaluateeId);

        // set progress
        foreach ($goalList as $key => $val) {
            foreach ($val as $key2 => $val2) {
                $goalList[$key][$key2]['Goal']['progress'] = $this->Evaluation->Goal->getProgress($val2['Goal']);
            }
        }
        $this->set(compact('scoreList', 'totalList', 'goalList', 'evaluateTermId', 'evaluateeId', 'evaluateType'));
    }

    function add()
    {
        $this->request->allowMethod('post', 'put');

        // case of saving draft
        if (isset($this->request->data['is_draft'])) {
            $saveType = "draft";
            unset($this->request->data['is_draft']);
            $successMsg = __d('gl', "下書きを保存しました。");
            $successAct = $this->referer();
            // case of registering
        }
        else {
            $saveType = "register";
            unset($this->request->data['is_register']);
            $successMsg = __d('gl', "自己評価を登録しました。");
            $successAct = "index";
        }

        // 保存処理実行
        try {
            $this->Evaluation->begin();
            $this->Evaluation->add($this->request->data, $saveType);
        } catch (RuntimeException $e) {
            $this->Evaluation->rollback();
            // saving as draft
            echo "test";
            if ($saveType === "register") {
                $this->Evaluation->add($this->request->data, "draft");
            }
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->Evaluation->commit();
        $this->Pnotify->outSuccess($successMsg);
        return $this->redirect($successAct);

    }

}
