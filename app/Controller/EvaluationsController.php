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
        $is_self_on = $this->Team->EvaluationSetting->isEnabledSelf();
        $is_evaluator_on = $this->Team->EvaluationSetting->isEnabledEvaluator();
        $is_final_on = $this->Team->EvaluationSetting->isEnabledFinal();
        $eval_term = $this->Team->EvaluateTerm->getCurrentTerm();
        $eval_term_id = viaIsSet($eval_term['EvaluateTerm']['id']) ? $eval_term['EvaluateTerm']['id'] : null;
        $is_myself_evaluations_completed = $this->Evaluation->isMySelfEvalCompleted($eval_term_id);
        if ($is_self_on && !$is_myself_evaluations_completed) {
            $incomplete_count++;
        }

        $this->set(compact('is_self_on', 'is_evaluator_on', 'is_final_on', 'is_myself_evaluations_completed',
                           'eval_term_id',
                           'incomplete_count'));
    }

    function view()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }

}
