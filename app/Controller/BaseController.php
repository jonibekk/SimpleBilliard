<?php
App::uses('Controller', 'Controller');
App::uses('UserAgent', 'Request');
App::uses('TeamStatus', 'Lib/Status');
App::uses('UserAgent', 'Request');
App::import('Service', 'TeamService');

use Goalous\Model\Enum as Enum;

/**
 * Application level Controller
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:00
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @property SessionComponent      $Session
 * @property SecurityComponent     $Security
 * @property AuthComponent         $Auth
 * @property NotifyBizComponent    $NotifyBiz
 * @property NotificationComponent $Notification
 * @property GlEmailComponent      $GlEmail
 * @property MixpanelComponent     $Mixpanel
 * @property LangComponent         $Lang
 * @property User                  $User
 * @property Post                  $Post
 * @property Goal                  $Goal
 * @property Team                  $Team
 * @property GlRedis               $GlRedis
 */
class BaseController extends Controller
{
    public $components = [
        'Session',
        'Security' => [
            'csrfUseOnce' => false,
            'csrfExpires' => '+24 hour'
        ],
        'Auth'     => [
            'flash' => [
                'element' => 'alert',
                'key'     => 'auth',
                'params'  => ['plugin' => 'BoostCake', 'class' => 'alert-error']
            ]
        ],
        'Notification',
        'NotifyBiz',
        'GlEmail',
        'Mixpanel',
        'Lang',
    ];

    public $uses = [
        'User',
        'Post',
        'Goal',
        'Team',
        'GlRedis',
    ];

    public $my_uid = null;
    public $current_team_id = null;
    public $current_term_id = null;
    public $next_term_id = null;

    /**
     * スマホからのリクエストか？
     * is request from mobile browser?
     *
     * @var bool
     */
    public $is_mb_browser = false;
    /**
     * スマホアプリからのリクエストか？
     * is request from mobile app?
     *
     * @var bool
     */
    public $is_mb_app = false;
    /**
     * iOSスマホアプリからのリクエストか？
     * is request from mobile app?
     *
     * @var bool
     */
    public $is_mb_app_ios = false;
    // TODO: Remove reference after NCMB apps been discontinued
    // temporary fix to support NCMB and Firebase apps
    public $is_mb_app_ios_high_header = false;
    /**
     * Request from tablet?
     */
    public $is_tablet = false;
    /**
     * スマホアプリのUA定義
     * defined user agents of mobile application
     *
     * @var array
     */
    private $mobile_app_uas = [
        'Goalous App iOS',
        'Goalous App Android'
    ];
    /**
     * use it when you need stop after beforender
     */
    public $stopInvoke = false;

