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
            return $this->render(implode('/', $path));
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
        $this->Auth->allow();

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
        $lang = $this->_getLangFromParam();
        if ($lang) {
            $this->set('top_lang', $lang);
            Configure::write('Config.language', $lang);
        }
    }

    function _getLangFromParam()
    {
        $lang = null;
        if (isset($this->request->params['lang'])) {
            $lang = $this->request->params['lang'];
        }
        elseif (isset($this->request->params['named']['lang'])) {
            $lang = $this->request->params['named']['lang'];
        }
        return $lang;
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

    public function contact($type = null)
    {
        $lang = $this->_getLangFromParam();
        //もしログイン済ならトップにリダイレクト
        if ($this->Auth->user()) {
            return $this->redirect('/');
        }
        $this->layout = 'homepage';
        $this->set('type_options', $this->_getContactTypeOption());
        $this->set('selected_type', $type);

        if ($this->request->is('get')) {
            return $this->render();
        }
        /**
         * var Email $Email
         */
        $Email = ClassRegistry::init('Email');
        $Email->validate = $Email->contact_validate;
        $Email->set($this->request->data);
        $data = Hash::extract($this->request->data, 'Email');
        if ($Email->validates()) {
            if (empty($data['sales_people'])) {
                $data['sales_people_text'] = __d('lp', '指定なし');
            }
            else {
                $data['sales_people_text'] = implode(', ', $data['sales_people']);
            }
            $data['want_text'] = $this->_getContactTypeOption()[$data['want']];

            $this->Session->write('contact_form_data', $data);
            $lang = $this->_getLangFromParam();
            return $this->redirect(['action' => 'contact_confirm', 'lang' => $lang]);
        }
        return $this->render();
    }

    private function _getContactTypeOption()
    {
        return [
            null => __d('lp', '選択してください'),
            1    => __d('lp', '詳しく知りたい'),
            2    => __d('lp', '資料がほしい'),
            3    => __d('lp', '協業したい'),
            4    => __d('lp', '取材したい'),
            5    => __d('lp', 'その他'),
        ];
    }

    public function contact_confirm()
    {
        //もしログイン済ならトップにリダイレクト
        if ($this->Auth->user()) {
            return $this->redirect('/');
        }
        $this->layout = 'homepage';
        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Pnotify->outError(__d('validate', '問題が発生したため、処理が完了しませんでした。'));
            return $this->redirect($this->referer());
        }
        $this->set(compact('data'));
        return $this->render();
    }

    public function contact_send()
    {
        //もしログイン済ならトップにリダイレクト
        if ($this->Auth->user()) {
            return $this->redirect('/');
        }

        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Pnotify->outError(__d('validate', '問題が発生したため、処理が完了しませんでした。'));
            return $this->redirect($this->referer());
        }
        $this->Session->delete('contact_form_data');
        //メール送信処理
        App::uses('CakeEmail', 'Network/Email');
        if (ENV_NAME === "local") {
            $config = 'default';
        }
        else {
            $config = 'amazon';
        }

        // 送信処理
        $email = new CakeEmail($config);
        $email
            ->template('contact', 'default')
            ->viewVars(['data' => $data])
            ->emailFormat('text')
            ->to([$data['email'] => $data['email']])
            ->bcc(['contact@goalous.com' => 'contact@goalous.com'])
            ->subject(__d('mail', '【Goalous】お問い合わせありがとうございました'))
            ->send();
        $lang = $this->_getLangFromParam();
        return $this->redirect(['controller' => 'pages', 'action' => 'display', 'pagename' => 'contact_thanks', 'lang' => $lang,]);
    }
}
