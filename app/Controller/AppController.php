<?php
/**
 * Application level Controller
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */
App::uses('BaseController', 'Controller');
App::uses('HelpsController', 'Controller');
App::uses('NotifySetting', 'Model');
App::uses('User', 'Model');
App::uses('CircleMember', 'Model');
App::uses('GoalousDateTime', 'DateTime');
App::uses('MobileAppVersion', 'Request');
App::uses('UserAgent', 'Request');
App::uses('UrlUtil', 'Util');
App::import('Service', 'GoalApprovalService');
App::import('Service', 'GoalService');
App::import('Service', 'TeamService');
App::import('Service', 'ChargeHistoryService');
App::import('Service', 'CreditCardService');
App::import('Service', 'CirclePinService');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsData');
App::import('Lib/Storage/Client', 'NewGoalousAssetsStorageClient');

use Goalous\Enum as Enum;

/**
 * Application Controller
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link           http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @property LangComponent      $Lang
 * @property CsvComponent       $Csv
 * @property CookieComponent    $Cookie
 * @property MixpanelComponent  $Mixpanel
 * @property OgpComponent       $Ogp
 * @property BenchmarkComponent $Benchmark
 * @property PaymentSetting     $PaymentSetting
 */
class AppController extends BaseController
{
    /**
     * アクション件数 キャッシュ有効期限
     */
    const CACHE_KEY_ACTION_COUNT_EXPIRE = 60 * 60 * 24; // 1日

    /**
     * AppControllerを分割した場合、子クラスでComponent,Helper,Modelがマージされないため、
     * 中間Controllerは以下を利用。末端Controllerは通常のCakeの規定通り
     */
    private $merge_components = [
        'DebugKit.Toolbar' => ['panels' => ['UrlCache.UrlCache']],
        'Paginator',
        'Lang',
        'Cookie',
        'Ogp',
        'Csv',
        'Flash',
        // CakePdf required it.
        'RequestHandler'
    ];
    private $merge_helpers = [
        'Session',
        'Html'      => ['className' => 'BoostCake.BoostCakeHtml'],
        'Form'      => ['className' => 'BoostCake.BoostCakeForm'],
        'Paginator' => ['className' => 'BoostCake.BoostCakePaginator'],
        'Upload',
        'TimeEx',
        'TextEx',
        'Csv',
        'Expt',
        'Post',
        'GlHtml',
        'Lang',
        'BackBtn',
        'PageResource'
    ];

    private $merge_uses = [
        'PaymentSetting'
    ];

    /**
     * 基本タイトル
     */
    public $title_for_layout;

    /**
     * 基本description
     */
    public $meta_description;
    /**
     * ページネータの初期設定
     *
     * @var array
     */
//    public $paginate = [
//        'limit' => 20,
//    ];

    /**
     * 認定対象ゴール件数
     */
    public $unapproved_cnt = 0;

    /**
     * 評価対象ゴール件数
     */
    public $evaluable_cnt = 0;

