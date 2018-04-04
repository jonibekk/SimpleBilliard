<?php
App::uses('AppController', 'Controller');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
/**
 * Circle Pins Controller
 *
 * @property CirclePinsController $CirclePinsController
 */
class CirclePinsController extends AppController
{
    public $uses = [
        'CirclePin'
    ];

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
     * Circle Pin Edit page index method
     *
     * @return void
     */
    public function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init("CirclePinService");
        
        $circles = $CirclePinService->getMyCircleSortedList($this->Auth->user('id'), $this->current_team_id);
        $defaultCircle = $circles['default_circle'];
        $regularCircles = $circles['regular_circle'];

        $this->set('defaultCircle', $defaultCircle);
        $this->set('circles', $regularCircles);
    }
}
