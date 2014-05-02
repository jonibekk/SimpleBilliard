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
        ]]
    ];
    public $helpers = [
        'Session',
        'Html'      => ['className' => 'BoostCake.BoostCakeHtml'],
        'Form'      => ['className' => 'BoostCake.BoostCakeForm'],
        'Paginator' => ['className' => 'BoostCake.BoostCakePaginator'],
    ];

    /**
     * Mixpanel
     *
     * @var MixPanel
     */
    public $Mp;

    public function beforeFilter()
    {
        parent::beforeFilter();
        //TODO 一時的に全許可
        $this->Auth->allow();
        //mixpanel初期化
        if (PUBLIC_ENV) {
            $this->Mp = Mixpanel::getInstance(MIXPANEL_TOKEN);
        }
        //ページタイトルセット
        $this->set('title_for_layout', SERVICE_NAME);
    }
}
