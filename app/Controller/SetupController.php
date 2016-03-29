<?php
App::uses('AppController', 'Controller');

/**
 * Setup Controller
 */
class SetupController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('without_footer', true);
        return $this->render();
    }
}
