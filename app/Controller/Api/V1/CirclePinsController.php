<?php
App::uses('ApiController', 'Controller/Api');
App::uses('AppController', 'Controller');
App::import('Service', 'CirclePinService');
/**
 * Class CirclePinsController
 */
class CirclePinsController extends ApiController
{
    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Update all pin orders relevant to the user using csv.
     * 
     * usage: /api/v1/circle
     *
     * @return CakeResponse
     */
    public function post()
    {
        if(!isset($this->request->data)) {
            return $this->_getResponseBadFail("No Data");
        }

        if(!array_key_exists('csv', $this->request->data)) {
            return $this->_getResponseBadFail("Missing Data");
        }

        if (!ClassRegistry::init("CirclePinService")->setCircleOrders($this->Auth->user('id'), $this->current_team_id, $this->request->data['csv'])) {
            return $this->_getResponseInternalServerError("Failed updating circle pin information");
        } else {
            //TODO:hamburger update => ClassRegistry::init('AppController')->_setMyCircle();
        }
    }
}
