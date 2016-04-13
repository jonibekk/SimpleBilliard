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
    }

    public function index()
    {
        return $this->render();
    }

    public function goal_image()
    {
        return $this->render('index');
    }

    public function purpose_select()
    {
        return $this->render('index');
    }

    public function goal_select()
    {
        return $this->render('index');
    }

    public function goal_create()
    {
        return $this->render('index');
    }

}
