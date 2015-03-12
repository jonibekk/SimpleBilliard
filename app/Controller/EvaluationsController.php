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
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        //評価設定の取得

        //自己評価取得

    }

    function view()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }

}
