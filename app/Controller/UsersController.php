<?php
App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * User register action
     *
     * @return void
     */
    public function register()
    {
        //ログイン済の場合はトップへ
        if ($this->Auth->user()) {
            $this->redirect('/');
        }

        if ($this->request->is('post') && !empty($this->request->data)) {
            //ユーザ仮登録成功
            if ($this->User->userProvisionalRegistration($this->request->data)) {

            }
            //ユーザ仮登録失敗
            else {

            }
        }
        //姓名の並び順をセット
        $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
        $this->set(compact('last_first'));
    }
}
