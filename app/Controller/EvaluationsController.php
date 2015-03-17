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
        $eval_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $is_myself_evaluations_incomplete = $this->Evaluation->isMySelfEvalIncomplete($eval_term_id);
        if ($is_myself_evaluations_incomplete) {
            $incomplete_count++;
        }

        $this->set(compact('eval_term_id', 'incomplete_count', 'is_myself_evaluations_incomplete'));
    }

    function view()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }

}
