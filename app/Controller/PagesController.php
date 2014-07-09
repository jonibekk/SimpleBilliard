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
//TODO 現状これが必要になるケースが無いため、一旦コメントアウト
//        $count = count($path);
//        if (!$count) {
//            /** @noinspection PhpVoidFunctionResultUsedInspection */
//            return $this->redirect('/');
//        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
//TODO 現状これが必要になるケースが無いため、一旦コメントアウト
//        if (!empty($path[1])) {
//            $subpage = $path[1];
//        }
        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));

        //ログインしている場合とそうでない場合の切り分け
        if ($this->Auth->user()) {
            if ($path[0] == 'home') {
                if ($this->Session->read('add_new_mode') === MODE_NEW_PROFILE) {
                    $this->Session->delete('add_new_mode');
                    $this->set('mode_view', MODE_VIEW_TUTORIAL);
                }
                $this->Paginator->settings = $this->Post->getPaginateOption();
                $posts = $this->Paginator->paginate('Post');
                $this->set(compact('posts'));
                return $this->render('logged_in_home');
            }
            else {
                $this->layout = LAYOUT_ONE_COLUMN;
                return $this->render(implode('/', $path));
            }
        }
        else {
            $this->layout = 'homepage';
            return $this->render(implode('/', $path));
        }
    }

    public function beforeFilter()
    {
        $this->_setLanguage();
        //全ページ許可
        $this->Auth->allow();
        //切り換え可能な言語をセット
        $this->set('lang_list', $this->_getPageLanguageList());
        parent::beforeFilter();
    }

    public function _setLanguage()
    {
        // パラメータから言語をセット
        $this->set('top_lang', null);
        if (isset($this->request->params['lang'])) {
            $this->set('top_lang', $this->request->params['lang']);
            Configure::write('Config.language', $this->request->params['lang']);
        }
    }

    /**
     * トップ用言語リスト
     */
    public function _getPageLanguageList()
    {
        $lang_list = [
            'ja' => __d('home', "Japanese"),
            'en' => __d('home', "English"),
        ];
        return $lang_list;
    }
}
