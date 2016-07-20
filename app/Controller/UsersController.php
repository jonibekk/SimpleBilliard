<?php
App::uses('AppController', 'Controller');
App::uses('Post', 'Model');

/**
 * Users Controller
 *
 * @property User           $User
 * @property Invite         $Invite
 * @property Circle         $Circle
 * @property TwoFaComponent $TwoFa
 */
class UsersController extends AppController
{
    public $uses = [
        'User',
        'Invite',
        'Circle',
    ];
    public $components = [
        'TwoFa',
    ];

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
        $this->Auth->allow('register', 'login', 'verify', 'logout', 'password_reset', 'token_resend', 'sent_mail',
            'accept_invite', 'registration_with_set_password', 'two_fa_auth', 'two_fa_auth_recovery',
            'add_subscribe_email', 'ajax_validate_email');

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
        $st_login = REFERER_STATUS_LOGIN;
        $this->Auth->loginRedirect = "/?st={$st_login}";
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
        $this->_uservoiceSetSession();
        $this->layout = LAYOUT_ONE_COLUMN;

        if ($this->Auth->user()) {
            $this->_ifFromUservoiceRedirect();
            return $this->redirect('/');
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        //account lock check
        $ip_address = $this->request->clientIp();
        $is_account_locked = $this->GlRedis->isAccountLocked($this->request->data['User']['email'], $ip_address);
        if ($is_account_locked) {
            $this->Pnotify->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
                ACCOUNT_LOCK_TTL / 60));
            return $this->render();
        }
        //メアド、パスの認証(セッションのストアはしていない)
        $user_info = $this->Auth->identify($this->request, $this->response);
        if (!$user_info) {
            $this->Pnotify->outError(__("Email address or Password is incorrect."));
            return $this->render();
        }
        $this->Session->write('preAuthPost', $this->request->data);

        //デバイス情報を保存する
        $user_id = $user_info['id'];
        $installation_id = $this->request->data['User']['installation_id'];
        if ($installation_id == "no_value") {
            $installation_id = null;
        }
        $app_version = $this->request->data['User']['app_version'];
        if ($app_version == "no_value") {
            $app_version = null;
        }
        if (!empty($installation_id)) {
            try {
                $this->NotifyBiz->saveDeviceInfo($user_id, $installation_id, $app_version);
                //セットアップガイドステータスの更新
                $this->updateSetupStatusIfNotCompleted();
            } catch (RuntimeException $e) {
                $this->log([
                    'where'           => 'login page',
                    'error_msg'       => $e->getMessage(),
                    'user_id'         => $user_id,
                    'installation_id' => $installation_id,
                ]);
            }
        }

        $is_2fa_auth_enabled = true;
        // 2要素認証設定OFFの場合
        // 2要素認証設定ONかつ、設定して30日以内の場合
        if ((is_null($user_info['2fa_secret']) === true) || (empty($user_info['2fa_secret']) === false
                && $this->GlRedis->isExistsDeviceHash($user_info['DefaultTeam']['id'], $user_info['id']))
        ) {
            $is_2fa_auth_enabled = false;
        }

        //２要素設定有効なら
        if ($is_2fa_auth_enabled) {
            $this->Session->write('2fa_secret', $user_info['2fa_secret']);
            $this->Session->write('user_id', $user_info['id']);
            $this->Session->write('team_id', $user_info['DefaultTeam']['id']);
            return $this->redirect(['action' => 'two_fa_auth']);
        }

        return $this->_afterAuthSessionStore();
    }

    function two_fa_auth()
    {
        if ($this->Auth->user()) {
            return $this->redirect($this->referer());
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        //仮認証状態か？そうでなければエラー出してリファラリダイレクト
        $is_avail_auth = !empty($this->Session->read('preAuthPost')) ? true : false;
        if (!$is_avail_auth) {
            $this->Pnotify->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $is_account_locked = $this->GlRedis->isTwoFaAccountLocked($this->Session->read('user_id'),
            $this->request->clientIp());
        if ($is_account_locked) {
            $this->Pnotify->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
                ACCOUNT_LOCK_TTL / 60));
            return $this->render();
        }

        if ((empty($this->Session->read('2fa_secret')) === false && empty($this->request->data['User']['two_fa_code']) === false)
            && $this->TwoFa->verifyKey($this->Session->read('2fa_secret'),
                $this->request->data['User']['two_fa_code']) === true
        ) {
            $this->GlRedis->saveDeviceHash($this->Session->read('team_id'), $this->Session->read('user_id'));
            return $this->_afterAuthSessionStore();

        } else {
            $this->Pnotify->outError(__("Incorrect 2fa code."));
            return $this->render();
        }
    }

    /**
     * リカバリコード入力画面
     *
     * @return CakeResponse|void
     */
    function two_fa_auth_recovery()
    {
        if ($this->Auth->user()) {
            return $this->redirect($this->referer());
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        //仮認証状態か？そうでなければエラー出してリファラリダイレクト
        $is_avail_auth = !empty($this->Session->read('preAuthPost')) ? true : false;
        if (!$is_avail_auth) {
            $this->Pnotify->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $is_account_locked = $this->GlRedis->isTwoFaAccountLocked($this->Session->read('user_id'),
            $this->request->clientIp());
        if ($is_account_locked) {
            $this->Pnotify->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
                ACCOUNT_LOCK_TTL / 60));
            return $this->render();
        }

        // 入力されたコードが利用可能なリカバリーコードか確認
        $code = str_replace(' ', '', $this->request->data['User']['recovery_code']);
        $row = $this->User->RecoveryCode->findUnusedCode($this->Session->read('user_id'), $code);
        if (!$row) {
            $this->Pnotify->outError(__("Incorrect recovery code."));
            return $this->render();
        }

        // コードを使用済にする
        $res = $this->User->RecoveryCode->useCode($row['RecoveryCode']['id']);
        if (!$res) {
            $this->Pnotify->outError(__("An error has occurred."));
            return $this->render();
        }

        $this->GlRedis->saveDeviceHash($this->Session->read('team_id'), $this->Session->read('user_id'));
        return $this->_afterAuthSessionStore();
    }

    function _afterAuthSessionStore()
    {
        $redirect_url = ($this->Session->read('Auth.redirect')) ? $this->Session->read('Auth.redirect') : "/?st=" . REFERER_STATUS_LOGIN;
        $this->request->data = $this->Session->read('preAuthPost');
        if ($this->Auth->login()) {
            $this->Session->delete('preAuthPost');
            $this->Session->delete('2fa_secret');
            $this->Session->delete('user_id');
            $this->Session->delete('team_id');
            $this->Session->write('referer_status', REFERER_STATUS_LOGIN);
            $this->_refreshAuth();
            $this->_setAfterLogin();
            $this->Pnotify->outSuccess(__("Hello %s.", $this->Auth->user('display_username')),
                ['title' => __("Succeeded to login")]);
            return $this->redirect($redirect_url);
        } else {
            $this->Pnotify->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
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
        foreach ($this->Session->read() as $key => $val) {
            if (in_array($key, ['Config', '_Token', 'Auth'])) {
                continue;
            }
            $this->Session->delete($key);
        }
        $this->Cookie->destroy();
        $this->Pnotify->outInfo(__("See you %s", $user['display_username']),
            ['title' => __("Logged out")]);
        return $this->redirect($this->Auth->logout());
    }

    /**
     * User register action
     *
     * @return void
     */
    public function register()
    {
        //現状、ローカルと本番環境以外でbasic認証を有効にする
        if (!(ENV_NAME == "local" || ENV_NAME == "www") && !isset($this->request->params['named']['invite_token'])) {
            $this->_setBasicAuth();
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        //ログイン済の場合はトップへ
        if ($this->Auth->user()) {
            return $this->redirect('/');
        }
        //トークン付きの場合はメアドデータを取得
        if (isset($this->request->params['named']['invite_token'])) {
            try {
                //トークンが有効かチェック
                $this->Invite->confirmToken($this->request->params['named']['invite_token']);
            } catch (RuntimeException $e) {
                $this->Pnotify->outError($e->getMessage());
                return $this->redirect('/');
            }
            $invite = $this->Invite->getByToken($this->request->params['named']['invite_token']);
            if (isset($invite['Invite']['email'])) {
                $this->set(['email' => $invite['Invite']['email']]);
            }
        }

        // リクエストデータが無い場合は登録画面を表示
        if (!$this->request->is('post')) {
            $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
            $this->set(compact('last_first'));
            return $this->render();
        }

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

        // トークンが存在しない場合はユーザ仮登録
        if (!isset($this->request->params['named']['invite_token'])) {
            // 仮登録実行
            $isSuccessTmpReg = $this->User->userRegistration($this->request->data);

            // 仮登録失敗
            if (!$isSuccessTmpReg) {
                //姓名の並び順をセット
                $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
                $this->set(compact('last_first'));
                return $this->render();
            }

            // 仮登録成功
            // ユーザにメール送信
            $this->GlEmail->sendMailUserVerify($this->User->id,
                $this->User->Email->data['Email']['email_token']);
            $this->Session->write('tmp_email', $this->User->Email->data['Email']['email']);
            return $this->redirect(['action' => 'sent_mail']);
        }

        // ユーザ本登録
        $isSuccessMainReg = $this->User->userRegistration($this->request->data, false);

        // 本登録失敗
        if (!$isSuccessMainReg) {
            //姓名の並び順をセット
            $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
            $this->set(compact('last_first'));
            return $this->render();
        }

        // 本登録成功
        //ログイン
        $this->_autoLogin($this->User->getLastInsertID(), true);
        //チーム参加
        $this->_joinTeam($this->request->params['named']['invite_token']);
        //ホーム画面でモーダル表示
        $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
        //プロフィール画面に遷移
        return $this->redirect([
            'action'       => 'add_profile',
            'invite_token' => $this->request->params['named']['invite_token']
        ]);

    }

    /**
     * User Registration by batch set up.
     */
    public function registration_with_set_password()
    {
        if ($this->Auth->user()) {
            throw new NotFoundException();
        }
        if (!viaIsSet($this->request->params['named']['invite_token'])) {
            throw new NotFoundException();
        }

        try {
            //トークンが有効かチェック
            $this->Invite->confirmToken($this->request->params['named']['invite_token']);
            if (!$this->Invite->isByBatchSetup($this->request->params['named']['invite_token'])) {
                throw new RuntimeException(__("Code is incorrect."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect('/');
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        if ($this->request->is('post')) {
            //tokenチェック
            $invite = $this->Invite->getByToken($this->request->params['named']['invite_token']);

            //Email match check
            if (!viaIsSet($invite['Invite']['email']) || $this->request->data['Email']['email'] != $invite['Invite']['email']) {
                $this->Pnotify->outError(__("Email address is incorrect. Please enter the address from the email you received."));
                return $this->render();
            }
            $user = $this->User->getUserByEmail($this->request->data['Email']['email']);
            $this->Invite->verify($this->request->params['named']['invite_token'], $user['User']['id']);
            //タイムゾーン設定
            //ユーザのローカル環境から取得したタイムゾーンをセット
            $timezone = $this->Timezone->getLocalTimezone($this->request->data['User']['local_date']);
            $user['User']['timezone'] = $timezone;

            //save password & activation
            $this->User->passwordReset($user, ['User' => $this->request->data['User']]);
            //team member activate
            $this->User->TeamMember->activateMembers($user['User']['id'], $invite['Invite']['team_id']);

            $this->_autoLogin($user['User']['id']);
            //Display modal on home.
            $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
            return $this->redirect('/');
        }
        return $this->render();
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

        // リクエストデータが無い場合は入力画面を表示
        if (!$this->request->is('put')) {
            $this->request->data = ['User' => $me];
            $language_name = $this->Lang->availableLanguages[$me['language']];
            $this->set(compact('me', 'is_not_use_local_name', 'language_name'));
            return $this->render();
        }

        //ローカル名の入力が無い場合は除去
        if (isset($this->request->data['LocalName'])) {
            $local_name = $this->request->data['LocalName'][0];
            if (!$local_name['first_name'] || !$local_name['last_name']) {
                unset($this->request->data['LocalName']);
            }
        }

        // プロフィールを保存
        $this->User->id = $me['id'];
        $isSavedSuccess = $this->User->saveAll($this->request->data);

        // 保存失敗
        if (!$isSavedSuccess) {
            $language_name = $this->Lang->availableLanguages[$me['language']];

            $this->set(compact('me', 'is_not_use_local_name', 'language_name'));
            return $this->render();
        }

        // 保存成功
        $this->_refreshAuth($me['id']);

        //トークン付きの場合は招待のため、ホームへ
        if (isset($this->request->params['named']['invite_token'])) {
            $this->Session->write('referer_status', REFERER_STATUS_INVITATION_NOT_EXIST);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect("/?st=" . REFERER_STATUS_INVITATION_NOT_EXIST);
        } else {
            //チーム作成ページへリダイレクト
            $this->Session->write('referer_status', REFERER_STATUS_SIGNUP);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect(['controller' => 'teams', 'action' => 'add']);
        }

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
        } else {
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
                $this->Pnotify->outSuccess(__('Succeeded to register!'));
                //新規ユーザ登録時のフロー
                $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                //新規プロフィール入力画面へ
                return $this->redirect(['action' => 'add_profile']);
            } else {
                //ログインされていれば、メール追加
                $this->Pnotify->outSuccess(__('Authenticated your email address.'));
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
        } catch (RuntimeException $e) {
            //例外の場合は、トークン再送信画面へ
            $this->Pnotify->outError($e->getMessage());
            //トークン再送メージへ
            return $this->redirect(['action' => 'token_resend']);
        }
    }

    /**
     * メールアドレス変更時の認証
     *
     * @param $token
     */
    public function change_email_verify($token)
    {
        try {
            $this->User->begin();
            $user = $this->User->verifyEmail($token, $this->Auth->user('id'));
            $this->User->changePrimaryEmail($this->Auth->user('id'), $user['Email']['id']);
        } catch (RuntimeException $e) {
            $this->User->rollback();
            //例外の場合は、トークン再送信画面へ
            $this->Pnotify->outError($e->getMessage() . "\n" . __("Please cancel changing email address and try again."));
            //トークン再送ページへ
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect(['action' => 'settings']);
        }
        $this->User->commit();
        $this->_autoLogin($this->Auth->user('id'));
        $this->Pnotify->outSuccess(__("Email address is changed."));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect(['action' => 'settings']);
    }

    /**
     * Password reset
     *
     * @param null $token
     *
     * @return CakeResponse|void
     */
    public function password_reset($token = null)
    {
        if ($this->Auth->user()) {
            throw new NotFoundException();
        }

        $this->layout = LAYOUT_ONE_COLUMN;

        if (!$token) {
            if (!$this->request->is('post')) {
                return $this->render('password_reset_request');
            }

            // Search user
            $user = $this->User->passwordResetPre($this->request->data);
            if ($user) {
                // Send mail containing token
                $this->GlEmail->sendMailPasswordReset($user['User']['id'], $user['User']['password_token']);
                $this->Pnotify->outSuccess(__("Password reset email has been sent. Please check your email."),
                    ['title' => __("Email sent.")]);
            }
            return $this->render('password_reset_request');
        }

        // Token existing case
        $user_email = $this->User->checkPasswordToken($token);

        if (!$user_email) {
            $this->Pnotify->outError(__("Password code incorrect. The validity period may have expired. Please resend email again."),
                ['title' => __("Failed to confirm code.")]);
            return $this->redirect(['action' => 'password_reset']);
        }

        if (!$this->request->is('post')) {
            return $this->render('password_reset');
        }

        $successPasswordReset = $this->User->passwordReset($user_email, $this->request->data);
        if ($successPasswordReset) {
            // Notify to user reset password
            $this->GlEmail->sendMailCompletePasswordReset($user_email['User']['id']);
            $this->Pnotify->outSuccess(__("Please login with your new password."),
                ['title' => __('Password is set.')]);
            return $this->redirect(['action' => 'login']);
        }
        return $this->render('password_reset');

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
                $this->Pnotify->outSuccess(__("Confirmation has been sent to your email address."),
                    ['title' => __("Send you an email.")]);
            }
        }
    }

    /**
     * ユーザ設定
     */
    public function settings()
    {
        //ユーザデータ取得
        $me = $this->_getMyUserDataForSetting();
        if ($this->request->is('put')) {
            //キャッシュ削除
            Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_NOTIFY_SETTING, true, null, false), 'user_data');
            Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');
            //request->dataに入っていないデータを表示しなければ行けない為、マージ
            $this->request->data['User'] = array_merge($me['User'],
                isset($this->request->data['User']) ? $this->request->data['User'] : []);

            // ローカル名 更新時
            if (isset($this->request->data['LocalName'][0])) {
                // すでにレコードが存在する場合は、id をセット（update にする)
                $row = $this->User->LocalName->getName($this->Auth->user('id'),
                    $this->request->data['LocalName'][0]['language']);
                if ($row) {
                    $this->request->data['LocalName'][0]['id'] = $row['LocalName']['id'];
                }
            }

            // 通知設定 更新時
            if (isset($this->request->data['NotifySetting']['email']) &&
                isset($this->request->data['NotifySetting']['mobile'])
            ) {
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('app', 'all'));
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('email',
                            $this->request->data['NotifySetting']['email']));
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('mobile',
                            $this->request->data['NotifySetting']['mobile']));
            }
            $this->User->id = $this->Auth->user('id');
            //チームメンバー情報を付与
            if ($this->User->saveAll($this->request->data)) {
                //ログインし直し。
                $this->_autoLogin($this->Auth->user('id'), true);
                //言語設定
                $this->_setAppLanguage();
                //セットアップガイドステータスの更新
                $this->updateSetupStatusIfNotCompleted();

                $this->Pnotify->outSuccess(__("Saved user setting."));
            } else {
                $this->Pnotify->outError(__("Failed to save user setting."));
            }
            $me = $this->_getMyUserDataForSetting();
            $this->request->data = $me;
            $this->set('my_prof', $this->User->getMyProf());
        } else {
            $this->request->data = $me;
        }
        $this->layout = LAYOUT_TWO_COLUMN;
        //姓名の並び順をセット
        $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
        //言語選択
        $language_list = $this->Lang->getAvailLangList();
        //タイムゾーン
        $timezones = $this->Timezone->getTimezones();
        //ローカル名を利用している国かどうか？
        $is_not_use_local_name = $this->User->isNotUseLocalName($me['User']['language']);
        $not_verified_email = $this->User->Email->getNotVerifiedEmail($this->Auth->user('id'));
        $language_name = $this->Lang->availableLanguages[$me['User']['language']];

        // 通知設定のプルダウンデフォルト
        $this->request->data['NotifySetting']['email'] = 'all';
        $this->request->data['NotifySetting']['mobile'] = 'all';
        // 既に通知設定が保存されている場合
        foreach (['email', 'mobile'] as $notify_target) {
            foreach (array_keys(NotifySetting::$TYPE_GROUP) as $type_group) {
                $values = $this->User->NotifySetting->getSettingValues($notify_target, $type_group);
                $same = true;
                foreach ($values as $k => $v) {
                    if ($this->request->data['NotifySetting'][$k] !== $v) {
                        $same = false;
                        break;
                    }
                }
                if ($same) {
                    $this->request->data['NotifySetting'][$notify_target] = $type_group;
                    break;
                }
            }
        }
        $this->set(compact('me', 'is_not_use_local_name', 'last_first', 'language_list', 'timezones',
            'not_verified_email', 'local_name', 'language_name'));
        return $this->render();
    }

    private function _getMyUserDataForSetting()
    {
        $me = $this->User->getDetail($this->Auth->user('id'));
        unset($me['User']['password']);
        $local_name = $this->User->LocalName->getName($this->Auth->user('id'), $this->Auth->user('language'));
        if (isset($local_name['LocalName'])) {
            $me['LocalName'][0] = $local_name['LocalName'];
        }

        return $me;
    }

    /**
     * パスワード変更
     *
     * @throws NotFoundException
     */
    public function change_password()
    {
        if (!$this->request->is('put')) {
            throw new NotFoundException();
        }

        try {
            $this->User->changePassword($this->request->data);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage(), ['title' => __("Failed to save password change.")]);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Pnotify->outSuccess(__("Changed password."));

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     *
     */
    public function change_email()
    {
        if (!$this->request->is('put')) {
            throw new NotFoundException();
        }

        try {
            $email_data = $this->User->addEmail($this->request->data, $this->Auth->user('id'));
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }

        $this->Pnotify->outInfo(__("Confirmation has been sent to your email address."));
        $this->GlEmail->sendMailChangeEmailVerify($this->Auth->user('id'), $email_data['Email']['email'],
            $email_data['Email']['email_token']);

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * 招待に応じる
     * 登録済みユーザの場合は、チーム参加でホームへリダイレクト
     * 未登録ユーザの場合は、個人情報入力ページへ
     *
     * @param $token
     */
    public function accept_invite($token)
    {
        try {
            // Check token available
            $this->Invite->confirmToken($token);

            if (!$this->Invite->isUser($token)) {
                return $this->redirect(['action' => 'register', 'invite_token' => $token]);
            }

            //By batch setup
            if ($this->Invite->isByBatchSetup($token)) {
                return $this->redirect(['action' => 'registration_with_set_password', 'invite_token' => $token]);
            }

            if (!$this->Auth->user()) {
                $this->Auth->redirectUrl(['action' => 'accept_invite', $token]);
                return $this->redirect(['action' => 'login']);
            }

            // Not allow invite me
            if (!$this->Invite->isForMe($token, $this->Auth->user('id'))) {
                throw new RuntimeException(__("This invitation isn't not for you."));
            }

            $team = $this->_joinTeam($token);

            $this->Session->write('referer_status', REFERER_STATUS_INVITATION_EXIST);
            $this->Pnotify->outSuccess(__("Joined %s.", $team['Team']['name']));
            return $this->redirect("/?st=" . REFERER_STATUS_INVITATION_EXIST);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect("/");
        }
    }

    /**
     * select2のユーザ検索
     */
    function ajax_select2_get_users()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = ['results' => []];
        if (isset($query['term']) && !empty($query['term']) && count($query['term']) <= SELECT2_QUERY_LIMIT && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $with_group = (isset($query['with_group']) && $query['with_group']);
            $res = $this->User->getUsersSelect2($query['term'], $query['page_limit'], $with_group);
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * select2のユーザ検索
     */
    function ajax_select_only_add_users()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = [];
        if (isset($query['post_id']) && !empty($query['post_id']) && isset($query['term']) && !empty($query['term']) && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $res = $this->User->getUsersSelectOnly($query['term'], $query['page_limit'], $query['post_id'], true);
        }
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_modal_2fa_register()
    {
        $this->_ajaxPreProcess();
        if ($this->Session->read('2fa_secret_key')) {
            $google_2fa_secret_key = $this->Session->read('2fa_secret_key');
        } else {
            $google_2fa_secret_key = $this->TwoFa->generateSecretKey();
            $this->Session->write('2fa_secret_key', $google_2fa_secret_key);
        }

        $url_2fa = $this->TwoFa->getQRCodeGoogleUrl(SERVICE_NAME,
            $this->Session->read('Auth.User.PrimaryEmail.email'),
            $google_2fa_secret_key);
        $this->set(compact('url_2fa'));
        $response = $this->render('User/modal_2fa_register');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function register_2fa()
    {
        $this->request->allowMethod('post');
        try {
            if (!$secret_key = $this->Session->read('2fa_secret_key')) {
                throw new RuntimeException(__("An error has occurred."));
            }
            if (!viaIsSet($this->request->data['User']['2fa_code'])) {
                throw new RuntimeException(__("An error has occurred."));
            }
            if (!$this->TwoFa->verifyKey($secret_key, $this->request->data['User']['2fa_code'])) {
                throw new RuntimeException(__("The code is incorrect."));
            }
            //2要素認証コードの登録
            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('2fa_secret', $secret_key);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        $this->Session->delete('2fa_secret_key');
        $this->Mixpanel->track2SV(MixpanelComponent::TRACK_2SV_ENABLE);
        $this->Pnotify->outSuccess(__("Succeeded to save 2-Step Verification."));
        $this->Session->setFlash(null, "flash_click_event", ['id' => 'ShowRecoveryCodeButton'], 'click_event');
        return $this->redirect($this->referer());
    }

    function ajax_get_modal_2fa_delete()
    {
        $this->_ajaxPreProcess();
        $response = $this->render('User/modal_2fa_delete');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function delete_2fa()
    {
        $this->request->allowMethod('post');
        $this->User->id = $this->Auth->user('id');
        $this->User->saveField('2fa_secret', null);
        $this->User->RecoveryCode->invalidateAll($this->User->id);
        if (empty($this->Auth->user('DefaultTeam.id')) === false && empty($this->Auth->user('id')) === false) {
            $this->GlRedis->deleteDeviceHash($this->Auth->user('DefaultTeam.id'), $this->Auth->user('id'));
        }
        $this->Mixpanel->track2SV(MixpanelComponent::TRACK_2SV_DISABLE);
        $this->Pnotify->outSuccess(__("Succeeded to cancel 2-Step Verification."));
        return $this->redirect($this->referer());
    }

    /**
     * リカバリコードを表示
     *
     * @return CakeResponse
     */
    function ajax_get_modal_recovery_code()
    {
        $this->_ajaxPreProcess();
        $recovery_codes = $this->User->RecoveryCode->getAll($this->Auth->user('id'));
        if (!$recovery_codes) {
            $success = $this->User->RecoveryCode->regenerate($this->Auth->user('id'));
            if (!$success) {
                throw new NotFoundException();
            }
            $recovery_codes = $this->User->RecoveryCode->getAll($this->Auth->user('id'));
        }
        $this->set('recovery_codes', $recovery_codes);
        $response = $this->render('User/modal_recovery_code');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    /**
     * リカバリコードを再生成
     */
    function ajax_regenerate_recovery_code()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');

        $success = $this->User->RecoveryCode->regenerate($this->Auth->user('id'));
        if (!$success) {
            return $this->_ajaxGetResponse([
                'error' => true,
                'msg'   => __("An error has occurred.")
            ]);
        }
        $recovery_codes = $this->User->RecoveryCode->getAll($this->Auth->user('id'));
        $codes = array_map(function ($v) {
            return $v['RecoveryCode']['code'];
        }, $recovery_codes);
        return $this->_ajaxGetResponse([
            'error' => false,
            'msg'   => __("Generated new recovery codes."),
            'codes' => $codes
        ]);
    }

    /**
     * select2のユーザ検索
     */
    function ajax_select2_get_circles_users()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = [];
        if (viaIsSet($query['term']) && viaIsSet($query['page_limit']) && viaIsSet($query['circle_type'])) {
            $res = $this->User->getUsersCirclesSelect2($query['term'], $query['page_limit'], $query['circle_type'],
                true);
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * select2の非公開サークル検索
     */
    function ajax_select2_get_secret_circles()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = [];
        if (viaIsSet($query['term']) && viaIsSet($query['page_limit'])) {
            $res = $this->User->getSecretCirclesSelect2($query['term'], $query['page_limit']);
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * チームに参加
     *
     * @param $token
     */
    function _joinTeam($token)
    {
        //トークン認証
        $invite = $this->Invite->verify($token, $this->Auth->user('id'));

        //チーム参加
        $this->User->TeamMember->add($this->Auth->user('id'), $invite['Invite']['team_id']);
        //デフォルトチーム設定
        $this->User->updateDefaultTeam($invite['Invite']['team_id']);
        //セッション更新
        $this->_refreshAuth();
        //チーム切換え
        $this->_switchTeam($invite['Invite']['team_id']);
        // 「チーム全体」サークルに追加
        // Circle と CircleMember の current_team_id を一時的に変更
        $tmp = $this->Circle->current_team_id;
        $this->Circle->current_team_id = $invite['Invite']['team_id'];
        $this->Circle->CircleMember->current_team_id = $invite['Invite']['team_id'];
        $teamAllCircle = $this->Circle->getTeamAllCircle();
        $this->Circle->CircleMember->joinNewMember($teamAllCircle['Circle']['id']);
        $this->Circle->current_team_id = $tmp;
        $this->Circle->CircleMember->current_team_id = $tmp;
        //cache削除
        Cache::delete($this->Circle->CircleMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false), 'team_info');
        //招待者に通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM, $invite['Invite']['id']);
        return $this->User->TeamMember->Team->findById($invite['Invite']['team_id']);
    }

    function _uservoiceSetSession()
    {
        if (isset($this->request->query['uv_login'])) {
            $this->Session->write('uv_status', $this->request->query);
        } else {
            $this->Session->delete('uv_status');
        }
    }

    public function ajax_get_user_detail($user_id)
    {
        $this->_ajaxPreProcess();
        $user_detail = $this->User->getDetail($user_id);
        return $this->_ajaxGetResponse($user_detail);
    }

    public function ajax_get_select2_circle_user_all()
    {
        $result = $this->User->getAllUsersCirclesSelect2();
        $this->_ajaxPreProcess();
        return $this->_ajaxGetResponse($result);
    }

    /**
     * メールアドレスが登録可能なものか確認
     *
     * @return CakeResponse
     */
    public function ajax_validate_email()
    {
        $this->_ajaxPreProcess();
        $email = $this->request->query('email');
        $valid = false;
        $message = '';
        if ($email) {
            // メールアドレスだけ validate
            $this->User->Email->create(['email' => $email]);
            $this->User->Email->validates(['fieldList' => ['email']]);
            if ($this->User->Email->validationErrors) {
                $message = $this->User->Email->validationErrors['email'][0];
            } else {
                $valid = true;
            }
        }
        return $this->_ajaxGetResponse([
            'valid'   => $valid,
            'message' => $message
        ]);
    }

    function view_goals()
    {
        $user_id = $this->_getRequiredParam('user_id');
        if (!$this->_setUserPageHeaderInfo($user_id)) {
            // ユーザーが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        $page_type = viaIsSet($this->request->params['named']['page_type']);

        $current_term = $this->Team->EvaluateTerm->getCurrentTermData();
        $current_id = $current_term['id'];

        $next_term = $this->Team->EvaluateTerm->getNextTermData();
        $next_id = $next_term['id'];

        $previous_term = $this->Team->EvaluateTerm->getPreviousTermData();
        $previous_id = $previous_term['id'];

        function show_date($start_date, $end_date, $all_timezone)
        {
            return date('Y/m/d', $start_date + $all_timezone * 3600) . " - " . date('Y/m/d',
                $end_date + $all_timezone * 3600);
        }

        $all_term = $this->Team->EvaluateTerm->getAllTerm();
        $all_id = array_column($all_term, 'id');
        $all_start_date = array_column($all_term, 'start_date');
        $all_end_date = array_column($all_term, 'end_date');
        $all_timezone = array_column($all_term, 'timezone');
        $all_term = array_map("show_date", $all_start_date, $all_end_date, $all_timezone);

        $term1 = array(
            $current_id  => __("Current Term"),
            $next_id     => __("Next Term"),
            $previous_id => __("Previous Term"),
        );
        $term2 = array_combine($all_id, $all_term);
        $term = $term1 + $term2;

        if (isset($this->request->params['named']['term_id'])) {
            $term_id = $this->request->params['named']['term_id'];
            $target_term = $this->Team->EvaluateTerm->findById($term_id);
            $start_date = $target_term['EvaluateTerm']['start_date'];
            $end_date = $target_term['EvaluateTerm']['end_date'];
        } else {
            $term_id = $current_id;
            $start_date = $this->Team->EvaluateTerm->getCurrentTermData()['start_date'];
            $end_date = $this->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        }

        $my_goals_count = $this->Goal->getMyGoals(null, 1, 'count', $user_id, $start_date, $end_date);
        $collabo_goals_count = $this->Goal->getMyCollaboGoals(null, 1, 'count', $user_id, $start_date, $end_date);
        $my_goals_count += $collabo_goals_count;
        $follow_goals_count = $this->Goal->getMyFollowedGoals(null, 1, 'count', $user_id, $start_date, $end_date);

        if ($page_type == "following") {
            $goals = $this->Goal->getMyFollowedGoals(null, 1, 'all', $user_id, $start_date, $end_date);
        } else {
            $goals = $this->Goal->getGoalsWithAction($user_id, MY_PAGE_ACTION_NUMBER, $start_date, $end_date);
        }
        $goals = $this->Goal->setIsCurrentTerm($goals);

        $is_mine = $user_id == $this->Auth->user('id') ? true : false;
        $display_action_count = MY_PAGE_ACTION_NUMBER;
        if ($is_mine) {
            $display_action_count--;
        }

        $term_base_url = Router::url([
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $user_id,
            'page_type'  => $page_type
        ]);

        $my_coaching_users = $this->User->TeamMember->getMyMembersList($this->my_uid);

        $this->set(compact(
            'term',
            'term_id',
            'term_base_url',
            'my_goals_count',
            'follow_goals_count',
            'page_type',
            'goals',
            'is_mine',
            'display_action_count',
            'my_coaching_users'
        ));
        return $this->render();
    }

    /**
     * ユーザーページ 投稿一覧
     *
     * @return CakeResponse
     */
    function view_posts()
    {
        $user_id = $this->_getRequiredParam('user_id');
        if (!$this->_setUserPageHeaderInfo($user_id)) {
            // ユーザーが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, [
            'user_id' => $user_id,
            'type'    => Post::TYPE_NORMAL
        ]);
        $team = $this->Team->getCurrentTeam();
        $this->set('item_created', $team['Team']['created']);
        $this->set('posts', $posts);
        $this->set('long_text', false);

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function view_actions()
    {
        $user_id = $this->_getRequiredParam('user_id');
        $page_type = $this->_getRequiredParam('page_type');
        $goal_id = viaIsSet($this->request->params['named']['goal_id']);
        if (!in_array($page_type, ['list', 'image'])) {
            $this->Pnotify->outError(__("Invalid screen transition."));
            $this->redirect($this->referer());
        }
        $params = [
            'author_id' => $user_id,
            'type'      => Post::TYPE_ACTION,
            'goal_id'   => $goal_id,
        ];
        $posts = [];
        switch ($page_type) {
            case 'list':
                $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $params);
                break;
            case 'image':
                $posts = $this->Post->get(1, MY_PAGE_CUBE_ACTION_IMG_NUMBER, null, null, $params);
                break;
        }
        $this->set(compact('posts'));
        if (!$this->_setUserPageHeaderInfo($user_id)) {
            // ユーザーが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $team = $this->Team->getCurrentTeam();
        $this->set('item_created', $team['Team']['created']);
        $this->layout = LAYOUT_ONE_COLUMN;
        $goal_ids = $this->Goal->Collaborator->getCollaboGoalList($user_id, true);
        $goal_select_options = $this->Goal->getGoalNameListByGoalIds($goal_ids, true, true);
        $goal_base_url = Router::url([
            'controller' => 'users',
            'action'     => 'view_actions',
            'user_id'    => $user_id,
            'page_type'  => $page_type
        ]);
        $this->set('long_text', false);
        $this->set(compact('goal_select_options', 'goal_id', 'goal_base_url'));
        return $this->render();
    }

    /**
     * ユーザーページ 基本情報
     *
     * @return CakeResponse
     */
    function view_info()
    {
        $user_id = $this->_getRequiredParam('user_id');

        if (!$this->_setUserPageHeaderInfo($user_id)) {
            // ユーザーが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function add_subscribe_email()
    {
        $this->request->allowMethod('post');
        /**
         * @var SubscribeEmail $SubscribeEmail
         */
        $SubscribeEmail = ClassRegistry::init('SubscribeEmail');
        if (!$SubscribeEmail->save($this->request->data)) {
            $this->Pnotify->outError($SubscribeEmail->validationErrors['email'][0]);
            return $this->redirect($this->referer());
        }
        $this->Pnotify->outSuccess(__('Registered email address.'));
        return $this->redirect($this->referer());
    }

    /**
     * ユーザページの上部コンテンツの表示に必要なView変数をセット
     *
     * @param $user_id
     *
     * @return bool
     */
    function _setUserPageHeaderInfo($user_id)
    {
        // ユーザー情報
        $user = $this->User->TeamMember->getByUserId($user_id);
        if (!$user) {
            // チームメンバーでない場合
            return false;
        }
        $this->set('user', $user);

        // 評価期間内の投稿数
        $term_start_date = $this->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $term_end_date = $this->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        $post_count = $this->Post->getCount($user_id, $term_start_date, $term_end_date);
        $this->set('post_count', $post_count);

        // 評価期間内のアクション数
        $action_count = $this->Goal->ActionResult->getCount($user_id, $term_start_date, $term_end_date);
        $this->set('action_count', $action_count);

        // 投稿に対するいいねの数
        $post_like_count = $this->Post->getLikeCountSumByUserId($user_id, $term_start_date, $term_end_date);
        // コメントに対するいいねの数
        $comment_like_count = $this->Post->Comment->getLikeCountSumByUserId($user_id, $term_start_date, $term_end_date);
        $this->set('like_count', $post_like_count + $comment_like_count);

        return true;
    }
}
