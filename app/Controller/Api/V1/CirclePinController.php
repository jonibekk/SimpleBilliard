<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CirclePinService');

/**
 * Class ActionsController
 */
class CirclePinController extends ApiController
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
     * Update all pin orders relevant to the user.
     * 
     * usage: /api/v1/circlepin/update
     *
     * @return CakeResponse
     */
    function post()
    {
        try
        {
            $jsonString = $this->request->data['json'];
            if(!isset($jsonString)){
                return false;
            }
            
            $jsonArray = json_decode($jsonString, true);
            if(!isset($jsonArray)){
                return false;
            }

            /** @var CirclePinService $CirclePinService */
            $CirclePinService = ClassRegistry::init("CirclePinService");

            if (!$CirclePinService->bulkInsert($this->Auth('user_id'), $this->Session->read('current_team_id'), $jsonArray)) {
                return $this->_getResponseInternalServerError();
            }
        }
        catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return $this->_getResponseSuccessSimple();
    }

    function Auth(){
        return true;
    }
}
