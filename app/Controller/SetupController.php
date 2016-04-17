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

    public function ajax_get_setup_status()
    {
        $this->layout = false;
        $status = $this->getStatusWithRedisSave();
        $res = [
          'status' => $status,
          'setup_rest_count' => $this->calcSetupRestCount($status)
        ];
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_add_goal() {
        $this->log($this->request->data);
        return true;
    }

}
