<?php
App::uses('AppController', 'Controller');
App::uses('Post', 'Model');
App::import('Service', 'GoalService');

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
        $this->Auth->allow('register', 'login', 'verify', 'logout', 'password_reset', 'token_resend', 'sent_mail',
            'accept_invite', 'register_with_invite', 'registration_with_set_password', 'two_fa_auth',
            'two_fa_auth_recovery',
            'add_subscribe_email', 'ajax_validate_email');
    }

    /**
     * Common login action
     *
     * @return void
     */
    public function login()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        if ($this->Auth->user()) {
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
        $redirect_url = ($this->Session->read('Auth.redirect')) ? $this->Session->read('Auth.redirect') : "/";
        $this->request->data = $this->Session->read('preAuthPost');
        if ($this->Auth->login()) {
            $this->Session->delete('preAuthPost');
            $this->Session->delete('2fa_secret');
            $this->Session->delete('user_id');
            $this->Session->delete('team_id');
            if ($this->Session->read('referer_status') === REFERER_STATUS_INVITED_USER_EXIST) {
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_EXIST);
            } else {
                $this->Session->write('referer_status', REFERER_STATUS_LOGIN);
            }
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

    public function register_with_invite()
    {

        $step = isset($this->request->params['named']['step']) ? (int)$this->request->params['named']['step'] : 1;
        if (!($step === 1 or $step === 2)) {
            $this->Pnotify->outError(__('Invalid access'));
            return $this->redirect('/');
        }

        $profile_template = 'register_prof_with_invite';
        $password_template = 'register_password_with_invite';

        $this->layout = LAYOUT_ONE_COLUMN;

        try {
            if (!isset($this->request->params['named']['invite_token'])) {
                throw new RuntimeException(__("The invitation token is incorrect. Check your email again."));
            }
            //トークンが有効かチェック
            $this->Invite->confirmToken($this->request->params['named']['invite_token']);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect('/');
        }
        $invite = $this->Invite->getByToken($this->request->params['named']['invite_token']);
        $team = $this->Team->findById($invite['Invite']['team_id']);
        $this->set('team_name', $team['Team']['name']);

        //batch case
        if ($user = $this->User->getUserByEmail($invite['Invite']['email'])) {
            // Set user info to view value
            $this->set('first_name', $user['User']['first_name']);
            $this->set('last_name', $user['User']['last_name']);
            $this->set('birth_day', $user['User']['birth_day']);
        }

        if (!$this->request->is('post')) {
            if ($step === 2) {
                return $this->render($password_template);
            }
            $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
            $this->set(compact('last_first'));
            return $this->render($profile_template);
        }

        //Sessionに保存してパスワード入力画面に遷移
        if ($step === 1) {
            //プロフィール入力画面の場合
            //validation
            if ($this->User->validates($this->request->data)) {
                //store to session
                $this->Session->write('data', $this->request->data);
                //パスワード入力画面にリダイレクト
                return $this->redirect(
                    [
                        'action'       => 'register_with_invite',
                        'step'         => 2,
                        'invite_token' => $this->request->params['named']['invite_token']
                    ]);
            } else {
                //エラーメッセージ
                $this->Pnotify->outError(__('Failed to save data.'));
                return $this->render($profile_template);
            }
        }
        //パスワード入力画面の場合

        //session存在チェック
        if (!$this->Session->read('data')) {
            $this->Pnotify->outError(__('Invalid access'));
            return $this->redirect('/');
        }

        //sessionデータとpostのデータとマージ
        $data = Hash::merge($this->Session->read('data'), $this->request->data);
        //batch case
        if ($user) {
            $user_id = $user['User']['id'];

            // Disabled user email validation
            // Because in batch case, email is already registered
            $email = $this->User->Email->getNotVerifiedEmail($user_id);
            $email_from_email_table = Hash::get($email, 'Email.email');
            $email_from_invite_table = $invite['Invite']['email'];
            if ($email_from_email_table === $email_from_invite_table) {
                unset($this->User->Email->validate['email']);
            }
            // Set user info to register data
            $data['User']['id'] = $user_id;
            $data['User']['no_pass_flg'] = false;
            $data['Email'][0]['Email']['id'] = $email['Email']['id'];
        }
        //email
        $data['Email'][0]['Email']['email'] = $invite['Invite']['email'];
        //タイムゾーンをセット
        if (isset($data['User']['local_date'])) {
            //ユーザのローカル環境から取得したタイムゾーンをセット
            $timezone = $this->Timezone->getLocalTimezone($data['User']['local_date']);
            $data['User']['timezone'] = $timezone;
            //自動タイムゾーン設定フラグをoff
            $data['User']['auto_timezone_flg'] = false;
        }
        //言語を保存
        $data['User']['language'] = $this->Lang->getLanguage();
        // ユーザ本登録
        if (!$this->User->userRegistration($data)) {
            //姓名の並び順をセット
            $last_first = in_array($this->Lang->getLanguage(), $this->User->langCodeOfLastFirst);
            $this->set(compact('last_first'));
            return $this->render($password_template);
        }
        //ログイン
        $user_id = $this->User->getLastInsertID() ? $this->User->getLastInsertID() : $user_id;
        $this->_autoLogin($user_id, true);
        // flash削除
        // csvによる招待のケースで_authLogin()の処理中に例外メッセージが吐かれるため、
        // 一旦ここで例外メッセージを表示させないためにFlashメッセージをremoveする
        $this->Session->delete('Message.pnotify');
        //チーム参加
        $this->_joinTeam($this->request->params['named']['invite_token']);
        //ホーム画面でモーダル表示
        $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
        //top画面に遷移
        return $this->redirect('/');
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
            //ユーザー情報更新
            //チームメンバー情報を付与
            if ($this->User->saveAll($this->request->data)) {
                //ログインし直し。
                $this->_autoLogin($this->Auth->user('id'), true);
                //言語設定
                $this->_setAppLanguage();
                //セットアップガイドステータスの更新
                $this->updateSetupStatusIfNotCompleted();

                $this->Pnotify->outSuccess(__("Saved user setting."));
                $this->redirect('/users/settings');
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

            // By email
            if (!$this->Invite->isUser($token)) {
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_EMAIL);
                return $this->redirect(['action' => 'register_with_invite', 'invite_token' => $token]);
            }
            //PreRegistered User
            if ($this->Invite->isUserPreRegistered($token)) {
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_EXIST_BY_EMAIL);
                return $this->redirect(['action' => 'register_with_invite', 'invite_token' => $token]);
            }

            // By batch setup
            if ($this->Invite->isByBatchSetup($token)) {
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_CSV);
                return $this->redirect(['action' => 'register_with_invite', 'invite_token' => $token]);
            }

            if (!$this->Auth->user()) {
                $this->Auth->redirectUrl(['action' => 'accept_invite', $token]);
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_EXIST);
                return $this->redirect(['action' => 'login']);
            }

            // Not allow invite me
            if (!$this->Invite->isForMe($token, $this->Auth->user('id'))) {
                throw new RuntimeException(__("This invitation isn't not for you."));
            }

            $team = $this->_joinTeam($token);

            $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_EXIST);
            $this->Pnotify->outSuccess(__("Joined %s.", $team['Team']['name']));
            return $this->redirect("/");
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
     * select2用に加工したユーザ情報を取得
     *
     * @param $userId
     *
     * @return CakeResponse|null
     */
    function ajax_select2_get_user_detail($userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            return $this->_ajaxGetResponse([]);
        }
        // ユーザ詳細情報取得
        $user = $this->User->getDetail($userId);
        if (empty($user)) {
            return $this->_ajaxGetResponse([]);
        }
        // レスポンス用にユーザ詳細情報を加工
        $res = $this->User->makeSelect2User($user);
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
            if (!Hash::get($this->request->data, 'User.2fa_code')) {
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
        $this->Flash->set(null,
            ['element' => 'flash_click_event', 'params' => ['id' => 'ShowRecoveryCodeButton'], 'key' => 'click_event']);
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
        if (Hash::get($query, 'term') && Hash::get($query, 'page_limit') && Hash::get($query, 'circle_type')) {
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
        if (Hash::get($query, 'term') && Hash::get($query, 'page_limit')) {
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
        App::import('Service', 'ExperimentService');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');
        if ($ExperimentService->isDefined('CircleDefaultSettingOff')) {
            $this->Circle->CircleMember->joinNewMember($teamAllCircle['Circle']['id'], false, false);
        } else {
            $this->Circle->CircleMember->joinNewMember($teamAllCircle['Circle']['id']);
        }

        $this->Circle->current_team_id = $tmp;
        $this->Circle->CircleMember->current_team_id = $tmp;
        //cache削除
        Cache::delete($this->Circle->CircleMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false), 'team_info');
        //招待者に通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM, $invite['Invite']['id']);
        return $this->User->TeamMember->Team->findById($invite['Invite']['team_id']);
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
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $user_id = Hash::get($this->request->params, "named.user_id");
        if (!$user_id || !$this->_setUserPageHeaderInfo($user_id)) {
            // ユーザーが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        $page_type = Hash::get($this->request->params, 'named.page_type');

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

        $goals = $GoalService->processGoals($goals);
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
        $user_id = Hash::get($this->request->params, "named.user_id");
        if (!$user_id || !$this->_setUserPageHeaderInfo($user_id)) {
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
        $user_id = Hash::get($this->request->params, "named.user_id");
        $page_type = Hash::get($this->request->params, "named.page_type");
        $goal_id = Hash::get($this->request->params, 'named.goal_id');
        if (!$user_id || !in_array($page_type, ['list', 'image'])) {
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
        $goal_ids = $this->Goal->GoalMember->getCollaboGoalList($user_id, true);
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
        $user_id = Hash::get($this->request->params, "named.user_id");

        if (!$user_id || !$this->_setUserPageHeaderInfo($user_id)) {
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
