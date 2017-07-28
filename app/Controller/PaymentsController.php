<?php
# /app/Controller/PaymentsController.php
App::uses('AppController', 'Controller');


class PaymentsController extends AppController {
    public $components = ['Lang'];
    public function apply() {
        if(Configure::read('Config.language')=='jpn'){
            $this->layout = LAYOUT_ONE_COLUMN;
            $this->render('/Payment/choose_payment_type');
        }else{
            $this->enterCompanyInfo();
        }
    }

    public function enterCompanyInfo() {
        $this->set('gLang',Configure::read('Config.language'));
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/company_info');
    }

    public function enterCCInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/credit_card_entry');
    }

    public function thankyou() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/thank_you');
    }
}