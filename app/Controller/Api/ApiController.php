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
        ],
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
        parent::beforeFilter();
        $this->_setupAuth();
        $this->autoRender = false;
        //htmlを出力してしまうためdebugを無効化
        Configure::write('debug', 0);
        if (!$this->request->is('ajax')) {
//            throw new BadRequestException(__('Ajax Only!'),400);
        }
        if (!$this->Auth->user()) {
            throw new BadRequestException(__('You should be logged in.'), 401);
        }
        $this->_setAppLanguage();
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
        $this->_getResponse(200, $data, $html);
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
        $this->_getResponse(400, null, null, $message, $validationErrors);
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
        return $this->_getResponseBadFail(__('Validation failed.'),
            $this->_validationExtract($Model->validationErrors));
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
     * バリデーションメッセージの展開
     * key:valueの形にして1フィールド1メッセージにする
     *
     * @param $validationErrors
     *
     * @return array
     */
    function _validationExtract($validationErrors)
    {
        $res = [];
        if (empty($validationErrors)) {
            return $res;
        }
        if ($validationErrors === true) {
            return $res;
        }
        foreach ($validationErrors as $k => $v) {
            $res[$k] = $v[0];
        }
        return $res;
    }

}
