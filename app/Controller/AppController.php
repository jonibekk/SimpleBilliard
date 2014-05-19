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
 * @property LangComponent $Lang
 * @property User          $User
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
     * Mixpanel
     *
     * @var MixPanel
     */
    public $Mp;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_setLanguage();
        //TODO 一時的に全許可
        $this->Auth->allow();
        //mixpanel初期化
        if (PUBLIC_ENV) {
            $this->Mp = Mixpanel::getInstance(MIXPANEL_TOKEN);
        }
        //ページタイトルセット
        $this->set('title_for_layout', SERVICE_NAME);
    }

    public function _setLanguage()
    {
        //言語切換えパラメータに対応
        if (!$this->Auth->user()) {
            $lang = null;

            //TODO 理想としては以下の対応（時間がかかる為、今はやらない）
            //一旦、英語をセット
            //Configure::write('Config.language', 'eng');
            //言語切換えの場合
            //英語ならパラメータ無しでルートにリダイレクト
            //英語以外はパラメータ付与してリダイレクト

            //言語パラメータ無しの場合
            //ブラウザ設定が英語以外の場合は言語パラメータ付きでリダイレクト
            //パラメータありの場合
            //使用可能な値ならリダイレクト無しで、全urlにパラメータ付与。
            //使用できない場合はルートにリダイレクト

            //TODO 現状、以下の対応
            //言語切換え
            if (isset($this->request->query['change_lang']) && !empty($this->request->query['change_lang'])) {
                $lang = h($this->request->query['change_lang']);
                $this->redirect("/?l=$lang");
            }
            //明示的な言語切換え時は全URLに言語パラメータを付与
            if ((isset($this->request->query['l']) && !empty($this->request->query['l']))
                || (isset($this->request->query['change_lang']) && !empty($this->request->query['change_lang']))
            ) {
                $lang = h($this->request->query['l']);

                //存在する言語か判定し、存在する場合は言語切換え
                $this->Lang->changeLang($lang);

                //全てのURLに言語パラメータを付与
                ini_set("url_rewriter.tags", "a=href,area=href,frame=src,form=action,fieldset=");
                output_add_rewrite_var('l', $lang);
            }

            //使用可能言語をフロントに渡す
            $lang_list = $this->Lang->getAvailLangList();
            $this->set(compact('lang_list', 'lang'));

        }

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
