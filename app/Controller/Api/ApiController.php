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
        if (!$this->request->is('ajax')) {
//            throw new BadRequestException('Ajax Only!',400);
        }
        if (!$this->Auth->user()) {
            throw new ForbiddenException('You should be logged in.');
        }
    }

    /**
     * @param      $status_code
     * @param null $data
     * @param null $html
     * @param null $message
     * @param null $validation_errors
     *
     * @return CakeResponse|null
     */
    public function _getResponse($status_code, $data = null, $html = null, $message = null, $validation_errors = null)
    {
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

}
