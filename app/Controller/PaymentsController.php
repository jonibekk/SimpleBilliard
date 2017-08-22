<?php
App::import('Service', 'TeamService');

class PaymentsController extends AppController {

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin();
    }

    /**
     * Display billing information
     */
    public function index(){
        $TeamService = ClassRegistry::init("TeamService");
        $this->set('teamMemberCount', count($this->Team->TeamMember->getAllMemberUserIdList(true, true, true)));
        $this->set('serviceUseStatus', $TeamService->getServiceUseStatus());
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('index');
    }

    /**
     * Register paid plan(SPA)
     * @param null $step
     *
     * @return \Cake\Network\Response|null
     */
    public function apply($step = null) {
        // Deny direct access
        if (!empty($step)) {
            return $this->redirect('/payments/apply');
        }

        // Check if is admin
        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');
        if (!$this->Team->TeamMember->isActiveAdmin($userId, $teamId)) {
            $this->Notification->outError(__("You have no permission."));
            return $this->redirect('/');
        }

        // Check if not already paid plan
        if ($this->Team->isPaidPlan($teamId)) {
            $this->Notification->outError(__("You have already registered the paid plan."));
            return $this->redirect('/');
        }

        $this->layout = LAYOUT_ONE_COLUMN;
    }

    public function enter_cc_info() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('credit_card_entry');
    }

     public function enter_company_info() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('company_info');
    }

    public function thank_you() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('thank_you');
    }

    public function history() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('payment_history');
    }

    public function cannot_use_service()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('cannot_use_service');
    }

    public function update_cc_info() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('update_credit_card');
    }
}
