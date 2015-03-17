<?php
App::uses('AppController', 'Controller');

/**
 * Evaluations Controller
 *
 * @property Evaluation $Evaluation
 */
class EvaluationsController extends AppController
{
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
        $my_evaluations = $this->Evaluation->EvaluateTerm->getMyEvaluationAllTerm();
        $eval_term = $this->Team->EvaluateTerm->getCurrentTerm();
        $eval_term_id = viaIsSet($eval_term['EvaluateTerm']['id']) ? $eval_term['EvaluateTerm']['id'] : null;

        $this->set(compact('is_self_on', 'is_evaluator_on', 'is_final_on', 'my_evaluations', 'eval_term_id'));
    }

    function view()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }

}
