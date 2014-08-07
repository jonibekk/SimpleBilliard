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
 * @property LangComponent              $Lang
 * @property SessionComponent           $Session
 * @property TimezoneComponent          $Timezone
 * @property CookieComponent            $Cookie
 * @property GlEmailComponent           $GlEmail
 * @property PnotifyComponent           $Pnotify
 * @property MixpanelComponent          $Mixpanel
 * @property OgpComponent               $Ogp
 * @property User                       $User
 * @property Post                       $Post
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

        $this->_setAppLanguage();
        //ajaxの時以外で実行する
        if (!$this->request->is('ajax')) {
            $this->_setMyTeam();
            $this->_setMyMemberStatus();
        }
        //ページタイトルセット
        $this->set('title_for_layout', SERVICE_NAME);
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
        $this->set('my_circles', $this->User->CircleMember->getMyCircle());
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

}
