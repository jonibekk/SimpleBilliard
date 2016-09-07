<?php
App::uses('Controller', 'Controller');

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
 * @property SessionComponent  $Session
 * @property SecurityComponent $Security
 * @property AuthComponent     $Auth
 * @property User              $User
 * @property Post              $Post
 * @property Goal              $Goal
 * @property Team              $Team
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
    ];

    public $uses = [
        'User',
        'Post',
        'Goal',
        'Team',
        'GlRedis',
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

}
