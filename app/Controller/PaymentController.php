<?php
# /app/Controller/PaymentController.php

class PaymentController extends AppController {
    public $uses = array("PaymentSetting");
    public function apply() {
        $countryList = $this->PaymentSetting->findByName('countryCode');
        $this->set('countryList', $countryList);
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/choose_payment_type');
    }

    public function enterCCInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/credit_card_entry');
    }

    public function enterCompanyInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/credit_card_entry');
    }
}