<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');

/**
 * Class ApiSavedPostService
 */
class ApiCirclePinService extends ApiService
{
    /**
     * @param $userId
     * Update all pin orders relevant to the user.
     * 
     * usage: /api/v1/circlepin/update
     *
     * @return CakeResponse
     */
    function update(string $jsonString)
    {
        $jsonDatas = json_decode($jsonString);
        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init("CirclePinService");

        if (!$CirclePinService->bulkInsert($this->Auth('user_id'), $jsonDatas)) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccessSimple();
    }

    function test(){
        return "Success test";
    }
}
