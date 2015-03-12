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
        $evaluateList = $this->Evaluation->getEvaluationList($evaluateTermId, $evaluateeId);
        $this->set(compact('scoreList', 'evaluateList'));
    }

    function add()
    {
        $this->request->allowMethod('post');
        $isDraft  = viaIsSet($this->request->data['is_draft']);
        $isRegist = viaIsSet($this->request->data['is_register']);

        if(!$isDraft || !$isRegist) {
            $this->Pnotify->outError(__d('gl', "保存に失敗しました。"));
            $this->redirect($this->referer());
        }

        // case of saving draft
        if($isDraft) {
            $saveDraft = $this->Evaluation->addDrafts($this->request->data);
            if ($saveDraft) {
                $this->Pnotify->outSuccess(__d('gl', "下書きを保存しました。"));
                $this->redirect($this->referer());
            } else {
                $this->Pnotify->outSuccess(__d('gl', "下書きの保存に失敗しました。"));
                $this->redirect($this->referer());
            }

        // case of registering
        } else {
            $saveRegister = $this->Evaluation->addRegisters($this->request->data);
            if ($saveRegister) {
                $this->Pnotify->outSuccess(__d('gl', "自己評価を登録しました。"));
                $this->redirect($this->referer());
            } else {
                $this->Pnotify->outSuccess(__d('gl', "自己評価の登録に失敗しました。"));
                $this->redirect($this->referer());
            }
        }
    }

}
