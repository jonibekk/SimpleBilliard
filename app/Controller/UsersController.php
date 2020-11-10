<?php
App::uses('AppController', 'Controller');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Post', 'Model');
App::uses('Device', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('AppUtil', 'Util');
App::import('Service', 'GoalService');
App::import('Service', 'UserService');
App::import('Service', 'CircleService');
App::import('Service', 'TermService');
App::import('Service', 'TeamMemberService');
App::import('Service', 'ExperimentService');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentTiming');

use Goalous\Enum as Enum;

/**
 * Users Controller
 *
 * @property User           $User
 * @property Invite         $Invite
 * @property Circle         $Circle
 * @property TeamMember     $TeamMember
 * @property TwoFaComponent $TwoFa
 */
class UsersController extends AppController
{
    public $uses = [
        'User',
        'Invite',
        'Circle',
        'TeamMember'
    ];
    public $components = [
        'TwoFa',
        'Mention'
    ];

    const ALLOW_DEMO_SAVING_USER_SETTINGS = [
        'User.language'
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('register', 'login', 'agree_and_login', 'verify', 'logout', 'password_reset', 'token_resend', 'sent_mail',
            'accept_invite', 'register_with_invite', 'registration_with_set_password', 'two_fa_auth',
            'two_fa_auth_recovery',
            'add_subscribe_email', 'ajax_validate_email');

        $this->_checkAdmin(['invite']);

        // GL-7364
        // TODO: remove these processing. but there is a problem that SecurityComponent.blackhole error occurs after update core.php `session.cookie_domain` to enable to share cookie across sub domains.
        if ($this->request->params['action'] == 'login') {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
    }

    /**
     * This route `/users/agree_and_login` is for bulk registration.
     * User will land here from url written in the email sent by bulk registration.
     * Check the document for spec.
     * https://isao.esa.io/#path=%2FGoalous%2FSpecification%2FUsers%2FUser%20bulk%20registration%20and%20link%20to%20a%20team
     *
     * @return \Cake\Network\Response|CakeResponse|null
     */
    public function agree_and_login()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        if ($this->Auth->user()) {
            return $this->redirect('/');
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        //メアド、パスの認証(セッションのストアはしていない)
        $userInfo = $this->Auth->identify($this->request, $this->response);
        $response = $this->validateAuth($userInfo);
        if (!empty($response)) {
            return $response;
        }

        $this->Session->write('preAuthPost', $this->request->data);

        // Make user agreed to latest term of service
        /* @var TermsOfService $TermsOfService */
        $TermsOfService = ClassRegistry::init('TermsOfService');
        /** @var User $User */
        $User = ClassRegistry::init("User");
        $userId = $userInfo['id'];
        $termsOfService = $TermsOfService->getCurrent();
        $User->updateAgreedTermsOfServiceId($userId, $termsOfService['id']);

        $teamIdSwitch = $this->request->query("team_id") ?? $userInfo['DefaultTeam']['id'];
        $this->Session->write('invited_team_id', $teamIdSwitch);

        $response = $this->confirm2faAuth($userInfo, $teamIdSwitch);
        if (!empty($response)) {
            return $response;
        }

        return $this->_afterAuthSessionStore();
    }

    private function confirm2faAuth(array $userInfo, ?int $teamId)
    {
        $is2faAuthEnabled = true;
        // 2要素認証設定OFFの場合
        // [Note]
        // Refer: https://jira.goalous.com/browse/GL-6874
        if (is_null($userInfo['2fa_secret'])) {
            $is2faAuthEnabled = false;
        }

        //２要素設定有効なら
        if ($is2faAuthEnabled) {
            $this->Session->write('2fa_secret', $userInfo['2fa_secret']);
            $this->Session->write('user_id', $userInfo['id']);
            $this->Session->write('team_id', $teamId);
            return $this->redirect(['action' => 'two_fa_auth']);
        }
        return null;
    }

    /**
     * @param array|bool $userInfo
     * @return CakeResponse|null
     */
    private function validateAuth($userInfo)
    {
        //account lock check
        $ipAddress = $this->request->clientIp();
        $isAccountLocked = $this->GlRedis->isAccountLocked($this->request->data['User']['email'], $ipAddress);
        if ($isAccountLocked) {
            $this->Notification->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
                ACCOUNT_LOCK_TTL / 60));
            return $this->render();
        }
        if (!$userInfo) {
            $this->GlRedis->incrementLoginFailedCount($this->request->data['User']['email'], $ipAddress);
            $this->Notification->outError(__("Email address or Password is incorrect."));
            return $this->render();
        }
        return null;
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
            if (IS_DEMO) {
                return $this->render('demo_login');
            }
            return $this->render();
        }

        if (IS_DEMO) {
            App::uses('LangHelper', 'View/Helper');
            $Lang = new LangHelper(new View());
            $lang = $Lang->getLangCode();

            if ($lang === LangHelper::LANG_CODE_JP) {
                $this->request->data['User'] = [
                    'email'           => 'demo.goalous@gmail.com',
                    'password'        => 'DemoDemo01',
                    'installation_id' => 'no_value',
                    'app_version'     => 'no_value'
                ];
            } else {
                $this->request->data['User'] = [
                    'email'           => 'demo.goalous+EN@gmail.com',
                    'password'        => 'DemoDemo01',
                    'installation_id' => 'no_value',
                    'app_version'     => 'no_value'
                ];
            }
        }

        //メアド、パスの認証(セッションのストアはしていない)
        $userInfo = $this->Auth->identify($this->request, $this->response);
        $response = $this->validateAuth($userInfo);
        if (!empty($response)) {
            return $response;
        }

        // Prevent bulk registered user to login from "/users/login"
        // if user have not login from "/users/agree_and_login" .
        $isUserActive = !empty($userInfo['active_flg']);
        $isNotAgreedToAnyTerm = empty($userInfo['agreed_terms_of_service_id']);

        if ($isUserActive && $isNotAgreedToAnyTerm) {
            $this->Notification->outError(__("Please agree to the term of service."));
            return $this->redirect('/users/agree_and_login');
        }

        $this->Session->write('preAuthPost', $this->request->data);

        //デバイス情報を保存する
        $user_id = $userInfo['id'];
        $installationId = $this->request->data['User']['installation_id'];
        if ($installationId == "no_value") {
            $installationId = null;
        }
        $app_version = $this->request->data['User']['app_version'];
        if ($app_version == "no_value") {
            $app_version = null;
        }
        if (!empty($installationId)) {
            try {
                $this->NotifyBiz->saveDeviceInfo($user_id, $installationId, $app_version);
                // Storing installationId for deleting installation id when logout in mobile app.
                $this->Session->write('installationId', $installationId);
            } catch (RuntimeException $e) {
                $this->log([
                    'where'           => 'login page',
                    'error_msg'       => $e->getMessage(),
                    'user_id'         => $user_id,
                    'installation_id' => $installationId,
                ]);
            }
        }
        $response = $this->confirm2faAuth($userInfo, $userInfo['DefaultTeam']['id']);
        if (!empty($response)) {
            return $response;
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
            $this->Notification->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $is_account_locked = $this->GlRedis->isTwoFaAccountLocked($this->Session->read('user_id'),
            $this->request->clientIp());
        if ($is_account_locked) {
            $this->Notification->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
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
            $this->Notification->outError(__("Incorrect 2fa code."));
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
            $this->Notification->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
        }

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $is_account_locked = $this->GlRedis->isTwoFaAccountLocked($this->Session->read('user_id'),
            $this->request->clientIp());
        if ($is_account_locked) {
            $this->Notification->outError(__("Your account is tempolary locked. It will be unlocked after %s mins.",
                ACCOUNT_LOCK_TTL / 60));
            return $this->render();
        }

        // 入力されたコードが利用可能なリカバリーコードか確認
        $code = str_replace(' ', '', $this->request->data['User']['recovery_code']);
        $row = $this->User->RecoveryCode->findUnusedCode($this->Session->read('user_id'), $code);
        if (!$row) {
            $this->Notification->outError(__("Incorrect recovery code."));
            return $this->render();
        }

        // コードを使用済にする
        $res = $this->User->RecoveryCode->useCode($row['RecoveryCode']['id']);
        if (!$res) {
            $this->Notification->outError(__("An error has occurred."));
            return $this->render();
        }

        $this->GlRedis->saveDeviceHash($this->Session->read('team_id'), $this->Session->read('user_id'));
        return $this->_afterAuthSessionStore();
    }

    function set_session()
    {
        $redirect_url = ($this->Session->read('Auth.redirect')) ? $this->Session->read('Auth.redirect') : "/";
        $this->set('redirect_url', $redirect_url);

        $teamId = $this->Auth->user('default_team_id');
        $userId = $this->Auth->user('id');
        $sesId = $this->Session->id();

        $this->set('team', $teamId);
        $this->set('user', $userId);
        $this->set('ses', $sesId);

        $mapSesAndJwt = $this->GlRedis->getMapSesAndJwt($teamId, $userId, $sesId);
        if (empty($mapSesAndJwt)) {
            $jwt = $this->GlRedis->saveMapSesAndJwt($teamId, $userId, $sesId);
            $this->set('jwt_token', $jwt->token());
        } else {
            $this->set('jwt_token', $mapSesAndJwt);
        }

        $this->layout = false;
        return $this->render();
    }

    function _afterAuthSessionStore()
    {
        $redirect_url = "/users/set_session";
        $this->request->data = $this->Session->read('preAuthPost');
        if ($this->Auth->login()) {
            $this->Session->delete('preAuthPost');
            $this->Session->delete('2fa_secret');
            $this->Session->delete('user_id');
            $this->Session->delete('team_id');
            if ($this->Session->read('referer_status') === REFERER_STATUS_INVITED_USER_EXIST) {
                //If default_team_id is deleted, replace with new one
                $teamId = $this->current_team_id;
                if (empty($teamId)) {
                    $teamId = $this->Auth->user('default_team_id');
                }

                $userId = $this->Auth->user('id');

                $invitedTeamId = $this->Session->read('invited_team_id');
                if (empty($invitedTeamId)) {
                    $this->Notification->outError(__("Error, failed to invite."));
                    GoalousLog::error("Empty invited team ID for user $userId");
                    return $this->redirect("/");
                }

                //If default team is deleted or if user is not active in current team
                if (empty($this->TeamMember->isActive($userId, $teamId))) {
                    /** @var UserService $UserService */
                    $UserService = ClassRegistry::init('UserService');
                    if (!$UserService->updateDefaultTeam($userId, $invitedTeamId)) {
                        $this->Notification->outError(__("Error, failed to invite."));
                        GoalousLog::error("Failed updating default team ID $teamId to $invitedTeamId of user $userId");
                        return $this->redirect("/");
                    }
                }
                $activeTeams = $this->Team->TeamMember->getActiveTeamMembersList();
                if (empty($activeTeams)) {
                    $this->Session->write('current_team_id', $invitedTeamId);
                }
                $token = $this->Session->read('Auth.redirect.0');
                if ($token) {
                    $this->accept_invite_temporary($token);
                }
            } else {
                $this->Session->write('referer_status', REFERER_STATUS_LOGIN);
            }

            if ($this->is_mb_app) {
                // If mobile app, updating setup guide for installation of app.
                // It should be called from here. Because, `updateSetupStatusIfNotCompleted()` uses Session Data.
                $this->_updateSetupStatusIfNotCompleted();
            }

            $this->_refreshAuth();
            $this->_setAfterLogin();

            if (!empty($teamId = $this->Session->read('invited_team_id'))) {
                $this->Session->write('current_team_id', $teamId);
            }

            // reset login failed count
            $ipAddress = $this->request->clientIp();
            $this->GlRedis->resetLoginFailedCount($this->request->data['User']['email'], $ipAddress);

            $this->Notification->outSuccess(__("Hello %s.", $this->Auth->user('display_username')),
                ['title' => __("Succeeded to login")]);
            $this->Session->delete('invited_team_id');
            return $this->redirect($redirect_url);
        } else {
            $this->Notification->outError(__("Error. Try to login again."));
            return $this->redirect(['action' => 'login']);
        }

    }

    /**
     * Common logout action
     */
    public function logout()
    {
        $user = $this->Auth->user();

        //Need to put the notification between logout process & the redirect
        //If not notification can't reach the frontend
        $logoutRedirect = $this->logoutProcess();

        if ($user) {
            $this->Notification->outInfo(__("See you %s", $user['display_username']),
                ['title' => __("Logged out")]);
        }

        return $this->redirect($logoutRedirect);
    }

    /**
     * ユーザー登録兼チームジョイン
     * - このメソッドは未登録ユーザーがチーム招待された場合にだけ呼ばれる
     * - ここで処理するチーム招待の種類は以下二つ。
     *  - CSVによる招待(ユーザー仮登録状態)
     *  - メールによる招待
     * - この中で呼ばれる_joinTeam()メソッド内でトランザクションを張っている
     * TODO: このメソッド中のユーザー登録処理にてトランザクションが張られていないため、
     *       チームジョインが失敗した際のユーザー情報ロールバック処理をベタ書きしてしまっている。
     *       ユーザー登録/チーム参加処理のリファクタとトランザクション処理の追加実装が必要。
     *
     * @return
     */
    public function register_with_invite()
    {

        $step = isset($this->request->params['named']['step']) ? (int)$this->request->params['named']['step'] : 1;
        if (!($step === 1 or $step === 2)) {
            $this->Notification->outError(__('Invalid access'));
            return $this->redirect('/');
        }

        $profileTemplate = 'register_prof_with_invite';
        $passwordTemplate = 'register_password_with_invite';

        $this->layout = LAYOUT_ONE_COLUMN;

        // トークンチェック
        try {
            // トークン存在チェック
            if (!isset($this->request->params['named']['invite_token'])) {
                throw new Exception(sprintf("The invitation token is not exist. params: %s"
                    , var_export($this->request->params, true)));
            }
            //トークンが有効かチェック
            $confirmRes = $this->Invite->confirmToken($this->request->params['named']['invite_token']);
            if ($confirmRes !== true) {
                throw new Exception(sprintf("The invitation token is not available. confirmMessage: %s"
                    , var_export($confirmRes, true)));
            }
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $this->Notification->outError(__("The invitation token is incorrect. Check your email again."));
            return $this->redirect('/');
        }

        $invite = $this->Invite->getByToken($this->request->params['named']['invite_token']);
        $team = $this->Team->findById($invite['Invite']['team_id']);
        $this->set('team_name', $team['Team']['name']);
        $this->set('birthday_class', '');

        if (!$this->request->is('post')) {
            if ($step === 2) {
                return $this->render($passwordTemplate);
            }
            return $this->render($profileTemplate);
        }

        //Sessionに保存してパスワード入力画面に遷移
        if ($step === 1) {
            //プロフィール入力画面の場合
            //validation
            if ($this->User->validates($this->request->data)) {
                if (!$this->checkAge(16, $this->request->data['User']['birth_day'], $this->request->data['User']['local_date']))
                {
                    $this->set('birthday_class', 'has-error');
                    return $this->render($profileTemplate);
                }

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
                $this->Notification->outError(__('Failed to save data.'));
                return $this->render($profileTemplate);
            }
        }
        //パスワード入力画面の場合

        //session存在チェック
        if (!$this->Session->read('data')) {
            $this->Notification->outError(__('Invalid access'));
            return $this->redirect('/');
        }

        //sessionデータとpostのデータとマージ
        $data = Hash::merge($this->Session->read('data'), $this->request->data);

        // TODO: After payment implementation, user must be created any case in invitation.
        //       So after merged auto creating use when invitation, this `if` statement should be deleted.
        $user = $this->User->getUserByEmail($invite['Invite']['email']);
        if ($user) {
            $userId = $user['User']['id'];

            // Disabled user email validation
            // Because in batch case, email is already registered
            $email = $this->User->Email->getNotVerifiedEmail($userId);
            $emailFromEmailTable = Hash::get($email, 'Email.email');
            $emailFromInviteTable = $invite['Invite']['email'];
            if ($emailFromEmailTable === $emailFromInviteTable) {
                unset($this->User->Email->validate['email']);
            }
            // Set user info to register data
            $data['User']['id'] = $userId;
            $data['User']['no_pass_flg'] = false;
            $data['Email'][0]['Email']['id'] = $email['Email']['id'];
        }
        //email
        $data['Email'][0]['Email']['email'] = $invite['Invite']['email'];
        //タイムゾーンをセット
        if (isset($data['User']['local_date'])) {
            //ユーザのローカル環境から取得したタイムゾーンをセット
            $data['User']['timezone'] = AppUtil::getClientTimezone($data['User']['local_date']);
            //自動タイムゾーン設定フラグをoff
            $data['User']['auto_timezone_flg'] = false;
        }
        //言語を保存
        $data['User']['language'] = $this->Lang->getLanguage();
        //デフォルトチームを設定
        $data['User']['default_team_id'] = $team['Team']['id'];

        // ユーザ本登録
        if (!$this->User->userRegistration($data)) {
            return $this->render($passwordTemplate);
        }
        //ログイン
        $userId = $this->User->getLastInsertID() ? $this->User->getLastInsertID() : $userId;

        try {
            // If _autoLogin is failed, _joinTeam() will be failed after this process.
            $this->_autoLogin($userId, true);
        } catch (\Throwable $e) {
            GoalousLog::critical('Failed auto login when after user register.', [
                'users.id' => $userId,
                'teams.id' => $team['Team']['id'],
            ]);
            throw $e;
        }
        // flash削除
        // _authLogin()の処理中に例外メッセージが吐かれるため、
        // 一旦ここで例外メッセージを表示させないためにFlashメッセージをremoveする
        $this->Session->delete('Message.noty');

        //チーム参加
        $invitedTeam = $this->_joinTeam($this->request->params['named']['invite_token']);
        if ($invitedTeam === false) {
            $this->Auth->logout();
            $this->Notification->outError(__("Failed to register user. Please try again later."));
            return $this->redirect("/");
        }



        // Message of team joining
        $this->Notification->outSuccess(__("Joined %s.", $team['Team']['name']));

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
     * @return void
     * @throws RuntimeException
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
                $this->Notification->outSuccess(__('Succeeded to register!'));
                //新規ユーザ登録時のフロー
                $this->Session->write('add_new_mode', MODE_NEW_PROFILE);
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                //新規プロフィール入力画面へ
                return $this->redirect(['action' => 'add_profile']);
            } else {
                //ログインされていれば、メール追加
                $this->Notification->outSuccess(__('Authenticated your email address.'));
                /** @noinspection PhpInconsistentReturnPointsInspection */
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect('/');
            }
        } catch (RuntimeException $e) {
            //例外の場合は、トークン再送信画面へ
            $this->Notification->outError($e->getMessage());
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
            $this->Notification->outError($e->getMessage() . "\n" . __("Please cancel changing email address and try again."));
            //トークン再送ページへ
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect(['action' => 'settings']);
        }
        $this->User->commit();
        $this->_autoLogin($this->Auth->user('id'));
        $this->Notification->outSuccess(__("Email address is changed."));
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
            $this->Auth->logout();
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
                $this->Notification->outSuccess(__("Password reset email has been sent. Please check your email."),
                    ['title' => __("Email sent.")]);
            }
            return $this->render('password_reset_request');
        }

        // Token existing case
        $user_email = $this->User->checkPasswordToken($token);

        if (!$user_email) {
            $this->Notification->outError(__("Password code incorrect. The validity period may have expired. Please resend email again."),
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
            $this->Notification->outSuccess(__("Please login with your new password."),
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
                $this->Notification->outSuccess(__("Confirmation has been sent to your email address."),
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
            // Restrict saving data if demo env
            if (IS_DEMO) {
                // Prevent to save data which is not allowed
                $tmp = $this->request->data;
                $this->request->data = [];
                foreach (self::ALLOW_DEMO_SAVING_USER_SETTINGS as $keyPath) {
                    $this->request->data[$keyPath] = Hash::get($tmp, $keyPath);
                }
                // Convert flatten array to multiple dimensions array.
                // https://book.cakephp.org/2.0/ja/core-utility-libraries/hash.html#Hash::expand
                $this->request->data = Hash::expand($this->request->data);
            }

            //キャッシュ削除
            Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_NOTIFY_SETTING, true, null, false), 'user_data');
            Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');

            // Specify update user
            $this->request->data['User']['id'] = $me['User']['id'];

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
            if (isset($this->request->data['NotifySetting']['email_status']) &&
                isset($this->request->data['NotifySetting']['mobile_status'])
            ) {
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('app', 'all'));
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('email',
                            $this->request->data['NotifySetting']['email_status']));
                $this->request->data['NotifySetting'] =
                    array_merge($this->request->data['NotifySetting'],
                        $this->User->NotifySetting->getSettingValues('mobile',
                            $this->request->data['NotifySetting']['mobile_status']));
            }

            if (isset($this->request->data['TeamMember'][0]['default_translation_language'])) {
                /** @var TeamMember $TeamMember */
                $TeamMember = ClassRegistry::init('TeamMember');
                $this->request->data['TeamMember'][0]['id'] = $TeamMember->getIdByTeamAndUserId($this->current_team_id, $this->request->data['User']['id']);
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
                $this->_updateSetupStatusIfNotCompleted();

                // update message search keywords by user id
                ClassRegistry::init('TopicSearchKeyword')->updateByUserId($this->Auth->user('id'));

                $this->Notification->outSuccess(__("Saved user setting."));
                $this->redirect('/users/settings');
            } else {
                $this->Notification->outError(__("Failed to save user setting."));
            }
            $me = $this->_getMyUserDataForSetting();
            // For updating header user info
            $this->set('my_prof', $this->User->getMyProf());
        }

        $this->request->data = $me;

        $this->layout = LAYOUT_TWO_COLUMN;
        //姓名の並び順をセット
        $lastFirst = in_array($me['User']['language'], $this->User->langCodeOfLastFirst);
        //言語選択
        $language_list = $this->Lang->getAvailLangList();
        //タイムゾーン
        $timezones = AppUtil::getTimezoneList();
        //ローカル名を利用している国かどうか？
        $is_not_use_local_name = $this->User->isNotUseLocalName($me['User']['language']);
        $not_verified_email = $this->User->Email->getNotVerifiedEmail($this->Auth->user('id'));
        $language_name = $this->Lang->availableLanguages[$me['User']['language']];

        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $team_can_translate = $TeamTranslationLanguage->hasLanguage($this->current_team_id);
        if ($team_can_translate) {
            /** @var TeamMemberService $TeamMemberService */
            $TeamMemberService = ClassRegistry::init('TeamMemberService');
            /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
            $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

            $translation_languages = $TeamTranslationLanguageService->getAllLanguages($this->current_team_id);

            try {
                $default_translation_language = $TeamMemberService->getDefaultTranslationLanguageCode($this->current_team_id, $this->Auth->user('id'), CakeRequest::acceptLanguage());
            } catch (Exception $exception) {
                GoalousLog::error("Failed to get default translation language in user setting page.", [
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                    'team_id' => $this->current_team_id,
                    'user_id' => $this->Auth->user('id')
                ]);
                $default_translation_language = "";
            }
        }

        $this->set(compact('me', 'is_not_use_local_name', 'lastFirst', 'language_list', 'timezones',
            'not_verified_email', 'local_name', 'language_name', 'team_can_translate', 'default_translation_language', 'translation_languages'));
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
            $this->Notification->outError($e->getMessage(), ['title' => __("Failed to save password change.")]);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Notification->outSuccess(__("Changed password."));

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
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }

        $this->Notification->outInfo(__("Confirmation has been sent to your email address."));
        $this->GlEmail->sendMailChangeEmailVerify($this->Auth->user('id'), $email_data['Email']['email'],
            $email_data['Email']['email_token']);

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * 招待に応じる
     * - 登録済みユーザの場合は、チーム参加でホームへリダイレクト
     * - 未登録ユーザの場合は、個人情報入力ページ(register_with_invite)へ
     * - この中で呼ばれる_joinTeam()メソッド内でトランザクションを張っている
     *
     * @param $token
     *
     * @return \Cake\Network\Response|null
     */
    public function accept_invite($token)
    {
        // トークンが有効かどうかチェック
        $confirmRes = $this->Invite->confirmToken($token);
        if ($confirmRes !== true) {
            $this->Notification->outError($confirmRes);
            return $this->redirect("/");
        }

        // 未ログイン
        if (!$this->Auth->User()) {
            // メール招待かつ未登録ユーザーの場合
            $invitation = Hash::get($this->Invite->getByToken($token), 'Invite');
            if ($this->User->isPreRegisteredByInvitationToken($invitation['email'])) {
                $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_EMAIL);
                return $this->redirect(['action' => 'register_with_invite', 'invite_token' => $token]);
            }

            //Save invited team ID
            $this->Session->write('invited_team_id', $invitation['team_id']);

            // 登録済みユーザーかつ未ログインの場合はログイン画面へ
            $this->Notification->outInfo(__("Please login and join the team"));
            $this->Auth->redirectUrl(['action' => 'accept_invite', $token]);
            $this->Session->write('referer_status', REFERER_STATUS_INVITED_USER_EXIST);
            return $this->redirect(['action' => 'login']);
        }

        $userId = $this->Auth->user('id');
        // トークンが自分用に生成されたもうのかどうかチェック
        if (!$this->Invite->isForMe($token, $userId)) {
            $this->Notification->outError(__("This invitation isn't not for you."));
            return $this->redirect("/");
        }

        // ユーザーがログイン中でかつチームジョインが失敗した場合、
        // ログインしていたチームのセッションに戻す必要があるためここでチームIDを退避させる
        $loggedInTeamId = $this->Auth->user('current_team_id');
        $invitedTeam = $this->_joinTeam($token);
        if ($invitedTeam === false) {
            if ($loggedInTeamId) {
                $this->_switchTeam($loggedInTeamId);
            }
            $this->Notification->outError(__("Failed to join team. Please try again later."));
            return $this->redirect("/");
        }

        $this->Session->delete('referer_status');
        $this->Notification->outSuccess(__("Joined %s.", $invitedTeam['Team']['name']));
        return $this->redirect("/");
    }

    public function accept_invite_temporary($token)
    {
        // トークンが有効かどうかチェック
        $confirmRes = $this->Invite->confirmToken($token);
        if ($confirmRes !== true) {
            GoalousLog::error('Cant confirm token', [
                'token' => $token,
                'message' => $confirmRes
            ]);

            return false;
        }

        $userId = $this->Auth->user('id');
        // トークンが自分用に生成されたもうのかどうかチェック
        if (!$this->Invite->isForMe($token, $userId)) {
            GoalousLog::error('This invitation isnt not for you', [
                'token' => $token,
                'user_id' => $userId
            ]);

            return false;
        }

        // ユーザーがログイン中でかつチームジョインが失敗した場合、
        // ログインしていたチームのセッションに戻す必要があるためここでチームIDを退避させる
        $loggedInTeamId = $this->Auth->user('current_team_id');
        $invitedTeam = $this->_joinTeam($token);
        if ($invitedTeam === false) {
            if ($loggedInTeamId) {
                $this->_switchTeam($loggedInTeamId);
            }
            GoalousLog::error('Failed to join team. Please try again later.', [
                'token' => $token,
                'loggedInTeamId' => $loggedInTeamId,
                'invitedTeam' => $invitedTeam,
            ]);
            return false;
        }

        $this->Session->delete('referer_status');
        GoalousLog::info('joined team', [
            'token' => $token,
            'loggedInTeamId' => $loggedInTeamId,
            'invitedTeam' => $invitedTeam,
        ]);
        return true;
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
            $with_group = boolval($query['with_group'] ?? false);
            $with_self = boolval($query['with_self'] ?? false);
            $excludedUsers = array_values($query['excluded_users'] ?? []);
            $res = $this->User->getUsersSelect2($query['term'], $query['page_limit'], $with_group, $with_self,
                $excludedUsers);
        }
        if (isset($query['in_post_id']) && !empty($query['in_post_id'])) {
            $res['results'] = $this->Mention::filterAsMentionableUser($query['in_post_id'], $res['results']);
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
     * search users for adding users in message select2
     */
    function ajax_select_add_members_on_message()
    {
        $this->_ajaxPreProcess();

        $query = $this->request->query;
        $res = ['results' => []];
        $existparameters = !empty($query['topic_id']) && !empty($query['term']) && !empty($query['page_limit']);
        if ($existparameters) {
            /** @var UserService $UserService */
            $UserService = ClassRegistry::init('UserService');
            $res['results'] = $UserService->findUsersForAddingOnTopic($query['term'], $query['page_limit'],
                $query['topic_id'], true);
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

        $url_2fa = $this->TwoFa->getQRCodeInline(SERVICE_NAME,
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
            $this->Notification->outError($e->getMessage());
            return $this->redirect('/users/settings');
        }
        $this->Session->delete('2fa_secret_key');
        $this->Mixpanel->track2SV(MixpanelComponent::TRACK_2SV_ENABLE);
        $this->Notification->outSuccess(__("Succeeded to save 2-Step Verification."));
        $this->Flash->set(null,
            ['element' => 'flash_click_event', 'params' => ['id' => 'ShowRecoveryCodeButton'], 'key' => 'click_event']);
        return $this->redirect('/users/settings');
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
        $this->Notification->outSuccess(__("Succeeded to cancel 2-Step Verification."));
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
     * - 一連の処理にトランザクションを使用。
     *
     * @param $token
     */
    function _joinTeam($token)
    {
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');
        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        try {
            $this->User->begin();

            //トークン認証
            $confirmRes = $this->Invite->confirmToken($token);
            if ($confirmRes !== true) {
                throw new Exception(sprintf("Failed to confirm token. token:%s errorMsg: %s"
                    , var_export($token, true), $confirmRes));
            }

            $userId = $this->Auth->user('id');
            $invite = $this->Invite->verify($token, $userId);

            $inviteTeamId = Hash::get($invite, 'Invite.team_id');
            $isCharge = $PaymentService->calcChargeUserCount($inviteTeamId, 1) === 1;

            //チーム参加
            if (!$this->User->TeamMember->add($userId, $inviteTeamId)) {
                $validationErrors = $ExperimentService->validationExtract($this->User->TeamMember->validationErrors);
                throw new Exception(sprintf("Failed to confirm token. userId:%s teamId:%s validationErrors:%s"
                    , $userId, $inviteTeamId, var_export($validationErrors, true)));
            }

            //セッション更新
            $this->_refreshAuth();

            //チーム切換え
            $this->_switchTeam($inviteTeamId);

            // Circle と CircleMember の current_team_id を一時的に変更
            $currentTeamId = $this->Circle->current_team_id;
            $this->Circle->current_team_id = $inviteTeamId;
            $this->Circle->CircleMember->current_team_id = $inviteTeamId;

            Cache::delete($this->Circle->CircleMember->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true), 'team_info');

            $teamAllCircle = $this->Circle->getTeamAllCircle();

            // 「チーム全体」サークルに追加
            App::import('Service', 'CircleService');
            $circleId = $teamAllCircle['Circle']['id'];
            if (!$CircleService->join($circleId, $userId)) {
                $validationErrors = $ExperimentService->validationExtract($this->Circle->CircleMember->validationErrors);
                throw new Exception(sprintf("Failed to join all team circle. userId:%s circleId:%s validationErrors:%s"
                    , $userId, $circleId, var_export($validationErrors, true)));
            }

            $this->Circle->current_team_id = $currentTeamId;
            $this->Circle->CircleMember->current_team_id = $currentTeamId;


            /* get payment flag */
            $teamId = $inviteTeamId;
            $paymentTiming = new PaymentTiming();
            if ($paymentTiming->checkIfPaymentTiming($teamId)){
                /* Charge if paid plan */
                /** @var Team $Team */
                $Team = ClassRegistry::init("Team");
                /** @var CampaignService $CampaignService */
                $CampaignService = ClassRegistry::init('CampaignService');
                if ($Team->isPaidPlan($teamId) && !$CampaignService->purchased($teamId) && $isCharge) {
                    // [Important] Transaction commit in this method
                    $PaymentService->charge(
                        $teamId,
                        Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE(),
                        1,
                        Hash::get($invite, 'Invite.from_user_id')
                    );
                }
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $this->User->rollback();
            return false;
        }

        $this->User->commit();

        //cache削除
        Cache::delete($this->Circle->CircleMember->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false), 'team_info');

        //招待者に通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM, $invite['Invite']['id']);

        $invitedTeam = $this->User->TeamMember->Team->findById($inviteTeamId);
        return $invitedTeam;
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

        $namedParams = $this->request->params['named'];

        $userId = Hash::get($namedParams, "user_id");
        if (!$userId || !$this->_setUserPageHeaderInfo($userId)) {
            // ユーザーが存在しない
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $this->layout = LAYOUT_ONE_COLUMN;
        $pageType = Hash::get($namedParams, 'page_type');

        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        $termFilterOptions = $TermService->getFilterMenu();

        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $team = $Team->getCurrentTeam();
        $termId = Hash::get($namedParams, 'term_id') ?? $Term->getCurrentTermId();
        // if all term is selected, start date will be team created, end date will be end date of next term
        if ($termId == $TermService::TERM_FILTER_ALL_KEY_NAME) {
            $startDate = AppUtil::dateYesterday(
                AppUtil::dateYmdLocal($team['Team']['created'],
                    $team['Team']['timezone'])
            );
            $nextTerm = $Term->getNextTermData();
            $endDate = $nextTerm['end_date'];

            $isAfterCurrentTerm = false;
        } else {
            $targetTerm = $this->Team->Term->findById($termId);
            $startDate = $targetTerm['Term']['start_date'];
            $endDate = $targetTerm['Term']['end_date'];

            $isAfterCurrentTerm = $TermService->isAfterCurrentTerm($termId);
        }

        $myGoalsCount = $this->Goal->getMyGoals(null, 1, 'count', $userId, $startDate, $endDate);
        $collaboGoalsCount = $this->Goal->getMyCollaboGoals(null, 1, 'count', $userId, $startDate, $endDate);
        $myGoalsCount += $collaboGoalsCount;
        $followGoalsCount = $this->Goal->getMyFollowedGoals(null, 1, 'count', $userId, $startDate, $endDate);

        if ($pageType == "following") {
            $goals = $this->Goal->getMyFollowedGoals(null, 1, 'all', $userId, $startDate, $endDate);
        } else {
            $goals = $this->Goal->getGoalsWithAction($userId, MY_PAGE_ACTION_NUMBER, $startDate, $endDate);
        }
        $goals = $GoalService->processGoals($goals);
        $goals = $GoalService->extendTermType($goals, $this->Auth->user('id'));
        $isMine = $userId == $this->Auth->user('id') ? true : false;
        $displayActionCount = MY_PAGE_ACTION_NUMBER;
        if ($isMine) {
            $displayActionCount--;
        }

        $termBaseUrl = Router::url([
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $userId,
            'page_type'  => $pageType
        ]);

        $myCoachingUsers = $this->User->TeamMember->getMyMembersList($this->my_uid);

        // 完了アクションが可能なゴールIDリスト
        $canCompleteGoalIds = Hash::extract(
            $this->Goal->findCanComplete($this->my_uid), '{n}.id'
        );

        $this->set([
            'term'                 => $termFilterOptions,
            'term_id'              => $termId,
            'term_base_url'        => $termBaseUrl,
            'my_goals_count'       => $myGoalsCount,
            'follow_goals_count'   => $followGoalsCount,
            'page_type'            => $pageType,
            'goals'                => $goals,
            'is_mine'              => $isMine,
            'display_action_count' => $displayActionCount,
            'my_coaching_users'    => $myCoachingUsers,
            'canCompleteGoalIds'   => $canCompleteGoalIds,
            'isAfterCurrentTerm'   => $isAfterCurrentTerm,
        ]);
        $this->addHeaderBrowserBackCacheClear();
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
            $this->Notification->outError(__("Invalid screen transition."));
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

        $this->addHeaderBrowserBackCacheClear();
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function view_actions()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');

        $currentTermId = $Term->getCurrentTermId();
        // make variables for requested named params.
        $namedParams = $this->request->params['named'];
        $userId = Hash::get($namedParams, "user_id");
        $pageType = Hash::get($namedParams, "page_type");
        $goalId = Hash::get($namedParams, 'goal_id');
        $termId = Hash::get($namedParams, 'term_id') ?? $currentTermId;

        // validation
        if (!$this->_validateParamsOnActionPage($userId, $pageType, $termId, $goalId)) {
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }

        $this->_setUserPageHeaderInfo($userId);

        $myUid = $this->Auth->user('id');

        if ($userId != $myUid) {
            $canAction = false;
        } else {
            if ($goalId) {
                $canAction = $Goal->isActionable($userId, $goalId);
            } else {
                $canAction = $this->_canActionOnActionPageInTerm($userId, $termId);
            }
        }

        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        // count action
        if ($termId == $TermService::TERM_FILTER_ALL_KEY_NAME) {
            // if term = all, then user_is is key
            $actionCount = $ActionResult->getCountByUserId($userId);
        } else {
            $goalIdsInTerm = $GoalService->findIdsByTermIdUserId($termId, $userId);
            $actionCount = $ActionResult->getCountByGoalId($goalIdsInTerm);
        }
        $termFilterOptions = $TermService->getFilterMenu(true, false);
        $goalFilterOptions = $this->_getGoalFilterMenuOnActionPage($userId, $termId);

        $postCondition = $this->_getTimestampsForPostCondition($termId, $userId);
        $startTimestamp = $postCondition['startTimestamp'];
        $endTimestamp = $postCondition['endTimestamp'];
        $oldestTimestamp = $postCondition['oldestTimestamp'];

        $posts = $this->_findPostsOnActionPage($pageType, $userId, $goalId, $startTimestamp, $endTimestamp);

        $this->set('long_text', false);
        $this->set(compact(
            'posts',
            'termId',
            'goalFilterOptions',
            'termFilterOptions',
            'endTimestamp',
            'oldestTimestamp',
            'actionCount',
            'currentTermId',
            'canAction'
        ));
        $this->addHeaderBrowserBackCacheClear();
        return $this->render();
    }

    /**
     * @param int        $userId
     * @param int|string $termId
     *
     * @return bool
     */
    function _canActionOnActionPageInTerm(int $userId, $termId): bool
    {
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        if ($userId == $this->Auth->user('id')
            && ($termId == $Term->getCurrentTermId() || $termId == $TermService::TERM_FILTER_ALL_KEY_NAME)
            && !empty($GoalService->findActionables())
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param int      $userId
     * @param int|null $termId
     *
     * @return array
     */
    function _getGoalFilterMenuOnActionPage(int $userId, $termId): array
    {
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        if ($termId == $TermService::TERM_FILTER_ALL_KEY_NAME) {
            $goalFilterOptions = $GoalService->getFilterMenu($userId, null);
        } else {
            $goalFilterOptions = $GoalService->getFilterMenu($userId, $termId);
        }
        return $goalFilterOptions;
    }

    /**
     * @param $userId
     * @param $pageType
     * @param $termId
     * @param $goalId
     *
     * @return bool
     */
    function _validateParamsOnActionPage($userId, $pageType, $termId, $goalId)
    {
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        if (!in_array($pageType, ['list', 'image'])) {
            // $pageType is wrong
            return false;
        }
        if ($termId != $TermService::TERM_FILTER_ALL_KEY_NAME && $Term->exists($termId) == false) {
            // $termId is wrong
            return false;
        }
        if ($goalId && $GoalMember->isCollaborated($goalId, $userId) == false) {
            // $goalId is not collaborated
            return false;
        }
        return true;
    }

    /**
     * @param $termId
     * @param $userId
     *
     * @return array ['startTimestamp'=>"",'endTimestamp'=>"",'oldestTimestamp'=>""]
     */
    function _getTimestampsForPostCondition($termId, $userId): array
    {
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init('TermService');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        if ($termId == $TermService::TERM_FILTER_ALL_KEY_NAME) {
            // if all term, start is date of team created
            $endTimestamp = REQUEST_TIMESTAMP;
            $startTimestamp = $endTimestamp - MONTH;
            $targetUser = $this->User->getDetail($userId);
            $oldestTimestamp = $targetUser['User']['created'];
        } else {
            $term = $Term->findById($termId)['Term'];
            $timezone = $Team->getTimezone();
            if ($termId == $Term->getCurrentTermId()) {
                $endTimestamp = REQUEST_TIMESTAMP;
            } else {
                $endTimestamp = AppUtil::getTimestampByTimezone(AppUtil::dateTomorrow($term['end_date']), $timezone);
            }
            $startTimestamp = $endTimestamp - MONTH;
            $oldestTimestamp = AppUtil::getTimestampByTimezone($term['start_date'], $timezone);
        }
        // $startTimestamp should be ahead of $oldestTimestamp
        if ($startTimestamp < $oldestTimestamp) {
            $startTimestamp = $oldestTimestamp;
        }

        $res = compact('startTimestamp', 'endTimestamp', 'oldestTimestamp');
        return $res;
    }

    function _findPostsOnActionPage($pageType, $userId, $goalId, $startTimestamp, $endTimestamp): array
    {
        $limit = ($pageType == 'list') ? POST_FEED_PAGE_ITEMS_NUMBER : MY_PAGE_CUBE_ACTION_IMG_NUMBER;
        $params = [
            'author_id' => $userId,
            'type'      => Post::TYPE_ACTION,
        ];
        if ($goalId) {
            $params['goal_id'] = $goalId;
        }
        $posts = $this->Post->get(1, $limit, $startTimestamp, $endTimestamp, $params);
        return $posts;
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
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }

        $this->addHeaderBrowserBackCacheClear();
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
            $this->Notification->outError($SubscribeEmail->validationErrors['email'][0]);
            return $this->redirect($this->referer());
        }
        $this->Notification->outSuccess(__('Registered email address.'));
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

        $timezone = $this->Team->getTimezone();
        // 評価期間内の投稿数
        $termStartTimestamp = AppUtil::getStartTimestampByTimezone($this->Team->Term->getCurrentTermData()['start_date'],
            $timezone);
        $termEndTimestamp = AppUtil::getEndTimestampByTimezone($this->Team->Term->getCurrentTermData()['end_date'],
            $timezone);

        $post_count = $this->Post->getCount($user_id, $termStartTimestamp, $termEndTimestamp);
        $this->set('post_count', $post_count);

        // 評価期間内のアクション数
        $action_count = $this->Goal->ActionResult->getCount($user_id, $termStartTimestamp, $termEndTimestamp);
        $this->set('action_count', $action_count);

        // 投稿に対するいいねの数
        $post_like_count = $this->Post->getLikeCountSumByUserId($user_id, $termStartTimestamp, $termEndTimestamp);
        // コメントに対するいいねの数
        $comment_like_count = $this->Post->Comment->getLikeCountSumByUserId($user_id, $termStartTimestamp,
            $termEndTimestamp);
        $this->set('like_count', $post_like_count + $comment_like_count);

        return true;
    }

    /**
     * User invitation form
     *
     * @param null $page
     *
     * @return void
     */
    public function invite($step = null)
    {
        // Deny direct access for confirm page
        if (!empty($step)) {
            return $this->redirect('/users/invite');
        }

        $this->layout = LAYOUT_ONE_COLUMN;
    }

    /**
     * Browser back cache clear
     * @see https://jira.goalous.com/browse/GL-8610
     */
    private function addHeaderBrowserBackCacheClear(): void
    {
        // For HTTP/1.1 conforming clients and the rest (MSIE 5)
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        // For HTTP/1.0 conforming clients
        header('Pragma: no-cache');
    }

    /**
     * check Age
     *
     */
    private function checkAge(int $age, array $birthday, string $localDate): bool
    {
        $year = $birthday['year'];
        $month = $birthday['month'];
        $day = $birthday['day'];
        if (empty($year) || empty($month) || empty($day)){
            return true;
        }
        /*
        if (GoalousDateTime::createFromDate($year, $month, $day)->age < 16)
        {
            return false;
        }
        */
        // use local_date to calculate the birthday
        $birthDate = GoalousDateTime::createFromFormat("Ymd", $year.$month.$day)->startOfDay();
        $userLocalDate = GoalousDateTime::parse($localDate)->startOfDay();
        $age = $userLocalDate->diffInYears($birthDate);
        if ($age < 16) {
            return false;
        }
        return true;

    }
}
