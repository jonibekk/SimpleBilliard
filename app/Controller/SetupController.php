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

    public function index()
    {
    }

    public function goal_image()
    {
    }

    public function purpose_select()
    {
    }

    public function goal_select()
    {
    }

    public function goal_create()
    {
    }

    public function profile_image()
    {
    }

    public function profile_add()
    {
    }

    public function app_image()
    {
    }

    public function app_select()
    {
    }
}
