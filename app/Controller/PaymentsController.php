<?php
App::import('Service', 'TeamService');

class PaymentsController extends AppController {
    public $uses = array('Teams');
    public function index(){
        $TeamService = ClassRegistry::init("TeamService");
        $this->set('teamMemberCount', count($this->Team->TeamMember->getAllMemberUserIdList(true, true, true)));
        $this->set('serviceUseStatus', $TeamService->getServiceUseStatus());
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('index');
    }

    public function apply() {
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
