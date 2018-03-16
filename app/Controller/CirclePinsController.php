<?php
App::uses('AppController', 'Controller');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
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

    /**
     * Opens the circle edit modal if the user_id is circle's admin
     * 
     * usage: POST:/api/v1/circle_pins/
     *
     * @return CakeResponse
     */
    public function ajax_get_edit_modal()
    {
        $circle_id = $this->request->params['named']['circle_id'];
        if(!isset($circle_id)) {
            return null;
        }
        $CircleMember = ClassRegistry::init('CircleMember');
        if(!$CircleMember->isAdmin($this->Auth->user('id'), $circle_id)){
            return null;
        }
        $this->_ajaxPreProcess();
        $this->request->data = ClassRegistry::init('Circle')->findById($circle_id);
        $this->request->data['Circle']['members'] = null;

        $circle_members = $CircleMember->getMembers($circle_id, true);
        $this->set('circle_members', $circle_members);
        //htmlレンダリング結果
        $response = $this->render('modal_edit_circle');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }
}
