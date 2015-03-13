<?php
App::uses('AppController', 'Controller');

/**
 * Evaluations Controller

 */
class EvaluationsController extends AppController
{

    function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }

    function view($evaluateTermId=null, $evaluateeId=null)
    {
        if(!$evaluateTermId || !$evaluateeId) {
            return;
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        $teamId = $this->Session->read('current_team_id');
        $scoreList = $this->Evaluation->EvaluateScore->getScoreList($teamId);
        $evaluationList = $this->Evaluation->getEvaluationList($evaluateTermId, $evaluateeId);
        $this->set(compact('scoreList', 'evaluationList'));
    }

    function add()
    {
        $this->request->allowMethod('post');

        // case of saving draft
        if(isset($this->request->data['is_draft'])) {
            $saveType = "draft";
            unset($this->request->data['is_draft']);
            $successMsg = __d('gl', "下書きを保存しました。");
            $errorMsg   = __d('gl', "下書きの保存に失敗しました。");

        // case of registering
        } else {
            $saveType = "register";
            unset($this->request->data['is_register']);
            $successMsg = __d('gl', "自己評価を登録しました。");
            $errorMsg   = __d('gl', "自己評価の登録に失敗しました。");
        }

        $saveEvaluation = $this->Evaluation->add($this->request->data, $saveType);
        if ($saveEvaluation) {
            $this->Pnotify->outSuccess($successMsg);
        } else {
            $this->Pnotify->outError($errorMsg);
        }
        $this->redirect($this->referer());

    }

}
