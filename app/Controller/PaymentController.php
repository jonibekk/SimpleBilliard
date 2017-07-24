<?php
# /app/Controller/PaymentController.php

class PaymentController extends AppController {
    public function choosePayment() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/choose_payment_type');
    }

    public function enterCCInfo() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/enter_credit_card');
    }
}