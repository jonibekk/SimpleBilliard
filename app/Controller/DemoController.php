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

class DemoController extends AppController
{
    public $uses = [
        'User',
        'Invite',
        'Circle',
        'TeamMember'
    ];
    public $components = [
        'TwoFa',
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('login');

        $this->_checkAdmin(['invite']);

        // GL-7364
        // TODO: remove these processing. but there is a problem that SecurityComponent.blackhole error occurs after update core.php `session.cookie_domain` to enable to share cookie across sub domains.
        if ($this->request->params['action'] == 'login') {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
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
        if (!IS_DEMO) {
            return $this->redirect('/users/login');
        }

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
}
