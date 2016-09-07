<?php
/**
 * Application level Controller
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
    private $merge_components = [];
    private $merge_helpers = [];
    private $merge_uses = [];

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        $this->uses = am($this->uses, $this->merge_uses);
        $this->components = am($this->components, $this->merge_components);
        $this->helpers = am($this->helpers, $this->merge_helpers);
    }

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->_setupAuth();
        $this->autoRender = false;
        if (!$this->request->is('ajax')) {
//            throw new ApiException('ajax only!');
        }
        if (!$this->Auth->user()) {
            return $this->_getResponse(401, null, null, 'not authorized');
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
        if ($data) {
            $ret['data'] = $data;
        }
        if ($html) {
            $ret['html'] = $html;
        }
        if ($message) {
            $ret['message'] = $message;
        }
        if ($validation_errors) {
            $ret['validation_errors'] = $validation_errors;
        }
        $this->response->type('json');
        $this->response->body(json_encode($ret));
        $this->response->statusCode($status_code);
        return $this->response;
    }

    /**
     * Setup Authentication Component
     *
     * @return void
     */
    protected function _setupAuth()
    {
        $this->Auth->authenticate = [
            'Form2' => [
                'fields'    => [
                    'username' => 'email',
                    'password' => 'password'
                ],
                'userModel' => 'User',
                'scope'     => [
                    'User.active_flg'             => 1,
                    'PrimaryEmail.email_verified' => 1
                ],
                'recursive' => 0,
            ]
        ];
        $this->Auth->loginRedirect = null;
        $this->Auth->logoutRedirect = null;
        $this->Auth->loginAction = null;
    }

}
