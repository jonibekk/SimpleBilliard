<?php
/**
 * Application level Controller
 *
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
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 *
 */
class AppController extends Controller
{
    public $components = array(
        'DebugKit.Toolbar',
        'Session',
        'Paginator',
        'Auth' => array(
            'flash' => array(
                'element' => 'alert',
                'key'     => 'auth',
                'params'  => array(
                    'plugin' => 'BoostCake',
                    'class'  => 'alert-error'
                )
            )
        )
    );
    public $helpers = array(
        'Session',
        'Html'      => array('className' => 'BoostCake.BoostCakeHtml'),
        'Form'      => array('className' => 'BoostCake.BoostCakeForm'),
        'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
    );

    /**
     * Mixpanel
     * @var MixPanel
     */
    public $Mp;

    public function beforeFilter()
    {
        parent::beforeFilter();
        //TODO 一時的に全許可
        $this->Auth->allow();
        //mixpanel初期化
        $this->Mp = Mixpanel::getInstance(MIXPANEL_PROJECT_TOKEN);
    }
}
