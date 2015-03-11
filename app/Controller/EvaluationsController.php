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
    }

    function add()
    {
        $this->request->allowMethod('post');
    }

}
