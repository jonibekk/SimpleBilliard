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
        $this->autoRender = false;
//        if(!$this->request->is('ajax')) {
//            throw new BadRequestException();
//        }
    }

}
