<?php
App::uses('AppController', 'Controller');

/**
 * Teams Controller
 *
 * @property Team $Team
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
        if ($this->request->is('post') && !empty($this->request->data)) {
            if ($this->Team->add($this->request->data, $this->Auth->user('id'))) {
                $this->_refreshAuth($this->Auth->user('id'));
                $this->Pnotify->outSuccess(__d('gl', "チームを作成しました。"));
                $this->redirect("/");
            }
            else {
                $this->Pnotify->outError(__d('gl', "チームに失敗しました。"));
            }
        }
    }

}
