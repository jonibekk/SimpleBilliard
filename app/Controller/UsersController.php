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
        $this->Auth->allow('register', 'login', 'verify', 'logout', 'password_reset', 'token_resend', 'sent_mail');

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
     * 新規プロフィール入力
     */
    public function add_profile()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        //新規ユーザ登録モードじゃない場合は４０４
        if ($this->Session->read('add_new_mode') !== MODE_NEW_PROFILE) {
            throw new NotFoundException;
        }
        $me = $this->Auth->user();
        //ローカル名を利用している国かどうか？
        $is_not_use_local_name = $this->User->isNotUseLocalName($me['language']);
        if ($this->request->is('post') && !empty($this->request->data)) {
            //プロフィールを保存
            $this->User->id = $me['id'];
            if ($this->User->save($this->request->data)) {
                //チーム作成ページへリダイレクト
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect(['controller' => 'teams', 'action' => 'add']);
            }
        }
        $this->set(compact('me', 'is_not_use_local_name'));
        return $this->render();
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
                $this->Pnotify->outSuccess(__d('notify', '本登録が完了しました！'));
                //新規ユーザ登録時のフロー
                $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                //新規プロフィール入力画面へ
                return $this->redirect(['action' => 'add_profile']);
            }
            else {
                //ログインされていれば、メール追加
                $this->Pnotify->outSuccess(__d('notify', 'メールアドレスの認証が完了しました。'));
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
        } catch (RuntimeException $e) {
            //例外の場合は、トークン再送信画面へ
            $this->Pnotify->outError($e->getMessage());
            //トークン再送メージへ
            $this->redirect(['action' => 'token_resend']);
        }
    }

    /**
     * パスワードリセット
     */
    public function password_reset($token = null)
    {
        if ($this->Auth->user()) {
            throw new NotFoundException();
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        //トークンがある場合はパスワードリセット画面
        if ($token) {
            //トークンが正しく期限内の場合
            if ($user_email = $this->User->checkPasswordToken($token)) {
                if ($this->request->is('post') && !empty($this->request->data)) {
                    //パスワードリセット完了した場合
                    if ($this->User->passwordReset($user_email, $this->request->data)) {
                        //パスワードリセット完了の旨をユーザに通知
                        $this->GlEmail->sendMailCompletePasswordReset($user_email['User']['id']);
                        $this->Pnotify->outSuccess(__d('gl', "新しいパスワードでログインしてください。"),
                                                   ['title' => __d('gl', 'パスワードを設定しました')]);
                        $this->redirect(['action' => 'login']);
                    }
                }
                return $this->render('password_reset');
            }
            //トークンが正しくないor期限外
            else {
                $this->Pnotify->outError(__d('gl', "パスワードトークンが正しくないか、期限切れの可能性があります。もう一度、再設定用のメールを送信してください。"),
                                         ['title' => __d('gl', "トークンの認証に失敗しました。")]);
                $this->redirect(['action' => 'password_reset']);
            }
        }
        //トークンがない場合はメールアドレス入力画面
        else {
            if ($this->request->is('post') && !empty($this->request->data)) {
                //パスワード認証情報登録成功した場合
                if ($user = $this->User->passwordResetPre($this->request->data)) {
                    //メールでトークンを送信
                    $this->GlEmail->sendMailPasswordReset($user['User']['id'], $user['User']['password_token']);
                    $this->Pnotify->outSuccess(__d('gl', "パスワード再設定のメールを送信しました。ご確認ください。"),
                                               ['title' => __d('gl', "メールを送信しました")]);
                }
            }
        }
        return $this->render('password_reset_request');
    }

    public function token_resend()
    {
        if ($this->Auth->user()) {
            throw new NotFoundException();
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        if ($this->request->is('post') && !empty($this->request->data)) {
            //パスワード認証情報登録成功した場合
            if ($email_user = $this->User->saveEmailToken($this->request->data['User']['email'])) {
                //メールでトークンを送信
                $this->GlEmail->sendMailEmailTokenResend($email_user['User']['id'],
                                                         $email_user['Email']['email_token']);
                $this->Pnotify->outSuccess(__d('gl', "メールアドレス認証用のメールを送信しました。ご確認ください。"),
                                           ['title' => __d('gl', "メールを送信しました")]);
            }
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
