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
        $this->_setupAuth();
    }

    /**
     * Setup Authentication Component
     *
     * @return void
     */
    protected function _setupAuth()
    {
        $this->Auth->allow('register', 'login', 'verify', 'logout', 'reset_password', 'sent_mail');

        $this->Auth->authenticate = array(
            'Form2' => array(
                'fields'    => array(
                    'username' => 'email',
                    'password' => 'password'
                ),
                'userModel' => 'User',
                'scope'     => array(
                    'User.active_flg'             => 1,
                    'PrimaryEmail.email_verified' => 1
                ),
                'recursive' => 0,
            )
        );
        $this->Auth->loginRedirect = '/';
        $this->Auth->logoutRedirect = array(
            'controller' => 'users',
            'action'     => 'login'
        );
        $this->Auth->loginAction = array(
            'admin'      => false,
            'controller' => 'users',
            'action'     => 'login'
        );
    }

    /**
     * Common login action
     *
     * @return void
     */
    public function login()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        //ログイン済の場合はトップへ
        if ($this->Auth->user()) {
            /** @noinspection PhpInconsistentReturnPointsInspection */
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect('/');
        }
        if ($this->request->is('post') && isset($this->request->data['User'])) {
            if ($this->Auth->login()) {
                $this->_setAfterLogin();
                $this->Pnotify->outSuccess(__d('notify', "%sさん、こんにちは。", $this->Auth->user('display_username')),
                                           ['title' => __d('notify', "ログイン成功")]);
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
            else {
                $this->Pnotify->outError(__d('notify', "メールアドレスもしくはパスワードが正しくありません。"));
            }
        }

    }

    /**
     * Common logout action
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->Auth->user();
        $this->Session->destroy();
        $this->Cookie->destroy();
        $this->Pnotify->outInfo(__d('notify', "%sさん、またお会いしましょう。", $user['display_username']),
                                ['title' => __d('notify', "ログアウトしました")]);
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->Auth->logout());
    }

    /**
     * User register action
     *
     * @return void
     */
    public function register()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
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
        $this->layout = LAYOUT_ONE_COLUMN;
        if ($this->Session->read('tmp_email')) {
            $this->set(['email' => $this->Session->read('tmp_email')]);
            $this->Session->delete('tmp_email');
        }
        else {
            throw new NotFoundException();
        }
    }

    /**
     * Confirm email action
     *
     * @param string $token Token
     *
     * @throws RuntimeException
     * @return void
     */
    public function verify($token = null)
    {
        try {
            $user = $this->User->verifyEmail($token);
            $last_login = null;
            if ($user) {
                //ログイン済か確認
                $last_login = $user['User']['last_login'];
                //自動ログイン
                $this->_autoLogin($user['User']['id']);
            }
            if (!$last_login) {
                //ログインがされていなければ、新規ユーザなので「ようこそ」表示
                $this->Pnotify->outSuccess(__d('notify', 'Goalousへようこそ！'));
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
            else {
                //ログインされていれば、メール追加
                $this->Pnotify->outSuccess(__d('notify', 'メールアドレスの認証が完了しました。'));
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /*
     * 自動でログインする
     */
    public function _autoLogin($user_id)
    {
        $user = $this->User->findById($user_id);
        //リダイレクト先を退避
        $redirect = null;
        if ($this->Session->read('Auth.redirect')) {
            $redirect = $this->Session->read('Auth.redirect');
        }
        $this->Auth->logout();

        unset($user['User']['password']);
        $user = array_merge(['User' => []], $user);
        //自動ログイン
        if ($this->Auth->login($user['User'])) {
            //リダイレクト先をセッションに保存
            $this->Session->write('redirect', $redirect);
            $this->_setAfterLogin();
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * ログイン後に実行する
     */
    public function _setAfterLogin()
    {
        $this->User->id = $this->Auth->user('id');
        $this->User->saveField('last_login', date('Y-m-d H:i:s'));
        $this->Mixpanel->setUser($this->User->id);
    }
}
