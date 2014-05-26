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
                //ユーザにメール送信
                $this->GlEmail->sendMailUserVerify($this->User->id, $this->User->Email->data['Email']['email_token']);
                $this->Session->write('tmp_email', $this->User->Email->data['Email']['email']);
                $this->redirect(['action' => 'sent_mail']);
            }
            //ユーザ仮登録失敗
            else {

            }
        }
        //姓名の並び順をセット
        $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
        $this->set(compact('last_first'));
    }

    /**
     * 承認メール送信後の画面
     */
    public function sent_mail()
    {
        if ($this->Session->read('tmp_email')) {
            $this->set(['email' => $this->Session->read('tmp_email')]);
            $this->Session->delete('tmp_email');
        }
        else {
            //error
            $this->redirect('/');
        }
    }

}
