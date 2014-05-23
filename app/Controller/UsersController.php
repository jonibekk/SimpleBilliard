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
            //タイムゾーンをセット
            if (isset($this->request->data['User']['local_date'])) {
                //ユーザのローカル環境から取得したタイムゾーンをセット
                $timezone = $this->Timezone->getLocalTimezone($this->request->data['User']['local_date']);
                $this->request->data['User']['timezone'] = $timezone;
                //自動タイムゾーン設定フラグをoff
                $this->request->data['User']['auto_timezone_flg'] = false;
            }
            //言語を保存
            $this->request->data['User']['language'] = $this->Lang->getLanguage();
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