    /**
     * 通知設定
     *
     * @var null
     */
    public $notify_setting = null;
    /**
     * ブラウザ情報
     *
     * @var array
     * @see AppController::_getBrowser()
     */
    private $_browser = [];

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        $this->uses = am($this->uses, $this->merge_uses);
        $this->components = am($this->components, $this->merge_components);
        $this->helpers = am($this->helpers, $this->merge_helpers);
    }

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->_setupAuth();
        //全ページ共通のタイトルセット(書き換える場合はこの変数の値を変更の上、再度アクションメソッド側でsetする)
        if (ENV_NAME == "www") {
            $this->title_for_layout = __('Goalous');
        } else {
            $this->title_for_layout = "[" . ENV_NAME . "]" . __('Goalous');
        }
        $this->set('title_for_layout', $this->title_for_layout);
        //全ページ共通のdescriptionのmetaタグの内容をセット(書き換える場合はこの変数の値を変更の上、再度アクションメソッド側でsetする)
        $this->meta_description = __(
            'Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',
            __("You can use Goalous on Web and on Mobile App."));
        $this->set('meta_description', $this->meta_description);

        $this->_setAppLanguage();
        $this->_decideMobileAppRequest();
        $this->_setIsTablet();
        $this->_setIsMobileBrowser();

        // Basic認証を特定の条件でかける
        if ($this->_isBasicAuthRequired()) {
            $this->_setBasicAuth();
        }
        $this->set('my_prof', $this->User->getMyProf());
        //ログイン済みの場合のみ実行する
        if ($this->Auth->user() && empty($this->Session->read('user_has_no_team'))) {

            $login_uid = $this->Auth->user('id');

            //sessionを手動で書き換える。cookieを更新するため。
            if ($this->request->is('get')) {
                if (!$this->Session->read('last_renewed') || $this->Session->read('last_renewed') < REQUEST_TIMESTAMP) {
                    $this->Session->renew();
                    $this->Session->write('last_renewed', REQUEST_TIMESTAMP + SESSION_RENEW_TTL);
                }
            }

            //通知の既読ステータス
            $notify_id = $this->request->query('notify_id');
            if ($notify_id) {
                $this->NotifyBiz->changeReadStatusNotification($notify_id);
            }

            // prohibit ajax request in read only term
            if ($this->request->is('ajax') && $this->_isProhibitedRequestByReadOnly()) {
                return $this->_forceErrorResponse();
            }
            // prohibit ajax request in status of cannot use service
            if ($this->request->is('ajax') && $this->_isProhibitedRequestByCannotUseService()) {
                return $this->_forceErrorResponse();
            }

            // by not ajax request
            if (!$this->request->is('ajax')) {
                if ($this->current_team_id) {
                    $currentTeam = $this->Team->getById($this->current_team_id);
                    if (empty($currentTeam) && $this->Session->read('referer_status') !== REFERER_STATUS_INVITED_USER_EXIST) {
                        return $this->Auth->logout();
                    }
                    $this->_setTerm();
                }
                $this->_setMyTeam();

                // when prohibit request in read only
                if ($this->_isProhibitedRequestByReadOnly()) {
                    $this->Notification->outError(__("You may only read your team’s pages."));
                    $this->redirect($this->referer());
                }
                // when prohibit request in status of cannot use service
                if ($this->_isProhibitedRequestByCannotUseService()) {
                    // it will be redirected to the page of notification that the service can not be used.
                    $this->redirect(['controller' => 'payments', 'action' => 'cannot_use_service']);
                }

                // Pass variable about team service use
                $this->_setValsForAlert();

                $active_team_list = $this->User->TeamMember->getActiveTeamList($login_uid);
                $set_default_team_id = !empty($active_team_list) ? key($active_team_list) : null;

                // アクティブチームリストに current_team_id が入っていない場合はログアウト
                // （チームが削除された場合）
                if ($this->current_team_id) {
                    if (!isset($active_team_list[$this->current_team_id]) && $this->Session->read('referer_status') !== REFERER_STATUS_INVITED_USER_EXIST) {
                        $this->Session->write('current_team_id', null);
                        //もしdefault_teamがそのチームだった場合はdefault_teamにnullをセット
                        if ($this->Auth->user('default_team_id') == $this->current_team_id) {
                            $this->User->updateDefaultTeam(null, true, $login_uid);
                        }
                        $this->Notification->outError(__("Logged out because the team you logged in is deleted."));
                        $this->Auth->logout();
                        return;
                    }
                }

                //リクエストがログイン中のチーム以外なら切り替える
                if ($this->request->is('get')) {
                    $this->_switchTeamBeforeCheck();
                }
                $is_isao_user = $this->_isIsaoUser($this->Session->read('Auth.User'),
                    $this->Session->read('current_team_id'));
                $this->set(compact('is_isao_user'));
                //getting notification without hide circle in home.
                if ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'home') {
                    $my_channels_json = $this->User->getMyChannelsJson(true);
                } else {
                    $my_channels_json = $this->User->getMyChannelsJson();
                }
                $this->set(compact('my_channels_json'));

                //デフォルトチームが設定されていない場合もしくはカレントチームで非アクティブの場合は
                //アクティブなチームでカレントチームとデフォルトチームを書き換え
                if (!$this->Auth->user('default_team_id') ||
                    !$this->User->TeamMember->isActive($login_uid)
                ) {
                    $this->User->updateDefaultTeam($set_default_team_id, true, $login_uid);
                    $this->Session->write('current_team_id', $set_default_team_id);
                    $this->_refreshAuth();
                    // すでにロード済みのモデルの current_team_id 等を更新する
                    foreach (ClassRegistry::keys() as $k) {
                        $obj = ClassRegistry::getObject($k);
                        if ($obj instanceof AppModel) {
                            $obj->current_team_id = $set_default_team_id;
                        }
                    }
                }
                $this->_setNotifySettings();
                $this->_setUnApprovedCnt($login_uid);
                $this->_setEvaluableCnt();
                $this->_setNotifyCnt();
                $this->_setSetupGuideStatus();
                $this->_setMyCircle();
                $this->_setActionCnt();
                $this->_setBrowserToSession();
                $this->_setTimeZoneEnvironment();
                $this->_setNotifyingCircleList();
                $this->_setCircleBadgeCount();
                $this->_setNewGoalousAssets();
            }
            $this->set('current_term', $this->Team->Term->getCurrentTermData());
            $this->_setMyMemberStatus();
            $this->_saveAccessUser($this->current_team_id, $this->Auth->user('id'));
            $this->_setAvailEvaluation();
            $this->_setAllAlertCnt();
        }
        $this->set('current_global_menu', null);
        $this->set('my_id', $this->Auth->user('id'));
        $this->set('my_team_id', $this->current_team_id);
        $this->set('userHasNoTeam', $this->Session->read('user_has_no_team') ?: false);
        $this->set('displayMobileAppFooter', $this->Auth->user() && $this->is_mb_app_web_footer);
        $this->_redirectIfMobileAppVersionUnsupported();
    }

    /**
     * @return CakeResponse
     */
    public function _forceErrorResponse()
    {
        $this->stopInvoke = true;
        $this->autoRender = false;
        $this->_ajaxPreProcess();
        return $this->_ajaxGetErrorResponse();
    }

    /**
     * redirect if Mobile App version is unsupported
     */
    private function _redirectIfMobileAppVersionUnsupported()
    {
        // GL-5962: show version expired if Goalous Mobile App version is old
        if (!$this->request->is('ajax')) {
            // not redirecting if route is '/app_force_update' or '/app_force_install' (avoiding redirect loop)
            if (!in_array(Router::url(), ['/app_force_update', '/app_force_install'])) {
                $userAgent = UserAgent::detect(Hash::get($_SERVER, 'HTTP_USER_AGENT') ?? '');
                if ($userAgent->isMobileAppAccess()) {
                    // https://jira.goalous.com/browse/GL-5962
                    // TODO: delete this "if" process, if old Android App(1.0.2) user is gone.
                    if ($this->_isAndroidVersionForceUninstall($userAgent)) {
                        $this->redirect('/app_force_install');
                    }
                    if ($this->_isExpiredVersionMobileApp($userAgent)) {
                        $this->redirect('/app_force_update');
                    }
                }
            }
        }
    }

    /**
     * return true if Goalous Mobile App version is not supported
     *
     * @param UserAgent $userAgent
     *
     * @return bool
     */
    private function _isExpiredVersionMobileApp(UserAgent $userAgent): bool
    {
        if (!$userAgent->isMobileAppAccess()) {
            return false;
        }
        $versionMobileAppLeast = '';
        if ($userAgent->isiOSApp()) {
            $versionMobileAppLeast = MOBILE_APP_IOS_VERSION_SUPPORTING_LEAST;
        } elseif ($userAgent->isAndroidApp()) {
            $versionMobileAppLeast = MOBILE_APP_ANDROID_VERSION_SUPPORTING_LEAST;
        }
        return MobileAppVersion::isExpired($versionMobileAppLeast, $userAgent->getMobileAppVersion());
    }

    /**
     * https://jira.goalous.com/browse/GL-5962
     * return true if Goalous Android App version is deprecated from google play store
     * temporary support: this method should be deleted on future.
     *
     * @param UserAgent $userAgent
     *
     * @return bool
     */
    private function _isAndroidVersionForceUninstall(UserAgent $userAgent): bool
    {
        if (!$userAgent->isAndroidApp()) {
            return false;
        }
        /**
         * @see version due to https://jira.goalous.com/browse/GL-5962 comments
         *       this magic number uses only here
         *       does not need to define somewhere
         */
        $versionMobileAppLeast = '1.0.4';
        return MobileAppVersion::isExpired($versionMobileAppLeast, $userAgent->getMobileAppVersion());
    }

    /**
     * This is wrapper parent invokeAction
     * - it can make execution stop until before render
     *
     * @param CakeRequest $request
     *
     * @return bool
     */
    public function invokeAction(CakeRequest $request)
    {
        if ($this->stopInvoke) {
            return false;
        }
        return parent::invokeAction($request);
    }

    public function _setBrowserToSession()
    {
        //UA情報をSessionにセット
        if (!$this->Session->read('ua')) {
            $ua = $this->_getBrowser();
            if (empty($ua['istablet']) && $ua['device_type'] == 'unknown') {
                $ua['device_type'] = 'Desktop';
            }
            $this->Session->write('ua', $ua);
        }
    }

    public function _setTimeZoneEnvironment()
    {
        $timezoneUser = floatval($this->Auth->user('timezone'));
        GoalousDateTime::setDefaultTimeZoneUserByHour($timezoneUser);

        $Team = ClassRegistry::init('Team');
        $team = $Team->getCurrentTeam();
        $timezoneTeam = floatval($team['Team']['timezone']);
        GoalousDateTime::setDefaultTimeZoneTeamByHour($timezoneTeam);
    }

    public function _setTerm()
    {
        $this->current_term_id = $this->Team->Term->getCurrentTermId();
        $this->next_term_id = $this->Team->Term->getNextTermId();
    }

    /**
     * 各種アラート件数の合計
     */
    public function _setAllAlertCnt()
    {
        $all_alert_cnt = $this->unapproved_cnt + $this->evaluable_cnt;
        $this->set(compact('all_alert_cnt'));
    }

    function _setNotifySettings()
    {
        $this->notify_setting = $this->User->NotifySetting->getMySettings();
        $this->set('notify_setting', $this->notify_setting);
    }

    /**
     * ログインユーザーが管理しているメンバーの中で認定されてないゴールの件数
     * - チームの評価設定がoffの場合はカウントしない。(0を返す)
     *
     * @param $login_uid
     */
    public function _setUnApprovedCnt($login_uid)
    {
        if ($this->Team->EvaluationSetting->isEnabled() === false) {
            return;
        }

        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        // サービス層でキャッシュを行う
        $unapproved_cnt = $GoalApprovalService->countUnapprovedGoal($login_uid);

        $this->set(compact('unapproved_cnt'));
        $this->unapproved_cnt = $unapproved_cnt;
    }

    function _setActionCnt()
    {
        $model = $this;
        $currentTerm = $model->Team->Term->getCurrentTermData();
        Cache::set('duration', self::CACHE_KEY_ACTION_COUNT_EXPIRE, 'user_data');
        $action_count = Cache::remember($this->Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true),
            function () use ($model, $currentTerm) {
                $timezone = $this->Team->getTimezone();
                $startTimestamp = AppUtil::getStartTimestampByTimezone($currentTerm['start_date'], $timezone);
                $endTimestamp = AppUtil::getEndTimestampByTimezone($currentTerm['end_date'], $timezone);
                $res = $model->Goal->ActionResult->getCount('me', $startTimestamp, $endTimestamp);
                return $res;
            }, 'user_data');
        $this->set(compact('action_count'));
    }

    function _setEvaluableCnt()
    {
        $this->evaluable_cnt = $this->Team->Evaluation->getMyTurnCount();
        $this->set('evaluable_cnt', $this->evaluable_cnt);
    }

    /**
     * isaoのユーザか判定
     * チームISAOもしくは、ISAOメールアドレスをプライマリに指定しているユーザを判別
     *
     * @param $user
     * @param $team_id
     *
     * @return bool
     */
    function _isIsaoUser($user, $team_id)
    {
        if ($team_id == ISAO_TEAM_ID) {
            return true;
        } else {
            if (!isset($user['PrimaryEmail']['email'])) {
                return false;
            }
            if (strstr($user['PrimaryEmail']['email'], ISAO_EMAIL_DOMAIN)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function _setMyTeam()
    {
        $my_teams = [];
        foreach ($this->User->TeamMember->getActiveTeamList($this->Auth->user('id')) as $key => $my_team) {
            $new_notify_cnt = $this->NotifyBiz->_getCountNewNotificationForTeams($key);
            if ($new_notify_cnt == 0) {
                $my_teams[$key] = $my_team;
            } else {
                $my_teams[$key] = $my_team . " ($new_notify_cnt)";
            }
        }
        $this->set('my_teams', $my_teams);
    }

    public function _setMyMemberStatus()
    {
        $team_member_info = $this->User->TeamMember->getWithTeam();
        $this->set('my_member_status', $team_member_info);
        $this->set('is_current_team_admin', !empty(Hash::get($team_member_info, 'TeamMember.admin_flg')));
    }

    public function _setMyCircle()
    {
        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init('CirclePinService');
        $myCircles = $CirclePinService->getMyCircleSortedList($this->Auth->user('id'), $this->current_team_id);
        $defaultCircle = $myCircles['default_circle'];
        $regularCircles = $myCircles['regular_circle'];

        $this->set('defaultCircle', $defaultCircle);
        $this->set('my_circles', $regularCircles);
    }

    public function _setCurrentCircle()
    {
        $current_circle = null;
        if (isset($this->request->params['named'])) {
            $params = array_merge($this->request->params, $this->request->params['named']);
        } else {
            $params = $this->request->params;
        }
        if (isset($params['circle_id']) && !empty($params['circle_id'])) {

            $is_secret = $this->User->CircleMember->Circle->isSecret($params['circle_id']);
            $is_exists_circle = $this->User->CircleMember->Circle->isBelongCurrentTeam($params['circle_id'],
                $this->Session->read('current_team_id'));
            $is_belong_circle_member = $this->User->CircleMember->isBelong($params['circle_id']);
            if ($is_exists_circle && (!$is_secret || ($is_secret && $is_belong_circle_member))) {
                $current_circle = $this->User->CircleMember->Circle->findById($params['circle_id']);
                $this->set('item_created',
                    isset($current_circle['Circle']['created']) ? $current_circle['Circle']['created'] : null);
            }
        }
        $this->set('current_circle', $current_circle);
    }

    public function _setFeedMoreReadUrl($controller = 'posts', $action = 'ajax_get_feed')
    {
        $base_url = ["controller" => $controller, 'action' => $action];
        $url = array_merge($base_url, $this->request->params['named']);
        foreach ($this->Post->orgParams as $key => $val) {
            if (array_key_exists($key, $this->request->params)) {
                $url = array_merge($url, [$key => $this->request->params[$key]]);
            }
        }
        $this->set('feed_more_read_url', $url);
    }

    function _switchTeam($team_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->Auth->user('id');
        }
        $this->User->TeamMember->updateLastLogin($team_id, $uid);
        $this->Session->write('current_team_id', $team_id);
    }

    /**
     * @param string $method
     */
    public function _ajaxPreProcess($method = 'ajax')
    {
        if (!$this->request->is($method)) {
            throw new RuntimeException(__('Invalid access'));
        }
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
    }

    /**
     * @param     $result
     * @param int $json_option
     *
     * @return CakeResponse
     */
    public function _ajaxGetResponse($result, $json_option = 0): CakeResponse
    {
        $this->response->type('json');
        if ($result !== null) {
            $this->response->body(json_encode($result, $json_option));
        } else {
            $this->response->body(null);
        }
        return $this->response;
    }

    /**
     * get error response
     *
     * @param     $result
     * @param int $statusCode
     *
     * @return CakeResponse
     */
    public function _ajaxGetErrorResponse($result = null, $statusCode = 400): CakeResponse
    {
        $this->response->statusCode($statusCode);
        $this->response->type('json');
        if ($result !== null) {
            $this->response->body(json_encode($result));
        } else {
            $this->response->body(null);
        }
        return $this->response;
    }

    /**
     * ベーシック認証が必要か？
     * - DevicesControllerは除外
     * - モバイルアプリは除外
     * - 特定の環境は除外
     */
    function _isBasicAuthRequired()
    {
        // アプリのデバイストークン追加、取得で用いられるため除外
        if ($this->request->params['controller'] == 'devices') {
            return false;
        }

        // モバイルアプリは除外
        if ($this->is_mb_app) {
            return false;
        }

        $excludeEnvs = [
            'local',
            'isao',
            'www',
            'stg',
        ];

        // 特定の環境を除外
        if (in_array(ENV_NAME, $excludeEnvs)) {
            return false;
        }

        // if env variable is not set, return false
        if (!defined('BASIC_AUTH_ID') || !defined('BASIC_AUTH_PASS')) {
            return false;
        }
        if (!BASIC_AUTH_ID || !BASIC_AUTH_PASS) {
            return false;
        }

        return true;
    }

    function _setBasicAuth()
    {
        $this->autoRender = false;
        if (!env('PHP_AUTH_USER')) {
            header('WWW-Authenticate: Basic realm="Private Page"');
            header('HTTP/1.0 401 Unauthorized');
            die("id / password Required");
        } else {
            if (env('PHP_AUTH_USER') != BASIC_AUTH_ID || env('PHP_AUTH_PW') != BASIC_AUTH_PASS) {
                header('WWW-Authenticate: Basic realm="Private Page"');
                header('HTTP/1.0 401 Unauthorized');
                die("Invalid id / password combination.  Please try again");
            }
        }
        $this->autoRender = true;
    }

    /**
     * 指定ヶ月後の月末のUnixtimeを返す
     *
     * @param int    $month_count
     * @param string $symbol
     *
     * @return int
     */
    function _getEndMonthLocalDateTime($month_count = 6, $symbol = "+")
    {
        if (!is_numeric($month_count)) {
            return null;
        }
        if (!in_array($symbol, ["+", "-"])) {
            return null;
        }
        $month_count++;
        $add_date = strtotime("{$symbol}{$month_count} month",
            REQUEST_TIMESTAMP + ($this->Auth->user('timezone') * 60 * 60));
        $year = date("Y", $add_date);
        $month = date("m", $add_date);
        $first_day = $year . "-" . $month . "-01";
        $end_day = strtotime("-1 day", strtotime($first_day));
        return $end_day;
    }

    /**
     * リクエストされたページが現在のページ以外の場合にチーム切換えを行う。
     * 所属していないチームの場合はエラー表示し、リファラにリダイレクト
     * コントローラの処理の最初に実行する事
     *
     * @return boolean
     */
    public function _switchTeamBeforeCheck()
    {
        $allow_controllers = array(
            'teams',
            'admins',
        );
        //許可コントローラの場合は何もせずreturn
        if (in_array($this->request->params['controller'], $allow_controllers)) {
            return false;
        }
        $current_team_id = $this->Session->read('current_team_id');
        $request_team_id = $this->_getTeamIdFromRequest();
        //チームidが判別できない場合は何もせずreturn
        if (!$request_team_id) {
            return false;
        }
        //リクエストされたチームと現在のチームが違う場合はチーム切換えを行う
        if ($current_team_id != $request_team_id) {
            //リクエストされたチームに所属しているか確認
            $team_list = $this->User->TeamMember->getActiveTeamList($this->Auth->user('id'));
            if (!array_key_exists($request_team_id, $team_list)) {
                //所属しているチームでは無い場合はエラー表示でtopにリダイレクト
                $this->Notification->outError(__("You don't have access right to this team."));
                $this->redirect('/');
            } else {
                //チームを切り替え
                $this->_switchTeam($request_team_id);
                $this->redirect($this->request->here);
            }
        }
        return false;
    }

    public function _getTeamIdFromRequest()
    {
        $request_params = $this->request->params;
        if (empty($request_params) ||
            !isset($request_params['controller']) ||
            empty($request_params['controller'])
        ) {
            return null;
        }
        $team_id = null;
        //対象IDを特定
        $id = null;
        //チームID指定されてた場合はチームIDを返す
        if (isset($request_params['named']['team_id']) && !empty($request_params['named']['team_id'])) {
            return $request_params['named']['team_id'];
        }
        if ($this->request->query('team_id')) {
            return $this->request->query('team_id');
        }
        //モデル名抽出
        $model_name = null;
        foreach ($this->User->model_key_map as $key => $model) {
            if ($id = Hash::get($request_params, "named.$key")) {
                $model_name = $model;
                break;
            } elseif ($id = Hash::get($request_params, $key)) {
                $model_name = $model;
                break;
            }

        }
        //IDが特定できない場合もしくはidが数値じゃない場合はnullを返す
        if (!$id || !is_numeric($id)) {
            return null;
        }
        $Model = ClassRegistry::init($model_name);
        switch ($Model->name) {
            case 'User':
                //Userの場合
                //相手が現在のチームに所属しているか確認
                $options = array(
                    'conditions' => array(
                        'user_id' => $id,
                        'team_id' => $this->Session->read('current_team_id'),
                        'status'  => Enum\Model\TeamMember\Status::ACTIVE,
                    ),
                );
                $team = $this->User->TeamMember->find('first', $options);
                $team_id = Hash::get($team, 'TeamMember.team_id');
                break;
            case 'Team':
                //チームの場合はそのまま
                $team_id = $id;
                break;
            default:
                $result = $Model->findWithoutTeamId('first', ['conditions' => ['id' => $id]]);
                if (empty($result)) {
                    return null;
                }
                $team_id = $result[$Model->name]['team_id'];
        }
        return $team_id;
    }

    /**
     * トップページからアクションするための情報をセット
     */
    public function _setGoalsForTopAction()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $canActionGoals = $GoalService->findActionables();
        $this->set(compact('canActionGoals'));
    }

    /**
     * 評価期間かどうかのフラグをセット
     */
    public function _setStartedEvaluation()
    {
        App::import('Service', 'EvaluationService');
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");

        $isStartedEvaluation = $EvaluationService->isStarted();
        $this->set(compact('isStartedEvaluation'));
    }

    /**
     * @param $id
     */
    public function _flashClickEvent($id)
    {
        $this->Flash->set(null, ['element' => 'flash_click_event', 'params' => ['id' => $id], 'key' => 'click_event']);
    }

    public function _setAvailEvaluation()
    {
        $this->set('is_evaluation_available', $this->Team->EvaluationSetting->isEnabled());
    }

    public function _setNotifyCnt()
    {
        $new_notify_cnt = $this->NotifyBiz->getCountNewNotification();
        $new_notify_message_cnt = $this->NotifyBiz->getCountNewMessageNotification();
        $unread_msg_topic_ids = $this->NotifyBiz->getUnreadMessagePostIds();
        $this->set(compact("new_notify_cnt", 'new_notify_message_cnt', 'unread_msg_topic_ids'));
    }

    public function _setCircleBadgeCount()
    {
        $UnreadPostsKey = new UnreadPostsKey($this->Auth->user('id'), $this->current_team_id);
        $UnreadPostsClient = new UnreadPostsClient();

        $UnreadPostsCount = count($UnreadPostsClient->read($UnreadPostsKey)->get());

        $this->set('circle_badge_cnt', $UnreadPostsCount);
    }

    function _getRedirectUrl()
    {
        $redirect_url = $this->request->data('Post.redirect_url');
        if ($redirect_url) {
            return $redirect_url;
        }

        $url_map = [
            'attached_file_list' => [
                'controller' => 'posts',
                'action'     => 'feed',
                'named'      => [
                    'circle_id'
                ]
            ]
        ];
        $parsed_url = Router::parse($this->referer(null, true));
        $referer_url = $this->referer(null, true);
        if ($url = Hash::get($url_map, Hash::get($parsed_url, 'action'))) {
            if ($names = Hash::get($url, 'named')) {
                unset($url['named']);
                foreach ($names as $name) {
                    if (Hash::get($parsed_url, "named.$name")) {
                        $url[$name] = $parsed_url['named'][$name];
                    }
                }
            }
            $referer_url = Router::url($url);
        }
        return $referer_url;
    }

    /**
     * ユーザーがアクセスした記録を残す
     *
     * @param $user_id
     */
    public function _saveAccessUser($team_id, $user_id)
    {
        $timezones = [
            9,    // 東京
            5.5,  // ニューデリー
            1,    // ベルリン
            -8,   // 太平洋標準時
        ];
        $this->GlRedis->saveAccessUser($team_id, $user_id, REQUEST_TIMESTAMP, $timezones);
    }

    /**
     * ブラウザ情報を返す
     */
    public function _getBrowser()
    {
        if (!$this->_browser) {
            $browscap = new \BrowscapPHP\Browscap();
            $this->_browser = (array)$browscap->getBrowser(null);;
        }
        return $this->_browser;
    }

    function _setResponseCsv($filename)
    {
        // safari は日本語ファイル名が文字化けするので特別扱い
        $browser = $this->_getBrowser();
        if ($browser['browser'] == 'Safari') {
            $this->response->header('Content-Disposition',
                sprintf('attachment; filename="%s";', $filename . '.csv'));
        } else {
            $this->response->header('Content-Disposition',
                sprintf('attachment; filename="%s"; filename*=UTF-8\'\'%s',
                    $filename . '.csv',
                    rawurlencode($filename . '.csv')));
        }
        $this->response->type('application/octet-stream');
    }

    function _setSetupGuideStatus()
    {
        $setup_guide_is_completed = $this->Auth->user('setup_complete_flg');
        if ($setup_guide_is_completed == User::SETUP_GUIDE_IS_COMPLETED) {
            $this->set('setup_status', null);
            $this->set('setup_rest_count', 0);
            return;
        }

        $status_from_redis = $this->_getStatusWithRedisSave();
        // remove last update time
        unset($status_from_redis[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME]);

        $this->set('setup_status', $status_from_redis);
        $this->set('setup_rest_count', count(User::$TYPE_SETUP_GUIDE) - count(array_filter($status_from_redis)));
        return;
    }

    function _getAllSetupDataFromRedis($user_id = false)
    {
        $user_id = ($user_id === false) ? $this->Auth->user('id') : $user_id;
        return $this->GlRedis->getSetupGuideStatus($user_id);
    }

    function _getStatusWithRedisSave($user_id = false)
    {
        $user_id = ($user_id === false) ? $this->Auth->user('id') : $user_id;
        $status = $this->_getAllSetupDataFromRedis($user_id);
        if (!$status) {
            $status = $this->User->generateSetupGuideStatusDict($user_id);
            //set update time
            $status[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME] = time();
            $this->GlRedis->saveSetupGuideStatus($user_id, $status);

            $status = $this->GlRedis->getSetupGuideStatus($user_id);
        }
        // remove last update time
        unset($status[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME]);

        return $status;
    }

    function _setValsForAlert()
    {
        // TODO.Payment: must refactoring.
        // Acquire only necessary information when necessary

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");

        $serviceUseStatus = $TeamService->getServiceUseStatus();
        $this->set('serviceUseStatus', $serviceUseStatus);
        $this->set('isTeamAdmin', boolval($this->User->TeamMember->isAdmin() ?? false));
        $this->set('stateEndDate', $TeamService->getStateEndDate());

        $isAdmin = $this->_isAdmin();
        if ($isAdmin && $serviceUseStatus == Team::SERVICE_USE_STATUS_PAID) {
            // show message if team last creditcard payment failed
            /** @var ChargeHistoryService $ChargeHistoryService */
            $ChargeHistoryService = ClassRegistry::init('ChargeHistoryService');
            $this->set('statusPaymentFailed', $ChargeHistoryService->isLatestChargeFailed($this->current_team_id));
        }

        $paymentSetting = $this->PaymentSetting->getCcByTeamId($this->current_team_id);
        // check if team credit card expire in one month
        if ($isAdmin && !empty($paymentSetting)) {
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init("CreditCardService");
            $dateNow = GoalousDateTime::now();
            $dateCreditCardExpire = $CreditCardService->getExpirationDateTimeOfTeamCreditCard($this->current_team_id);
            if (!is_null($dateCreditCardExpire)) {
                $this->set('teamCreditCardStatus', Team::STATUS_CREDIT_CARD_CLEAR);
                $dateCreditCardExpireBeforeOneMonth = $dateCreditCardExpire->copy()->subMonth(1);
                if ($dateNow->greaterThanOrEqualTo($dateCreditCardExpire)) {
                    // team credit card has been expired
                    $this->set('teamCreditCardStatus', Team::STATUS_CREDIT_CARD_EXPIRED);
                    $this->set('teamCreditCardExpireDate', $dateCreditCardExpire->format('Y-m-d'));
                } else {
                    if ($dateNow->greaterThanOrEqualTo($dateCreditCardExpireBeforeOneMonth)) {
                        // team credit card expire in 1 month at least
                        $this->set('teamCreditCardStatus', Team::STATUS_CREDIT_CARD_EXPIRE_SOON);
                        $this->set('teamCreditCardExpireDate', $dateCreditCardExpire->format('Y-m-d'));
                    }
                }
            }
        }
    }

    protected function _setDefaultTeam($team_id)
    {
        $userId = $this->Auth->user('id');
        try {
            $skipCheckUserStatus = !empty($this->Session->read('invited_team_id'));
            $this->User->TeamMember->permissionCheck($team_id, $userId, $skipCheckUserStatus);
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            GoalousLog::error("Error on setting user $userId default_team_id. " . $e->getMessage());
            $newTeamId = $this->User->TeamMember->getLatestLoggedInActiveTeamId($userId) ?: null;
            $this->Session->write('current_team_id', $newTeamId);
            $this->User->updateDefaultTeam($newTeamId, true, $userId);
            return false;
        }
        $this->Session->write('current_team_id', $team_id);
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
    }

    /**
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
            } else {
                $this->_setAfterLogin();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check permission if team administrator
     * [How to use]
     * ・Check in all action methods
     * 　Not set argument.
     * 　e.g. `$this->_checkAdmin();`
     * ・Check in specified action methods
     * 　set argument as array.
     * 　e.g.
     *    check method: index, create
     *    not check method: update
     *    `$this->_checkAdmin(['index', 'create']);`
     *
     * @param array $actionMethods
     *
     * @return \Cake\Network\Response|null
     */
    protected function _checkAdmin(array $actionMethods = [])
    {
        if (!$this->_isAdmin($actionMethods)) {
            $this->Notification->outError(__("You have no permission."));
            return $this->redirect('/');
        }
    }

    /**
     * Setup Authentication Component
     *
     * @return void
     */
    protected function _setupAuth()
    {
        $this->Auth->authenticate = [
            'Form2' => [
                'fields'         => [
                    'username' => 'email',
                    'password' => 'password'
                ],
                'userModel'      => 'User',
                'scope'          => [
                    'User.active_flg'             => 1,
                    'PrimaryEmail.email_verified' => 1
                ],
                'recursive'      => 0,
                'passwordHasher' => [
                    'className' => 'Simple',
                    'hashType'  => 'sha256' // if change it, should change User::generateHash() also.
                ]
            ]
        ];
        $st_login = REFERER_STATUS_LOGIN;
        $this->Auth->loginRedirect = "/{$st_login}";
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
     * pass `is_mb_browser` variable to view.
     * - get browser ua from browscap
     */
    public function _setIsMobileBrowser()
    {
        $browser = $this->_getBrowser();
        $this->is_mb_browser = $browser['ismobiledevice'];
        $this->set('isMobileBrowser', $this->is_mb_browser);
    }

    /**
     * pass `isTablet` variable to view.
     * - get browser ua from browscap
     */
    public function _setIsTablet()
    {
        $browser = $this->_getBrowser();
        $this->is_tablet = $browser['istablet'];
        $this->set('isTablet', $this->is_tablet);
    }

    /**
     * Set new Goalous assets to prefetch on old Goalous
     */
    public function _setNewGoalousAssets()
    {
        if (!$this->request->is('get')) {
            $this->set('newGoalousAssets', []);
            return;
        }
        /** @var NewGoalousAssetsStorageClient $NewGoalousAssetsStorageClient */
        $NewGoalousAssetsStorageClient = ClassRegistry::init('NewGoalousAssetsStorageClient');
        $newGoalousAssets = $NewGoalousAssetsStorageClient->getKeys();
        $this->set('newGoalousAssets', $newGoalousAssets);
    }

    /**
     * Set list of joined circles with enabled notification for this user
     */
    protected function _setNotifyingCircleList(){

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        $circleIds = [];

        $circles = $CircleMember->getCirclesWithNotificationFlg($this->Auth->user('id'), true);
        /** @var CircleMemberEntity $circle */
        foreach ($circles as $circle) {
            $circleIds[] = strval($circle['circle_id']);
        }

        $this->set('my_notifying_circles', $circleIds);
    }

}
