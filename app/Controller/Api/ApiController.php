<?php
/**
 * Api level Controller
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:05
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */
App::uses('BaseController', 'Controller');

class ApiController extends BaseController
{
    /**
     * AppControllerを分割した場合、子クラスでComponent,Helper,Modelがマージされないため、
     * 中間Controllerでは以下を利用。末端Controllerは通常のCakeの規定通り
     */
    private $merge_components = [
        'Security' => [
            'csrfUseOnce'  => false,
            'csrfExpires'  => '+24 hour',
            'validatePost' => false,
        ]
    ];
    private $merge_helpers = [];
    private $merge_uses = [];

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);

        $this->uses = am($this->uses, $this->merge_uses);
        $this->components = am($this->components, $this->merge_components);
        $this->helpers = am($this->helpers, $this->merge_helpers);

        Configure::write('Exception.renderer', 'ApiExceptionRenderer');
        Configure::write('Exception.log', false);
    }

    function beforeFilter()
    {
        CustomLogger::getInstance()->logEvent('UELO:ApiController:beforeFilter');

        parent::beforeFilter();
        $this->_setHeader();
        $this->_setupAuth();
        $this->autoRender = false;
        //htmlを出力してしまうためdebugを無効化
        Configure::write('debug', 0);
        if (!$this->request->is('ajax')) {
//            throw new BadRequestException(__('Ajax Only!'),400);
        }
        if (!$this->Auth->user()) {
            throw new BadRequestException(__('Please log in.'), 401);
        }

        // when prohibit request in read only
        if ($this->_isProhibitedRequestByReadOnly()) {
            $this->stopInvoke = true;
            return $this->_getResponseBadFail(__("You may only read your team’s pages."));
        }
        // when prohibit request in status of cannot use service
        if ($this->_isProhibitedRequestByCannotUseService()) {
            $this->stopInvoke = true;
            return $this->_getResponseBadFail(__("You cannot use service on the team."));
        }

        $this->_setAppLanguage();
        $this->set('my_id', $this->Auth->user('id'));
        $this->set('my_team_id', $this->current_team_id);
    }

    /**
     * Set header
     * Disable cache
     * Ref: https://stackoverflow.com/questions/13640109/how-to-prevent-browser-cache-for-php-site
     */
    private function _setHeader()
    {
        header("Cache-Control: no-store, no-cache, max-age=0");
        header("Pragma: no-cache");
    }

    /**
     * This is wrapper parent invokeAction
     * - it can make execution stop until before render
     *
     * @param CakeRequest $request
     *
     * @return void
     */
    public function invokeAction(CakeRequest $request)
    {
        if ($this->stopInvoke) {
            return false;
        }
        return parent::invokeAction($request);
    }

    /**
     * 成功(Status Code:200)のレスポンスを返す
     *
     * @param string|null $data
     * @param string|null $html
     *
     * @return CakeResponse
     */
    protected function _getResponseSuccess($data = null, $html = null)
    {
        return $this->_getResponse(200, $data, $html);
    }

    /**
     * 成功(Status Code:200)のdataキーが無いシンプルバージョン
     *
     * @param $ret
     *
     * @return CakeResponse|null
     */
    protected function _getResponseSuccessSimple($ret = null)
    {
        $this->response->type('json');
        $this->response->body(json_encode($ret));
        $this->response->statusCode(200);
        return $this->response;
    }

    /**
     * 成功(Status Code:200)のページングデータ取得用レスポンスを返す
     *
     * @param null $ret
     *
     * @return CakeResponse
     */
    protected function _getResponsePagingSuccess($ret = null)
    {
        $this->response->type('json');
        $this->response->body(json_encode($ret));
        $this->response->statusCode(200);
        return $this->response;
    }

    /**
     * リクエスト不正(Status Code:400)のレスポンスを返す
     *
     * @param string     $message
     * @param array|null $validationErrors
     *
     * @return CakeResponse
     */
    protected function _getResponseBadFail($message, $validationErrors = null)
    {
        return $this->_getResponse(400, null, null, $message, $validationErrors);
    }

    /**
     * アクセス権限無しのレスポンスを返す
     *
     * @param string $message
     *
     * @return CakeResponse
     */
    protected function _getResponseForbidden($message = null)
    {
        if (empty($message)) {
            $message = __("You don't have a permission.");
        }
        return $this->_getResponse(403, null, null, $message);
    }

    /**
     * NotFound(404)のレスポンスを返す
     *
     * @param string $message
     *
     * @return CakeResponse
     */
    protected function _getResponseNotFound($message = null)
    {
        if (empty($message)) {
            $message = __("Ooops, Not Found.");
        }
        return $this->_getResponse(404, null, null, $message);
    }

    /**
     * リソースの衝突
     * 既にリソースが更新されている
     *
     * @return CakeResponse
     */
    protected function _getResponseConflict($message = null)
    {
        if (empty($message)) {
            $message = __("Error, data conflicted.");
        }
        return $this->_getResponse(409, null, null, $message);
    }

    /**
     * InternalServerError(500)のレスポンスを返す
     *
     * @param string $message
     *
     * @return CakeResponse
     */
    protected function _getResponseInternalServerError($message = null)
    {
        if (empty($message)) {
            $message = __("Server error occurred. We apologize for the inconvenience. Please try again.");
        }
        return $this->_getResponse(500, null, null, $message);
    }

    /**
     * 通常のバリデーション結果をレスポンスとして返す
     * - バリデーション成功の場合はStatus Code:200
     * - バリデーション失敗の場合はStatus Code:400
     *
     * @param Model $Model
     *
     * @return CakeResponse
     */
    protected function _getResponseDefaultValidation(Model $Model)
    {
        $Model->set($this->request->data);
        if ($Model->validates()) {
            return $this->_getResponseSuccess();
        }
        // TODO: _validationExtractがService基底クラスに移行されたらここの呼び出し元も変える
        return $this->_getResponseBadFail(__('Validation failed.'),
            $Model->_validationExtract($Model->validationErrors));
    }

    /**
     * バリデーションエラー(Status Code:400)をレスポンスとして返す
     *
     * @param array $validationMsg
     *
     * @return CakeResponse
     */
    protected function _getResponseValidationFail($validationMsg)
    {
        return $this->_getResponseBadFail(__('Validation failed.'), $validationMsg);
    }

    function _requireRequestData()
    {
        //csrfトークンは邪魔なので削除
        unset($this->request->data['_Token']['key']);
        if (empty($this->request->data)) {
            throw new BadRequestException(__('No Data'));
        }
        return true;
    }

    /**
     * レスポンス汎用メソッド
     *
     * @param integer           $status_code
     * @param array|string|null $data
     * @param string|null       $html
     * @param string|null       $message
     * @param array|null        $validation_errors
     *
     * @return CakeResponse
     */
    protected function _getResponse(
        $status_code,
        $data = null,
        $html = null,
        $message = null,
        $validation_errors = null
    ) {
        $ret = [];
        if ($data !== null) {
            $ret['data'] = $data;
        }
        if ($html !== null) {
            $ret['html'] = $html;
        }
        if ($message !== null) {
            $ret['message'] = $message;
        }
        if ($validation_errors !== null) {
            $ret['validation_errors'] = $validation_errors;
        }
        $this->response->type('json');
        $this->response->body(json_encode($ret));
        $this->response->statusCode($status_code);
        return $this->response;
    }

    /**
     * リクエストパラメータにidを含める事を強制する
     * 例 /visions/123/test この場合の123がid
     *
     * @param null $id
     *
     * @return bool
     */
    public function _requiredId($id = null)
    {
        if (!$id) {
            $id = $this->request->param('id');
        }
        if (!$id) {
            throw new NotFoundException();
        }
        return true;
    }

    /**
     * Setup Authentication Component
     *
     * @return void
     */
    protected function _setupAuth()
    {
        $this->Auth->loginRedirect = null;
        $this->Auth->logoutRedirect = null;
        $this->Auth->loginAction = null;
    }

    /**
     * Check permission if team administrator
     * [How to use]
     * ・Check in all action methods
     * 　Not set argument.
     * 　e.g. `$this->_checkAdmin();`
     * ・Check in specified action methods
     * 　set argument as array.
     * 　e.g.
     *    check method: index, create
     *    not check method: update
     *    `$this->_checkAdmin(['index', 'create']);`
     *
     * @param array $actionMethods
     *
     * @return CakeResponse
     */
    protected function _checkAdmin(array $actionMethods = [])
    {
        if (!$this->_isAdmin($actionMethods)) {
            $this->stopInvoke = true;
            return $this->_getResponseForbidden();
        }
    }
}
