<?php
App::uses('AppController', 'Controller');

/**
 * Teams Controller
 *
 * @property User $User
 */
class TeamsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function add()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $me = $this->User->findById($this->Auth->user('id'));
        $this->set(compact('me'));

    }

}