    /**
     * This list for excluding from prohibited request
     * It's like a cake request params.
     * If only controller name is specified, including all actions
     * If you would like to specify several action, refer to the following:
     * [
     * 'controller' => 'users', 'action'     => 'settings',
     * ],
     * [
     * 'controller' => 'users', 'action'     => 'view_goals',
     * ],
     *
     * @var array
     */
    private $ignoreProhibitedRequest = [
        [
            'controller' => 'payments',
        ],
        [
            'controller' => 'teams',
        ],
        [
            'controller' => 'users',
            'action'     => 'logout',
        ],
        [
            'controller' => 'users',
            'action'     => 'accept_invite',
        ],
        [
            'controller' => 'users',
            'action'     => 'settings',
        ],
        [
            'controller' => 'terms',
        ],
        // TODO: We have to fix it. now, privacy_policy and terms are redirected to home. but they should be appear and important page!
        [
            'controller' => 'pages',
            'action'     => 'display',
            'pagename'   => 'privacy_policy',
        ],
        [
            'controller' => 'pages',
            'action'     => 'display',
            'pagename'   => 'terms',
        ],
    ];

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        $this->_mergeParent = 'BaseController';
    }

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->_setSecurity();

        if ($this->Auth->user()) {
            $this->current_team_id = $this->Session->read('current_team_id');
            $this->my_uid = $this->Auth->user('id');

            //Check from DB whether user is deleted
            $condition = [
                'conditions' => [
                    'User.id'      => $this->my_uid,
                    'User.del_flg' => false
                ],
                'fields'     => [
                    'User.id'
                ]
            ];
            //If user is deleted, delete session & user cache, and redirect to login page
            if (empty($this->User->find('first', $condition))) {
                $this->User->deleteCache($this->my_uid);
                GoalousLog::notice("User $this->my_uid is deleted. Redirecting");
                $this->Notification->outNotice(__("This user does not exist."));
                $this->redirect($this->Auth->logout());
            }

            // TODO: Delete these lines after we fixed processing to update `default_team_id` when activate user
            // Detect inconsistent data that current team id is empty
            if (empty($this->current_team_id)) {
                GoalousLog::emergency("Current team id is empty", ['user_id' => $this->my_uid]);
                $this->redirect($this->Auth->logout());
            }
            $this->_setTeamStatus();
        }
    }

    /**
     * initializing current users TeamStatus from $this->current_team_id
     * after this method, call TeamStatus::getCurrentTeam() to get current teams settings.
     */
    private function _setTeamStatus()
    {
        $teamId = $this->current_team_id;

        $teamStatus = TeamStatus::getCurrentTeam();

        if (AppUtil::isInt($this->current_team_id)) {
            $teamStatus->initializeByTeamId($teamId);
        }
    }

    public function _setSecurity()
    {
        // sslの判定をHTTP_X_FORWARDED_PROTOに変更
        $this->request->addDetector('ssl', ['env' => 'HTTP_X_FORWARDED_PROTO', 'value' => 'https']);
        //サーバー環境のみSSLを強制
        if (ENV_NAME != "local") {
            $this->Security->blackHoleCallback = 'forceSSL';
            $this->Security->requireSecure();
        }
    }

    /**
     * アプリケーション全体の言語設定
     */
    public function _setAppLanguage()
    {
        //言語設定済かつ自動言語フラグが設定されていない場合は、言語設定を適用。それ以外はブラウザ判定
        if ($this->Auth->user() && $this->Auth->user('language') && !$this->Auth->user('auto_language_flg')) {
            Configure::write('Config.language', $this->Auth->user('language'));
            $this
                ->set('is_not_use_local_name', $this->User->isNotUseLocalName($this->Auth->user('language')));
        } else {
            $lang = $this->Lang->getLanguage();
            $this->set('is_not_use_local_name', $this->User->isNotUseLocalName($lang));
        }
    }

    function _updateSetupStatusIfNotCompleted()
    {
        $setup_guide_is_completed = $this->Auth->user('setup_complete_flg');
        if ($setup_guide_is_completed) {
            return true;
        }

        $user_id = $this->Auth->user('id');
        $this->GlRedis->deleteSetupGuideStatus($user_id);
        $status_from_mysql = $this->User->generateSetupGuideStatusDict($user_id);
        if ($this->_calcSetupRestCount($status_from_mysql) === 0) {
            $this->User->completeSetupGuide($user_id);
            $this->_refreshAuth($this->Auth->user('id'));
            return true;
        }
        //set update time
        $status_from_mysql[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME] = time();

        $this->GlRedis->saveSetupGuideStatus($user_id, $status_from_mysql);
        return true;
    }

    function _calcSetupRestCount($status)
    {
        return count(User::$TYPE_SETUP_GUIDE) - count(array_filter($status));
    }

    function _calcSetupCompletePercent($status)
    {
        $rest_count = $this->_calcSetupRestCount($status);
        if ($rest_count <= 0) {
            return 100;
        }

        $complete_count = count(User::$TYPE_SETUP_GUIDE) - $rest_count;
        if ($complete_count === 0) {
            return 0;
        }

        return 100 - floor(($rest_count / count(User::$TYPE_SETUP_GUIDE) * 100));
    }

    /**
     * ログイン中のAuthを更新する（ユーザ情報を更新後などに実行する）
     *
     * @param $uid
     *
     * @return bool
     */
    public function _refreshAuth($uid = null)
    {
        if (!$uid) {
            $uid = $this->Auth->user('id');
        }
        //言語設定を退避
        $user_lang = $this->User->findById($uid);
        $lang = null;
        if (!empty($user_lang)) {
            $lang = $user_lang['User']['language'];
        }
        $this->Auth->logout();
        $this->User->resetLocalNames();
        $this->User->me['language'] = $lang;
        $this->User->recursive = 0;
        $user_buff = $this->User->findById($uid);
        $this->User->recursive = -1;
        unset($user_buff['User']['password']);
        $user_buff = array_merge(['User' => []], $user_buff);
        //配列を整形（Userの中に他の関連データを配置）
        $user = [];
        $associations = [];
        foreach ($user_buff as $key => $val) {
            if ($key == 'User') {
                $user[$key] = $val;
            } else {
                $associations[$key] = $val;
            }
        }
        if (isset($user['User'])) {
            $user['User'] = array_merge($user['User'], $associations);
        }
        $this->User->me = $user['User'];
        $res = $this->Auth->login($user['User']);
        return $res;
    }

    /**
     * @param $model_id
     * @param $notify_type
     */
    function _sendNotifyToCoach($model_id, $notify_type)
    {
        $coach_id = $this->Team->TeamMember->getCoachId($this->Auth->user('id'),
            $this->Session->read('current_team_id'));
        if (!$coach_id) {
            return;
        }
        $this->NotifyBiz->execSendNotify($notify_type, $model_id, null, $coach_id);
    }

    /**
     * コーチーに通知
     *
     * @param $goalMemberId
     * @param $notifyType
     */
    function _sendNotifyToCoachee($goalMemberId, $notifyType)
    {
        $goalMember = $this->Goal->GoalMember->findById($goalMemberId);
        if (!Hash::get($goalMember, 'GoalMember')) {
            return;
        }
        $this->NotifyBiz->execSendNotify($notifyType,
            $goalMember['GoalMember']['goal_id'],
            null,
            $goalMember['GoalMember']['user_id']
        );
    }

    /**
     * @param string $class  __CLASS__
     * @param string $method __METHOD__
     */
    function _logOldRequest(string $class, string $method)
    {
        $this->log(sprintf("This is old page. Class: %s::%s referer URL: %s", $class, $method, $this->referer()));
    }

    public function _decideMobileAppRequest()
    {
        $ua = Hash::get($_SERVER, 'HTTP_USER_AGENT');
        if (strpos($ua, 'Goalous App') !== false) {
            $this->is_mb_app = true;
        }
        $this->set('is_mb_app', $this->is_mb_app);
        if (strpos($ua, 'Goalous App iOS') !== false) {
            $this->is_mb_app_ios = true;
        }
        $this->set('is_mb_app_ios', $this->is_mb_app_ios);

        // TODO: Remove  after NCMB apps been discontinued
        $userAgent = UserAgent::detect(Hash::get($_SERVER, 'HTTP_USER_AGENT') ?? '');
        if ($userAgent->isiOSApp() && $userAgent->getMobileAppVersion() < MOBILE_APP_IOS_VERSION_NEW_HEADER) {
            $this->is_mb_app_ios_high_header = true;
        }
        $this->set('is_mb_app_ios_high_header', $this->is_mb_app_ios_high_header);
    }

    /**
     * check prohibited request in read only term
     *
     * @return bool
     */
    public function _isProhibitedRequestByReadOnly(): bool
    {
        if ($this->_isExcludeRequestParamInProhibited()) {
            return false;
        }
        if (!$this->request->is(['post', 'put', 'delete', 'patch'])) {
            return false;
        }

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");

        if ($TeamService->getServiceUseStatus() == Team::SERVICE_USE_STATUS_READ_ONLY) {
            return true;
        }
        return false;
    }

    /**
     * check prohibited request in read only term
     *
     * @return bool
     */
    public function _isProhibitedRequestByCannotUseService(): bool
    {
        if ($this->_isExcludeRequestParamInProhibited()) {
            return false;
        }

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");

        if ($TeamService->isCannotUseService()) {
            return true;
        }
        return false;
    }

    /**
     * Request params are excluded request in prohibited?
     * Decide with $this->excludeRequestParamsInProhibited
     * checking controller and action
     * if only controller checking and hit it, return true
     *
     * @return bool
     */
    private function _isExcludeRequestParamInProhibited(): bool
    {
        // if controller is not much, skip all.
        $ignoreParamExists = array_search($this->request->param('controller'),
            Hash::extract($this->ignoreProhibitedRequest, '{n}.controller')
        );
        if ($ignoreParamExists === false) {
            return false;
        }

        foreach ($this->ignoreProhibitedRequest as $ignoreParam) {
            // filter requested param with $ignoreParam
            $intersectedParams = array_intersect_key($this->request->params, $ignoreParam);
            if ($intersectedParams == $ignoreParam) {
                return true;
            }
        }
        return false;
    }

    /**
     * check is logged in or not
     *
     * @return bool
     */
    public function _isLoggedIn(): bool
    {
        return (bool)$this->Auth->user();
    }

    /*
     * check prohibited request in read only term
     *
     * @param array $actionMethods
     *
     * @return bool
     */
    protected function _isAdmin(array $actionMethods = []): bool
    {
        if (!empty($actionMethods) && !in_array($this->request->action, $actionMethods)) {
            return true;
        }

        // Check if team administrator
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;
        if (empty($userId) || empty($teamId)) {
            return false;
        }
        return $this->Team->TeamMember->isActiveAdmin($userId, $teamId);
    }
}
