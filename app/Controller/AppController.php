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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link           http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @property LangComponent               $Lang
 * @property SessionComponent            $Session
 * @property SecurityComponent           $Security
 * @property TimezoneComponent           $Timezone
 * @property CookieComponent             $Cookie
 * @property GlEmailComponent            $GlEmail
 * @property PnotifyComponent            $Pnotify
 * @property MixpanelComponent           $Mixpanel
 * @property OgpComponent                $Ogp
 * @property User                        $User
 * @property Post                        $Post
 * @property Goal                        $Goal
 * @property NotifyBizComponent          $NotifyBiz
 */
class AppController extends Controller
{
    public $components = [
        'DebugKit.Toolbar',
        'Session',
        //TODO Securityコンポーネントを利用した場合のテスト通過方法がわからない。要調査
        'Security' => [
            'csrfUseOnce' => false,
            'csrfExpires' => '+24 hour'
        ],
        'Paginator',
        'Auth'     => ['flash' => [
            'element' => 'alert',
            'key'     => 'auth',
            'params'  => ['plugin' => 'BoostCake', 'class' => 'alert-error']
        ]],
        'Lang',
        'Cookie',
        'Timezone',
        'GlEmail',
        'Pnotify',
        'Mixpanel',
        'Ogp',
        'NotifyBiz',
    ];
    public $helpers = [
        'Session',
        'Html'      => ['className' => 'BoostCake.BoostCakeHtml'],
        'Form'      => ['className' => 'BoostCake.BoostCakeForm'],
        'Paginator' => ['className' => 'BoostCake.BoostCakePaginator'],
        'Upload',
        'TimeEx',
        'TextEx',
    ];

    public $uses = [
        'User',
        'Post',
        'Goal',
    ];

    /**
     * ページネータの初期設定
     *
     * @var array
     */
//    public $paginate = [
//        'limit' => 20,
//    ];

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->_setSecurity();
        $this->_setAppLanguage();
        //ログイン済みの場合のみ実行する
        if ($this->Auth->user()) {
            //ajaxの時以外で実行する
            if (!$this->request->is('ajax')) {
                $this->_setMyTeam();
                //リクエストがログイン中のチーム以外なら切り替える
                if ($this->request->is('get')) {
                    $this->_switchTeamBeforeCheck();
                }
            }
            $this->_setMyMemberStatus();
        }
        //ページタイトルセット
        $this->set('title_for_layout', SERVICE_NAME);
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

