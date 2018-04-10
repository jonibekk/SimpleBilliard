<?php
App::uses('ApiController', 'Controller/Api');
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
     * usage: POST:/api/v1/circle_pins/
     *
     * @return CakeResponse
     */
    public function post()
    {
        if(!isset($this->request->data)) {
            return $this->_getResponseBadFail();
        }

        if(!array_key_exists('pin_order', $this->request->data)) {
            return $this->_getResponseBadFail();
        }

        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init("CirclePinService");

        if($CirclePinService->validateApprovalPinOrder($this->request->data['pin_order']) !== true) {
            return $this->_getResponseBadFail();
        }
      
        if (!$CirclePinService->setCircleOrders($this->Auth->user('id'), $this->current_team_id, $this->request->data['pin_order'])) {
            return $this->_getResponseInternalServerError();
        }
    }
}
