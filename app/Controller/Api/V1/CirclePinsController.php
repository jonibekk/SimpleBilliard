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
     * usage: PUT:/api/v1/circle_pins/
     *
     * @return CakeResponse
     */
    public function post()
    {
        if(!isset($this->request->data)) {
            return $this->_getResponseBadFail("No Data");
        }

        if(!array_key_exists('csv', $this->request->data)) {
            //TODO:fix call procedure use PUT instedad jsut for testing
            $this->circle_edit_ajax($this->request->data);
        }

        if (!ClassRegistry::init("CirclePinService")->setCircleOrders($this->Auth->user('id'), $this->current_team_id, $this->request->data['csv'])) {
            return $this->_getResponseInternalServerError("Failed updating circle pin information");
        } else {
            //TODO:hamburger update => ClassRegistry::init('AppController')->_setMyCircle();
        }
    }

    /**
     * Opens the circle edit modal if the user_id is circle's admin
     * 
     * usage: POST:/api/v1/circle_pins/
     *
     * @return CakeResponse
     */
    public function circle_edit_ajax($data)
    {
        if(!isset($data)) {
            return $this->_getResponseBadFail("No Data");
        }

        if(!array_key_exists('circle_id', $data)) {
            return $this->_getResponseBadFail("Missing Data");
        }

        return ClassRegistry::init("CirclePinService")->isUserCircleAdmin($this->Auth->user('id'), $data['circle_id']);
    }
}