    public function forceSSL()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->redirect('https://' . env('HTTP_HOST') . $this->here);
    }

    public function _setMyTeam()
    {
        $this->set('my_teams', $this->User->TeamMember->getActiveTeamList($this->Auth->user('id')));
    }

    public function _setMyMemberStatus()
    {
        $this->set('my_member_status', $this->User->TeamMember->getWithTeam());
    }

    public function _setMyCircle()
    {
        $my_circles = $this->User->CircleMember->getMyCircle();
        $current_circle = null;
        if (isset($this->request->params['circle_id']) &&
            !empty($this->request->params['circle_id']) &&
            !empty($my_circles)
        ) {
            foreach ($my_circles as $key => $circle) {
                if ($circle['Circle']['id'] == $this->request->params['circle_id']) {
                    $current_circle = $circle;
                    //未読件数を0セット
                    if ($circle['CircleMember']['unread_count'] != 0) {
                        $this->User->CircleMember->updateUnreadCount($circle['Circle']['id']);
                        $my_circles[$key]['CircleMember']['unread_count'] = 0;
                    }
                    break;
                }
            }
        }
        $this->set('my_circles', $my_circles);
        $this->set('current_circle', $current_circle);
    }

    public function _setFeedMoreReadUrl()
    {
        $base_url = ["controller" => "posts", 'action' => 'ajax_get_feed'];
        $url = array_merge($base_url, $this->request->params['named']);
        foreach ($this->Post->orgParams as $key => $val) {
            if (array_key_exists($key, $this->request->params)) {
                $url = array_merge($url, [$key => $this->request->params[$key]]);
            }
        }
        $this->set('feed_more_read_url', $url);
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
        }
        else {
            $lang = $this->Lang->getLanguage();
            $this->set('is_not_use_local_name', $this->User->isNotUseLocalName($lang));
        }
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
        $this->Auth->logout();
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
            }
            else {
                $associations[$key] = $val;
            }
        }
        if (isset($user['User'])) {
            $user['User'] = array_merge($user['User'], $associations);
        }
        $this->User->me = $user['User'];
        return $this->Auth->login($user['User']);
    }

    function _switchTeam($team_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->Auth->user('id');
        }
        $this->User->TeamMember->updateLastLogin($team_id, $uid);
        $this->Session->write('current_team_id', $team_id);
    }

    public function _ajaxPreProcess()
    {
        if (!$this->request->is('ajax')) {
            throw new RuntimeException(__d('exception', '不正なアクセスです。'));
        }
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
    }

    public function _ajaxGetResponse($result)
    {
        //レスポンスをjsonで生成
        $this->response->type('json');
        $this->response->body(json_encode($result));
        return $this->response;
    }

    function _setBasicAuth()
    {
        $loginId = 'isao';
        $loginPassword = '1qaz2wsx';

        $this->autoRender = false;
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Private Page"');
            header('HTTP/1.0 401 Unauthorized');
            die("id / password Required");
        }
        else {
            if ($_SERVER['PHP_AUTH_USER'] != $loginId || $_SERVER['PHP_AUTH_PW'] != $loginPassword) {
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
    function getEndMonthLocalDateTime($month_count = 6, $symbol = "+")
    {
        if (!is_numeric($month_count)) {
            return null;
        }
        if (!in_array($symbol, ["+", "-"])) {
            return null;
        }
        $month_count++;
        $add_date = strtotime("{$symbol}{$month_count} month", time() + ($this->Auth->user('timezone') * 60 * 60));
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
        $request_team_id = $this->_getTeamIdFromRequest($this->request->params);
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
                $this->Pnotify->outError(__d('gl', "このチームへのアクセス権限がありません。"));
                $this->redirect('/');
            }
            else {
                //チームを切り替え
                $this->_switchTeam($request_team_id);
                $this->redirect($this->request->here);
            }
        }
        return false;
    }

    public function _getTeamIdFromRequest($request_params)
    {
        if (empty($request_params)) {
            return null;
        }
        if (isset($request_params['controller']) && !empty($request_params['controller'])
        ) {
            //対象IDを特定
            $id = null;
            //サークルID指定されてた場合
            if (isset($request_params['circle_id']) && !empty($request_params['circle_id'])) {
                $id = $request_params['circle_id'];
            }
            //投稿ID指定されてた場合
            elseif (isset($request_params['post_id']) && !empty($request_params['post_id'])) {
                $id = $request_params['post_id'];
            }
            //チームID指定されてた場合
            elseif (isset($request_params['team_id']) && !empty($request_params['team_id'])) {
                $id = $request_params['team_id'];
            }
            //通常のID指定されていた場合
            elseif (isset($request_params['pass'][0]) && !empty($request_params['pass'][0])) {
                $id = $request_params['pass'][0];
            }

            //IDが特定できない場合はnullを返す
            if (!$id) {
                return null;
            }
            //idが数値じゃない場合はnullを返す
            if (!is_numeric($id)) {
                return null;
            }

            //モデル名抽出
            $model_name = null;
            if ($request_params['controller'] == 'pages') {
                $model_name = 'Team';
            }
            elseif (isset($request_params['circle_id']) && !empty($request_params['circle_id'])) {
                $model_name = 'Circle';
            }
            else {
                $model_name = Inflector::classify($request_params['controller']);
            }
            $Model = ClassRegistry::init($model_name);

            $team_id = null;
            switch ($Model->name) {
                case 'User':
                    //Userの場合
                    //相手が現在のチームに所属しているか確認
                    $options = array(
                        'conditions' => array(
                            'user_id' => $id,
                            'team_id' => $this->Session->read('current_team_id'),
                        ),
                    );
                    $team = $this->User->TeamMember->find('first', $options);
                    if (!empty($team)) {
                        $team_id = $team['TeamMember']['team_id'];
                    }
                    break;
                case 'Team':
                    //チームの場合はそのまま
                    $team_id = $id;
                    break;
                default:
                    $Model->recursive = -1;
                    $result = $Model->findById($id);
                    $Model->recursive = 1;
                    if (empty($result)) {
                        return null;
                    }
                    $team_id = $result[$Model->name]['team_id'];
            }
            return $team_id;
        }
        return null;
    }
}
