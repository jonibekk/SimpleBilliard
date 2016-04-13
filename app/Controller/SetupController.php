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
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('without_footer', true);
        return $this->render();
    }

    public function index() {}

    public function goal_image() {}

    public function select_purpose() {}

    public function select_goal() {}

}
