<?php
App::uses('AppController', 'Controller');
App::import('Service', 'CirclePinService');

/**
 * Circle Pins Controller
 */
class CirclePinsController extends AppController
{
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Security->enabled = true;
    }

    function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init('CirclePinService');

        try {
            $defaultCircle = $CirclePinService->getDefaultCircle();
            $pinnedCircles = $CirclePinService->getPinned();
            $unpinnedCircles = $CirclePinService->getUnpinned();
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->set('defaultCircle', $defaultCircle);
        $this->set('pinnedCircles', $pinnedCircles);
        $this->set('unpinnedCircles', $unpinnedCircles);
    }

}
