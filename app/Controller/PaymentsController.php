<?php
# /app/Controller/PaymentsController.php

class PaymentsController extends AppController {
    public function apply() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('choose_payment_type');
    }

    public function enterCCInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('credit_card_entry');
    }

    public function enterCompanyInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('company_info');
    }
    public function thankyou() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('thank_you');
    }
}