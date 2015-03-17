<?php
App::uses('AppController', 'Controller');

/**
 * Evaluations Controller
 *
 * @property Evaluation $Evaluation
 */
class EvaluationsController extends AppController
{

    function beforeFilter(){
        parent::beforeFilter();
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
        $is_self_on = $this->Team->EvaluationSetting->isEnabledSelf();
        $is_evaluator_on = $this->Team->EvaluationSetting->isEnabledEvaluator();
        $is_final_on = $this->Team->EvaluationSetting->isEnabledFinal();
        $my_evaluations = $this->Evaluation->getMyEvaluation();
        $this->set(compact('is_self_on', 'is_evaluator_on', 'is_final_on', 'my_evaluations'));
    }

    function view($evaluateTermId=null, $evaluateeId=null)
    {
        if(!$evaluateTermId || !$evaluateeId) {
            $this->Pnotify->outError(__d('gl', "パラメータが不正です。"));
            return $this->redirect($this->referer());
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        $teamId = $this->Session->read('current_team_id');
        $scoreList = $this->Evaluation->EvaluateScore->getScoreList($teamId);
        $evaluationList = $this->Evaluation->getEditableEvaluations($evaluateTermId, $evaluateeId);
        $evaluationList = $this->Evaluation->insertValidationStatus($evaluationList);
        if(empty($evaluationList)) {
            $this->Pnotify->outError(__d('gl', "このメンバーの評価は完了しまいます。"));
            return $this->redirect($this->referer());
        }
        if(empty($evaluationList[0]['Evaluation']['goal_id'])) {
            $total = $evaluationList[0];
            unset($evaluationList[0]);
            $goalList = $evaluationList;
        } else {
            $total = [];
            $goalList = $evaluationList;
        }
        var_dump($scoreList);
        $this->set(compact('scoreList', 'total', 'goalList', 'evaluateTermId', 'evaluateeId'));
    }

    function add()
    {
        $this->request->allowMethod('post', 'put');

        // case of saving draft
        if(isset($this->request->data['is_draft'])) {
            $saveType = "draft";
            unset($this->request->data['is_draft']);
            $successMsg = __d('gl', "下書きを保存しました。");

        // case of registering
        } else {
            $saveType = "register";
            unset($this->request->data['is_register']);
            $successMsg = __d('gl', "自己評価を登録しました。");
        }

        // 保存処理実行
        try {
            $saved = $this->Evaluation->add($this->request->data, $saveType);
            if(!$saved) {
                throw new RuntimeException(__d('validate', "入力値に誤りがあります。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->Pnotify->outSuccess($successMsg);
        $this->redirect('index');

    }

}
