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

    function view()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $teamId = $this->Session->read('current_team_id');
        $scoreList = $this->EvaluateScore->getScoreList($teamId);

        $this->set(compact('scoreList'));
    }

    function add()
    {
        $this->request->allowMethod('post');
        $type = $this->request->data['type'];
        if ($type !== 'register' || $type !== 'draft') {
            $this->Pnotify->outError(__d('gl', "保存に失敗しました。"));
            $this->redirect($this->referer());
        }

        $this->Evaluation->create();

        // case of saving draft
        if($type === 'draft') {
            $saveDraft = $this->Evaluation->saveDraft($this->request->data);
            if ($saveDraft) {
                $this->Pnotify->outError(__d('gl', "下書きを保存しました。"));
                $this->redirect($this->referer());
            }

        // case of registering
        } else {
            $saveRegister = $this->Evaluation->saveRegister($this->request->data);
            if ($saveRegister) {
                $this->Pnotify->outError(__d('gl', "自己評価を登録しました。"));
                $this->redirect($this->referer());
            }
        }
    }

}
