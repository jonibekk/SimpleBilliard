<?php
App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @property User            $User
 * @property Invite          $Invite
 * @property TwoFaComponent  $TwoFa
 */
class UsersController extends AppController
{
    public $uses = [
        'User',
        'Invite',
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
                           'accept_invite', 'registration_with_set_password');

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
        $this->_uservoiceSetSession();
        $redirect_url = ($this->Session->read('Auth.redirect')) ? $this->Session->read('Auth.redirect') : "/";
        $this->layout = LAYOUT_ONE_COLUMN;

        if ($this->Auth->user()) {
            $this->_ifFromUservoiceRedirect();
            return $this->redirect('/');
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        if ($this->Auth->login()) {
            $this->_refreshAuth();
            $this->_setAfterLogin();
            $this->Pnotify->outSuccess(__d('notify', "%sさん、こんにちは。", $this->Auth->user('display_username')),
                                       ['title' => __d('notify', "ログイン成功")]);
            return $this->redirect($redirect_url);
        }
        else {
            $this->Pnotify->outError(__d('notify', "メールアドレスもしくはパスワードが正しくありません。"));
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
        $this->Pnotify->outInfo(__d('notify', "%sさん、またお会いしましょう。", $user['display_username']),
                                ['title' => __d('notify', "ログアウトしました")]);
        return $this->redirect($this->Auth->logout());
    }

    /**
     * User register action
     *
     * @return void
     */
    public function register()
    {
        //TODO basic認証 本番公開後に外す
        if ((ENV_NAME == "www" || ENV_NAME == "stg") && !isset($this->request->params['named']['invite_token'])) {
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
        $this->_autoLogin($this->User->getLastInsertID());
        //チーム参加
        $this->_joinTeam($this->request->params['named']['invite_token']);
        //ホーム画面でモーダル表示
        $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
        //プロフィール画面に遷移
        return $this->redirect(['action' => 'add_profile', 'invite_token' => $this->request->params['named']['invite_token']]);

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
                throw new RuntimeException(__d('gl', "トークンが正しくありません。"));
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
                $this->Pnotify->outError(__d('gl', "メールアドレスが一致しません。招待が届いたメールアドレスを入力してください。"));
                return $this->render();
            }
            $user = $this->User->getUserByEmail($this->request->data['Email']['email']);
            $this->Invite->verify($this->request->params['named']['invite_token']);
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
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect("/");
        }
        else {
            //チーム作成ページへリダイレクト
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
            $this->Pnotify->outError($e->getMessage() . "\n" . __d('gl', "メールアドレス変更を一度キャンセルし、再度変更してください。"));
            //トークン再送ページへ
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect(['action' => 'settings']);
        }
        $this->User->commit();
        $this->_autoLogin($this->Auth->user('id'));
        $this->Pnotify->outSuccess(__d('gl', "メールアドレスの変更が正常に完了しました。"));
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
                $this->Pnotify->outSuccess(__d('gl', "パスワード再設定のメールを送信しました。ご確認ください。"),
                                           ['title' => __d('gl', "メールを送信しました")]);
            }
            return $this->render('password_reset_request');
        }

        // Token existing case
        $user_email = $this->User->checkPasswordToken($token);

        if (!$user_email) {
            $this->Pnotify->outError(__d('gl', "パスワードトークンが正しくないか、期限切れの可能性があります。もう一度、再設定用のメールを送信してください。"),
                                     ['title' => __d('gl', "トークンの認証に失敗しました。")]);
            return $this->redirect(['action' => 'password_reset']);
        }

        if (!$this->request->is('post')) {
            return $this->render('password_reset');
        }

        $successPasswordReset = $this->User->passwordReset($user_email, $this->request->data);
        if ($successPasswordReset) {
            // Notify to user reset password
            $this->GlEmail->sendMailCompletePasswordReset($user_email['User']['id']);
            $this->Pnotify->outSuccess(__d('gl', "新しいパスワードでログインしてください。"),
                                       ['title' => __d('gl', 'パスワードを設定しました')]);
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
                $this->Pnotify->outSuccess(__d('gl', "メールアドレス認証用のメールを送信しました。ご確認ください。"),
                                           ['title' => __d('gl', "メールを送信しました")]);
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
            //request->dataに入っていないデータを表示しなければ行けない為、マージ
            $this->request->data['User'] = array_merge($me['User'],
                                                       isset($this->request->data['User']) ? $this->request->data['User'] : []);
            $this->User->id = $this->Auth->user('id');
            //チームメンバー情報を付与
            if ($this->User->saveAll($this->request->data)) {
                //ログインし直し。
                $this->_autoLogin($this->Auth->user('id'), true);
                //言語設定
                $this->_setAppLanguage();
                $me = $this->_getMyUserDataForSetting();
                $this->request->data = $me;

                $this->Pnotify->outSuccess(__d('gl', "ユーザ設定を保存しました。"));
            }
            else {
                $this->Pnotify->outError(__d('gl', "ユーザ設定の保存に失敗しました。"));
            }
        }
        else {
            $this->request->data = $me;
        }
        $this->layout = LAYOUT_SETTING;
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
            $this->Pnotify->outError($e->getMessage(), ['title' => __d('gl', "パスワードの変更に失敗しました")]);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Pnotify->outSuccess(__d('gl', "パスワードを変更しました。"));

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

        $this->Pnotify->outInfo(__d('gl', "認証用のメールを送信しました。送信されたメールを確認し、認証してください。"));
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
                throw new RuntimeException(__d('exception', "別のユーザ宛のチーム招待です。"));
            }

            $team = $this->_joinTeam($token);
            $this->Pnotify->outSuccess(__d('gl', "チーム「%s」に参加しました。", $team['Team']['name']));
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
        $res = [];
        if (isset($query['term']) && !empty($query['term']) && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $res = $this->User->getUsersSelect2($query['term'], $query['page_limit']);
        }
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_modal_2fa_register()
    {
        $this->_ajaxPreProcess();
        if ($this->Session->read('2fa_secret_key')) {
            $google_2fa_secret_key = $this->Session->read('2fa_secret_key');
        }
        else {
            $google_2fa_secret_key = $this->TwoFa->generateSecretKey();
            $this->Session->write('2fa_secret_key', $google_2fa_secret_key);
        }
        $url_2fa = $this->TwoFa->getQRCodeGoogleUrl('Goalous',
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
                throw new RuntimeException(__d('gl', "エラーが発生しました。"));
            }
            if (!viaIsSet($this->request->data['User']['2fa_code'])) {
                throw new RuntimeException(__d('gl', "エラーが発生しました。"));
            }
            if (!$this->TwoFa->verifyKey($secret_key, $this->request->data['User']['2fa_code'])) {
                throw new RuntimeException(__d('gl', "コードが正しくありません。"));
            }
            //2要素認証コードの登録
            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('2fa_secret', $secret_key);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        $this->Session->delete('2fa_secret_key');
        $this->Pnotify->outSuccess(__d('gl', "２要素認証の登録が完了しました。"));
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
        $this->Pnotify->outSuccess(__d('gl', "２要素認証を解除しました。"));
        return $this->redirect($this->referer());
    }

    /**
     * select2のユーザ検索
     */
    function ajax_select2_get_circles_users()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = [];
        if (isset($query['term']) && !empty($query['term']) && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $res = $this->User->getUsersCirclesSelect2($query['term'], $query['page_limit']);
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
        $invite = $this->Invite->verify($token);

        //チーム参加
        $this->User->TeamMember->add($this->Auth->user('id'), $invite['Invite']['team_id']);
        //デフォルトチーム設定
        $this->User->updateDefaultTeam($invite['Invite']['team_id']);
        //セッション更新
        $this->_refreshAuth();
        //チーム切換え
        $this->_switchTeam($invite['Invite']['team_id']);
        return $this->User->TeamMember->Team->findById($invite['Invite']['team_id']);
    }

    /*
     * 自動でログインする
     */
    public function _autoLogin($user_id, $is_not_change_current_team = false)
    {
        //リダイレクト先を退避
        $redirect = null;
        if ($this->Session->read('Auth.redirect')) {
            $redirect = $this->Session->read('Auth.redirect');
        }
        $current_team_id = $this->Session->read('current_team_id');
        //自動ログイン
        if ($this->_refreshAuth($user_id)) {
            //リダイレクト先をセッションに保存
            $this->Session->write('redirect', $redirect);
            if ($is_not_change_current_team) {
                $this->_setAfterLogin($current_team_id);
            }
            else {
                $this->_setAfterLogin();
            }
            return true;
        }
        else {
            return false;
        }
    }

    function _uservoiceSetSession()
    {
        if (isset($_GET['uv_login'])) {
            $this->Session->write('uv_status', $_GET);
        }
        else {
            $this->Session->delete('uv_status');
        }
    }

    /**
     * ログイン後に実行する
     *
     * @param null $team_id
     */
    public function _setAfterLogin($team_id = null)
    {
        $this->User->id = $this->Auth->user('id');
        $this->User->saveField('last_login', REQUEST_TIMESTAMP);
        if (!$team_id) {
            $team_id = $this->Auth->user('default_team_id');
        }
        $this->_setDefaultTeam($team_id);
        if ($this->Session->read('current_team_id')) {
            $this->User->TeamMember->updateLastLogin($this->Session->read('current_team_id'), $this->Auth->user('id'));
        }
        $this->User->_setSessionVariable();
        $this->Mixpanel->setUser($this->User->id);

        $this->_ifFromUservoiceRedirect();

    }

    public function _ifFromUservoiceRedirect()
    {
        $uservoice_token = $this->Uservoice->getToken();
        //uservoiceのメールから来た場合はリダイレクト
        if ($this->Session->read('uv_status')) {
            if ($this->Session->read('uv_status.uv_ssl')) {
                $protocol = "https://";
            }
            else {
                $protocol = "http://";
            }
            $redirect_url = $protocol . USERVOICE_SUBDOMAIN . ".uservoice.com/login_success?sso=" . $uservoice_token;
            $this->Session->delete('uv_status');
            $this->redirect($redirect_url);
        }

    }

    public function _setDefaultTeam($team_id)
    {
        try {
            $this->User->TeamMember->permissionCheck($team_id, $this->Auth->user('id'));
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $team_list = $this->User->TeamMember->getActiveTeamList($this->Auth->user('id'));
            $set_team_id = !empty($team_list) ? key($team_list) : null;
            $this->Session->write('current_team_id', $set_team_id);
            $this->User->updateDefaultTeam($set_team_id, true, $this->Auth->user('id'));
            return false;
        }
        $this->Session->write('current_team_id', $team_id);
    }

    /**
     * ajaxで投稿数を取得(defaultで自分の投稿数および今期)
     *
     * @param string $type
     *
     * @return CakeResponse
     */
    public function ajax_get_post_count($type = "me")
    {
        $start_date = isset($this->request->params['named']['start_date']) ? $this->request->params['named']['start_date'] : null;
        $end_date = isset($this->request->params['named']['end_date']) ? $this->request->params['named']['end_date'] : null;
        if (!$start_date && !$end_date) {
            //デフォルトで今期
            $start_date = $this->User->TeamMember->Team->getCurrentTermStartDate();
            $end_date = $this->User->TeamMember->Team->getCurrentTermEndDate();
        }
        $post_count = $this->Post->getCount($type, $start_date, $end_date);
        $this->_ajaxPreProcess();

        $result = [
            'count' => $post_count
        ];
        return $this->_ajaxGetResponse($result);
    }

    /**
     * ajaxでアクション数を取得(defaultで自分のアクション数および今期)
     *
     * @param string $type
     *
     * @return CakeResponse
     */
    public function ajax_get_action_count($type = "me")
    {
        $start_date = isset($this->request->params['named']['start_date']) ? $this->request->params['named']['start_date'] : null;
        $end_date = isset($this->request->params['named']['end_date']) ? $this->request->params['named']['end_date'] : null;
        if (!$start_date && !$end_date) {
            //デフォルトで今期
            $start_date = $this->User->TeamMember->Team->getCurrentTermStartDate();
            $end_date = $this->User->TeamMember->Team->getCurrentTermEndDate();
        }
        $action_count = $this->Goal->ActionResult->getCount($type, $start_date, $end_date);
        $this->_ajaxPreProcess();

        $result = [
            'count' => $action_count
        ];
        return $this->_ajaxGetResponse($result);
    }

}
