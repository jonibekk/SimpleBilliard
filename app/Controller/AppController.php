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
*@package        app.Controller
 * @link           http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @property LangComponent       $Lang
 * @property TimezoneComponent   $Timezone
 * @property CookieComponent     $Cookie
 * @property User                $User
 */
class AppController extends Controller
{
    public $components = [
        'DebugKit.Toolbar',
        'Session',
        'Paginator',
        'Auth' => ['flash' => [
            'element' => 'alert',
            'key'     => 'auth',
            'params'  => ['plugin' => 'BoostCake', 'class' => 'alert-error']
        ]],
        'Lang',
        'Cookie',
        'Timezone',
    ];
    public $helpers = [
        'Session',
        'Html'      => ['className' => 'BoostCake.BoostCakeHtml'],
        'Form'      => ['className' => 'BoostCake.BoostCakeForm'],
        'Paginator' => ['className' => 'BoostCake.BoostCakePaginator'],
    ];

    public $uses = [
        'User',
    ];

    /**
     * @var null
     */
    public $top_lang = null;

    /**
     * Mixpanel
     *
     * @var MixPanel
     */
    public $Mp;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_setAppLanguage();
        //TODO 一時的に全許可
        $this->Auth->allow();
        //mixpanel初期化
        if (PUBLIC_ENV) {
            $this->Mp = Mixpanel::getInstance(MIXPANEL_TOKEN);
        }
        //ページタイトルセット
        $this->set('title_for_layout', SERVICE_NAME);
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
}
