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

    /**
     * Opens the Circle Edit Modal if User is admin of the circle
     *
     * @return CakeResponse
     */
    public function ajax_get_edit_modal()
    {
        $circleId = $this->request->params['named']['circle_id'];
        if(!isset($circleId)) {
            return $this->_getResponseBadFail();
        }
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        if(!$CircleMember->isAdmin($this->Auth->user('id'), $circleId)){
            return $this->_getResponseBadFail();
        }
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        $this->_ajaxPreProcess();
        $this->request->data = $Circle->findById($circleId);
        $this->request->data['Circle']['members'] = null;

        $circleMembers = $CircleMember->getMembers($circleId, true);
        $this->set('circle_members', $circleMembers);
        //htmlレンダリング結果
        $response = $this->render('modal_edit_circle');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }
}
