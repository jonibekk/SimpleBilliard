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

        // Difine URL params for Google analytics.
        $this->_setUrlParams();

        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));

        //ログインしているかつ、topの場合はフィード表示
        if ($this->Auth->user() && $path[0] === 'home') {
            $this->_setTopAllContentIfLoggedIn();
            return $this->render('logged_in_home');
        }

        $this->layout = LAYOUT_HOMEPAGE;
        //html出力結果をキャッシュ
        $url = "/" . $this->request->url . "_lang:" . Configure::read('Config.language');
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

    function _setTopAllContentIfLoggedIn()
    {
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
        //省略型の言語テキストをviewにセット
        $short_lang = $this->Lang->getShortLang();
        $available_lang = $this->_getPageLanguageList();
        if (!array_key_exists($short_lang, $available_lang)) {
            $short_lang = 'en';
        }
        $this->set('short_lang', $short_lang);
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
            'ja' => __("Japanese"),
            'en' => __("English"),
        ];
        return $lang_list;
    }

    public function contact($type = null)
    {
        $this->layout = LAYOUT_HOMEPAGE;
        $this->set('type_options', $this->_getContactTypeOption());
        $this->set('selected_type', $type);

        if ($this->request->is('get')) {
            if (isset($this->request->params['named']['from_confirm']) &&
                $this->Session->read('contact_form_data')
            ) {
                $this->request->data['Email'] = $this->Session->read('contact_form_data');
            }
            return $this->render();
        }
        /**
         * @var Email $Email
         */
        $Email = ClassRegistry::init('Email');
        $Email->validate = $Email->contact_validate;
        $Email->set($this->request->data);
        $data = Hash::extract($this->request->data, 'Email');
        if ($Email->validates()) {
            if (empty($data['sales_people'])) {
                $data['sales_people_text'] = __('Anyone');
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
            null => __('Please select'),
            1    => __('Get more information'),
            2    => __('Get documentation'),
            3    => __('Collaborate with ISAO'),
            4    => __('Give us an interview'),
            5    => __('Others'),
        ];
    }

    public function contact_confirm()
    {
        $this->layout = LAYOUT_HOMEPAGE;
        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Pnotify->outError(__('Ooops. Some problems occured.'));
            return $this->redirect($this->referer());
        }
        $this->set(compact('data'));
        return $this->render();
    }

    public function contact_send()
    {
        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Pnotify->outError(__('Ooops. Some problems occured.'));
            return $this->redirect($this->referer());
        }
        $this->Session->delete('contact_form_data');
        //メール送信処理
        App::uses('CakeEmail', 'Network/Email');
        if (ENV_NAME === "local") {
            $config = 'default';
        }
        else {
            $config = 'amazon_contact';
        }

        // 送信処理
        $email = new CakeEmail($config);
        $email
            ->template('contact', 'default')
            ->viewVars(['data' => $data])
            ->emailFormat('text')
            ->to([$data['email'] => $data['email']])
            ->bcc(['contact@goalous.com' => 'contact@goalous.com'])
            ->subject(__('Goalous - Thanks for your contact.'))
            ->send();
        $lang = $this->_getLangFromParam();
        return $this->redirect(['controller' => 'pages', 'action' => 'display', 'pagename' => 'contact_thanks', 'lang' => $lang,]);
    }

    public function _setUrlParams() {
        $url_params = $this->params['url'];

        if($this->Auth->user()) {
            $parsed_referer_url = Router::parse($this->referer('/', true));
            $request_status = viaIsSet($url_params['st']);
            $status_from_referer = $this->_defineStatusFromReferer();

            // When parametes separated from google analitics already exists,
            // ignore redirect for google analitics.
            $reserved_params = ['notify_id', 'after_click', 'common_form', 'team_id', 'from'];
            foreach($reserved_params as $param) {
                if(viaIsSet($this->request->params[$param]) || viaIsSet($this->request->params['named'][$param])) {
                    return true;
                }
            }

            if($request_status !== $status_from_referer) {
                return $this->redirect("/?st={$status_from_referer}");
            }
            $this->Session->delete('referer_status');
            return true;
        }

        if($url_params) {
            return $this->redirect('/');
        }
        return true;
    }

    public function _defineStatusFromReferer() {
        switch ($this->Session->read('referer_status')) {
            // New Registration(Not invite)
            case REFERER_STATUS_SIGNUP:
                return REFERER_STATUS_SIGNUP;

            // Invitation(exist goalous account)
            case REFERER_STATUS_INVITATION_EXIST:
                return REFERER_STATUS_INVITATION_EXIST;

            // Invitation(not exist goalous account)
            case REFERER_STATUS_INVITATION_NOT_EXIST:
                return REFERER_STATUS_INVITATION_NOT_EXIST;

            // Login
            case REFERER_STATUS_LOGIN:
                return REFERER_STATUS_LOGIN;

            // Others
            default:
                return REFERER_STATUS_DEFAULT;
        }
    }
}
