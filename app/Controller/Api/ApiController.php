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
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->autoRender = false;
//        if(!$this->request->is('ajax')) {
//            throw new BadRequestException();
//        }
    }

}
