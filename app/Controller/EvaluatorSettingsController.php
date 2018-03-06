<?php
App::uses('AppController', 'Controller');

/**
 * EvaluatorSettingsController Controller
 */
class EvaluatorSettingsController extends AppController
{
    public $uses = [
    ];

    function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * TODO: implement here
     */
    function index()
    {
    }

    /**
     * TODO: implement here
     */
    function detail()
    {
        $userId = $this->request->params['user_id'];
        $this->set('userId', $userId);
    }
}
