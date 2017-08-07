<?php
# /app/Controller/PaymentsController.php

class PaymentsController extends AppController {
    public function index(){
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('index');
    }

    public function apply() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('choose_payment_type');
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

    public function pricing() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('pricing');
    }

    public function cannot_use_service()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }
}
