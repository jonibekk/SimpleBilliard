<?php
/**
 * Static content controller.
 * This file will render views from views/pages/
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link          http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 * @property User $User
 * @noinspection  PhpInconsistentReturnPointsInspection
 */
class PagesController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = ['User'];

    /**
     * Displays a view
     *
     * @throws NotFoundException
     * @throws Exception
     * @throws MissingViewException
     * @internal param \What $mixed page to display
     * @return $this->redirect('/') or void
     */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));

        //ログインしている場合とそうでない場合の切り分け
        if ($this->Auth->user()) {
            if ($path[0] == 'home') {
                //homeの場合
                if ($this->Session->read('completed_today_alist')) {
                    //全てのリストが完了している場合はモーダル表示
                    $this->set('completed_today_alist', true);
                    $this->Session->delete('completed_today_alist');
                }

                $this->render('logged_in_home');
            }
            else {
                $this->render(implode('/', $path));
            }
        }
        else {
            //ログインしていない場合のヘッダー
            //$this -> layout = 'not_logged_in';
            $this->layout = 'homepage';
            //現在の登録ユーザ数
            $user_count = $this->User->getAllUsersCount();
            $this->set(compact('user_count'));
            if ($path[0] == 'logged_in_home') {
                $this->render('home');
            }
            else {
                $this->render(implode('/', $path));
            }
        }
        return $this->render(implode('/', $path));
    }

    public function beforeFilter()
    {
        $this->_setPageLanguage();
        parent::beforeFilter();
    }

    /**
     * Pagesのみの言語設定
     */
    public function _setPageLanguage()
    {
        //言語切換えパラメータに対応
        if (!$this->Auth->user()) {
            /**
             * TODO 本来はRouterで設定し、Viewの$this->linkも逆ルーティングで置き換えるやり方が正しいが、良い方法が見つからないのでリダイレクトで対応。Router設定に準拠。
             */
            //言語設定を取得
            $cookie_lang = $this->Cookie->read('language');
            $param_lang = (isset($this->request->params['lang'])) ? $this->request->params['lang'] : null;
            //使用可能言語をフロントに渡す
            $lang_list = $this->Lang->getAvailLangList();
            if (array_key_exists($param_lang, $lang_list)) {
                //$param_langが英語で$cookie_langが英語以外の場合はクッキーの言語にリダイレクト
                if ($param_lang == "eng" && $cookie_lang != $param_lang) {
                    //$cookie_langがセットされていなければ、現在のブラウザ設定を保存
                    if (!$cookie_lang) {
                        $this->Cookie->write('language', $this->Lang->getLanguage());
                    }
                    $params = $this->request->params;
                    if ($params['pass'][0] == "home") {
                        //ホームの場合
                        $this->redirect("/" . $this->Cookie->read('language'));
                    }
                    else {
                        //ホーム以外の場合
                        $this->redirect("/" . $this->Cookie->read('language') . "/" . $params['controller'] . "/" . $params['pass'][0]);
                    }
                }
                Configure::write('Config.language', $param_lang);
            }
            else {
                //指定された言語が利用不可の場合はルートにリダイレクト
                $this->redirect('/');
            }
            $this->set(compact('lang_list', 'lang'));
        }
    }
}
