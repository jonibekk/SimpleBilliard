<?php
App::uses('AppController', 'Controller');
App::import('Service', 'CirclePinService');
/**
 * Circles Members Controller
 *
 * @property CircleMember $CircleMember
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
     * index method
     *
     * @return void
     */
    public function index()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        try {
            $circles = ClassRegistry::init('CirclePinService')->getMyCircleSortedList($this->Auth->user('id'), $this->current_team_id);
            $defaultCircle = $circles['default_circle'];
            $regularCircles = $circles['regular_circle'];
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }

        $this->set('defaultCircle', $defaultCircle);
        $this->set('circles', $regularCircles);
    }
}
