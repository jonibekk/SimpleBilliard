<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CirclePinService');

/**
 * Class CirclePinsController
 */
class CirclePinsController extends ApiController
{
    function get_pinned(){
        $CirclePinService = ClassRegistry::init('CirclePinService');
        $results = $this->CirclePinService->getPinned();
        return results;
    }

    function get_unpinned(){
        $CirclePinService = ClassRegistry::init('CirclePinService');
        $results = $this->CirclePinService->getUnpinned();
        return results;
    }
}
