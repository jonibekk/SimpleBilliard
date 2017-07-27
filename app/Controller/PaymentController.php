<?php
# /app/Controller/PaymentController.php

class PaymentController extends AppController {
    public function apply() {
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