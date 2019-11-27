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
App::import('Service', 'PaymentService');
App::import('Service', 'UserService');
App::import('Service', 'CampaignService');
App::import('Service', 'PostDraftService');
App::import('Model', 'PostDraft');

/**
 * Static content controller
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link          http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 * @property User           $User
 * @property TermsOfService TermsOfService
 * @noinspection  PhpInconsistentReturnPointsInspection
 */
class PagesController extends AppController
{
    public $uses = ['TermsOfService'];
    public $components = ['Mention'];
    // TODO: delete all old lp pages and processing related after we migrated lp (goalous-lp repo) for all envs
    private $newLpEnvs = ['stg', 'isao', 'www'];

    public function beforeFilter()
    {
        $this->_setLanguage();
        //全ページ許可
        $this->Auth->allow();

        //チームidがあった場合は許可しない
        if (isset($this->request->params['team_id'])) {
            $this->Auth->deny('display');
        }

        // Pass login status to view
        $this->set('isLoggedIn', $this->_isLoggedIn());

        //切り換え可能な言語をセット
        $this->set('lang_list', $this->_getPageLanguageList());
        parent::beforeFilter();
    }

    /**
     * Displays a view
     *
     * @return $this->redirect('/') or void
     * @throws Exception
     * @throws MissingViewException
     * @throws NotFoundException
     * @internal param \What $mixed page to display
     */
    public function home()
    {
        // Display lp top page if not logged in
        if (!$this->_isLoggedIn()) {
            // TODO: delete all old lp pages and processing related after we migrated lp (goalous-lp repo) for all envs
            if (in_array(ENV_NAME, $this->newLpEnvs, true)) {
                return $this->redirectLp();
            } else {
                $this->layout = LAYOUT_HOMEPAGE;
                return $this->render('home');
            }
        }

        // Define URL params for Google analytics.
        $this->_setUrlParams();

        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));
        $this->_setTopAllContentIfLoggedIn();

        /**
         * Browser back cache clear
         * @see https://jira.goalous.com/browse/GL-8610
         */
        // For HTTP/1.1 conforming clients and the rest (MSIE 5)
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        // For HTTP/1.0 conforming clients
        header('Pragma: no-cache');

        return $this->render('logged_in_home');
    }

    /**
     * Get lang code
     * @return string
     */
    private function redirectLp(string $page = "")
    {
        App::uses('LangHelper', 'View/Helper');
        $Lang = new LangHelper(new View());
        $lang = $Lang->getLangCode();
        $url = "/intl/${lang}/";
        if (!empty($page)) {
          $url .= $page.'/';
        }
        $this->redirect($url);
    }

    /**
     * Display lp pages other than top page
     *
     * @return void
     */
    public function lp()
    {
        $path = func_get_args();
        $page = $path[0];
        // Redirect new LP env `/intl/**`
        if (in_array(ENV_NAME, $this->newLpEnvs, true)) {
            return $this->redirectLp($page);
        }

        // Old processing.
        // TODO: delete all old lp pages and processing related after we migrated lp (goalous-lp repo) for all envs
        if ($page === 'pricing') {
            $this->_setPricingValues();
        } elseif ($page === 'terms') {
            $this->_setTerms();
        } elseif ($page === 'campaign_terms') {
            /** @var CampaignService $CampaignService */
            $CampaignService = ClassRegistry::init('CampaignService');
            if (!$this->_isLoggedIn() ||
                !$CampaignService->isCampaignTeam($this->current_team_id)) {
                throw new NotFoundException();
            }
        } elseif ($page === 'case_study') {
            $company = $this->request->query('company') ?? 'witone';
            $this->set('company', $company);
        }

        $this->set('is_mb_app', $this->is_mb_app);
        $this->layout = LAYOUT_HOMEPAGE;
        return $this->render(implode('/', $path));
    }

    /**
     * Display lp pages other than top page
     *
     * @return void
     */
    private function _setTerms()
    {
        $terms = $this->TermsOfService->getCurrent();
        App::uses('LangHelper', 'View/Helper');
        $Lang = new LangHelper(new View());
        $lang = $Lang->getLangCode();

        if ($lang === $Lang::LANG_CODE_JP) {
            $this->set('terms', $terms['text_ja']);
        } else {
            $this->set('terms', (empty($terms['text_en']) ? $terms['text_ja'] : $terms['text_en']));
        }
    }

    public function app_version_unsupported()
    {
        $path = func_get_args();

        // pages app_force_update / app_force_install
        // both can view on login/logout
        $this->layout = LAYOUT_NO_HEADER;
        return $this->render(implode('/', $path));
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
        $this->_setGoalsForTopAction();
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
        if ($form_type = Hash::get($this->request->params, 'common_form_type')) {
            $this->set('common_form_type', $form_type);
        } else {
            $this->set('common_form_type', 'action');
        }
        $this->set('enable_translation', true);

        try {
            $paramsPostGet = $this->request->params;
            $this->set([
                'posts' => $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
                    $paramsPostGet)
            ]);
        } catch (RuntimeException $e) {
            GoalousLog::error("Error in showing home page.", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->Auth->user('id'),
                'team_id' => $current_team['Team']['id']
            ]);
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        /** @var PostDraftService $PostDraftService */
        $PostDraftService = ClassRegistry::init('PostDraftService');
        $this->set('post_drafts',
            $PostDraftService->getPostDraftForFeed($this->Auth->user('id'), $current_team['Team']['id']));
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
        } elseif (isset($this->request->params['named']['lang'])) {
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
        if (in_array(ENV_NAME, $this->newLpEnvs, true)) {
            return $this->redirectLp('contact');
        }

        // Old processing.
        // TODO: delete all old lp pages and processing related after we migrated lp (goalous-lp repo) for all envs
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
            $this->Notification->outError(__('Ooops. Some problems occurred.'));
            return $this->redirect($this->referer());
        }
        $this->set(compact('data'));
        return $this->render();
    }

    public function contact_send()
    {
        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Notification->outError(__('Ooops. Some problems occurred.'));
            return $this->redirect($this->referer());
        }
        $this->Session->delete('contact_form_data');
        //メール送信処理
        App::uses('CakeEmail', 'Network/Email');
        if (ENV_NAME === "local") {
            $config = 'default';
        } else {
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
        return $this->redirect('/contact_thanks');
    }

    public function _setUrlParams()
    {
        $parsed_referer_url = Router::parse($this->referer('/', true));
        $request_status = $this->params['url'];
        $status_from_referer = $this->_defineStatusFromReferer();

        // When parametes separated from google analitics already exists,
        // ignore redirect for google analitics.
        $reserved_params = ['notify_id', 'common_form', 'team_id', 'from'];
        foreach ($reserved_params as $param) {
            if (Hash::get($this->request->params, $param) || Hash::get($this->request->params, "named.$param")) {
                return true;
            }
        }

        if ($this->_parseParameter($request_status) !== $status_from_referer) {
            return $this->redirect("${status_from_referer}");
        }
        $this->Session->delete('referer_status');
        return true;
    }

    public function _defineStatusFromReferer()
    {
        switch ($this->Session->read('referer_status')) {
            case REFERER_STATUS_SIGNUP_WITH_INVITING:
                return REFERER_STATUS_SIGNUP_WITH_INVITING;

            case REFERER_STATUS_SIGNUP_WITH_NOT_INVITING:
                return REFERER_STATUS_SIGNUP_WITH_NOT_INVITING;

            case REFERER_STATUS_INVITED_USER_EXIST:
                return REFERER_STATUS_INVITED_USER_EXIST;

            case REFERER_STATUS_INVITED_USER_EXIST_BY_EMAIL:
                return REFERER_STATUS_INVITED_USER_EXIST_BY_EMAIL;

            case REFERER_STATUS_INVITED_USER_EXIST_BY_CSV:
                return REFERER_STATUS_INVITED_USER_EXIST_BY_CSV;

            case REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_EMAIL:
                return REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_EMAIL;

            case REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_CSV:
                return REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_CSV;

            case REFERER_STATUS_LOGIN:
                return REFERER_STATUS_LOGIN;

            // Others
            default:
                return REFERER_STATUS_DEFAULT;
        }
    }

    public function _parseParameter($parameters)
    {
        $parameters_text = '';
        $prefix = '';
        $i = 0;
        foreach ($parameters as $key => $value) {
            if ($i === 0) {
                $prefix = '?';
            } else {
                $prefix = '&';
            }
            $parameters_text .= "${prefix}${key}=${value}";
            $i++;
        }
        return $parameters_text;
    }

    /**
     * set view amount per user
     *
     * @return void
     */
    function _setPricingValues()
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        App::uses('LangHelper', 'View/Helper');
        $Lang = new LangHelper(new View());
        $userCountryCode = $Lang->getUserCountryCode();
        $userCurrency = $PaymentService->getCurrencyTypeByCountry($userCountryCode);

        if (!$this->_isLoggedIn()) {
            $amountPerUser = $PaymentService->getDefaultAmountPerUserByCountry($userCountryCode);
            $price = $PaymentService->formatCharge($amountPerUser, $userCurrency);
            $this->set(compact('price'));
            return;
        }

        $teamId = $this->current_team_id;
        $isLoggedIn = true;
        $isPaidPlan = false;
        $payment = $PaymentService->get($teamId);
        if ($payment) {
            $isPaidPlan = true;
            $amountPerUser = $payment['amount_per_user'];
            $currency = $payment['currency'];
            $price = $PaymentService->formatCharge($amountPerUser, $currency);
        } else {
            $amountPerUser = $PaymentService->getAmountPerUserBeforePayment($teamId, $userCountryCode);
            $price = $PaymentService->formatCharge($amountPerUser, $userCurrency);
        }
        $this->set(compact('price', 'isLoggedIn', 'isPaidPlan'));
    }

}
