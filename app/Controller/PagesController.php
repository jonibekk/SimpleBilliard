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
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }

        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));

        //ログインしていない場合
        if (!$this->Auth->user()) {
            $this->layout = 'homepage';
            //html出力結果をキャッシュ
            $url = "/" . $this->request->url . "_lang:" . Configure::read('Config.language');;
            if (CACHE_HOMEPAGE) {
                if (!$out = Cache::read($url, 'homepage')) {
                    $out = $this->render(implode('/', $path));
                    Cache::write($url, $out, 'homepage');
                }
            }
            else {
                $out = $this->render(implode('/', $path));
            }
            return $out;
        }

        // 1カラムレイアウト
        if ($path[0] !== 'home') {
            $this->layout = LAYOUT_ONE_COLUMN;
            return $this->render(implode('/', $path));
        }

        // プロフィール作成モードの場合、ビューモードに切り替え
        if ($this->Session->read('add_new_mode') === MODE_NEW_PROFILE) {
            $this->Session->delete('add_new_mode');
            $this->set('mode_view', MODE_VIEW_TUTORIAL);
        }

        // ビュー変数のセット
        $this->_setCurrentCircle();
        $this->_setFeedMoreReadUrl();
        $this->_setViewValOnRightColumn();
        // 現在のチーム
        $current_team = $this->Team->getCurrentTeam();
        $this->set('item_created', isset($current_team['Team']['created']) ? $current_team['Team']['created'] : null);
        $this->set('current_team', $current_team);
        // チーム全体サークル
        $this->set('team_all_circle', $this->Team->Circle->getTeamAllCircle());
        $current_global_menu = "home";
        $feed_filter = 'all';
        $this->set(compact('feed_filter', 'current_global_menu'));
        $this->set('long_text', false);
        if ($form_type = viaIsSet($this->request->params['common_form_type'])) {
            $this->set('common_form_type', $form_type);
        }
        else {
            $this->set('common_form_type', 'action');
        }

        try {
            $this->set(['posts' => $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
                                                    $this->request->params)]);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        return $this->render('logged_in_home');
    }

    public function beforeFilter()
    {
        $this->_setLanguage();
        //全ページ許可
        $this->Auth->allow('display');
        //チームidがあった場合は許可しない
        if (isset($this->request->params['team_id'])) {
            $this->Auth->deny('display');
        }

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
