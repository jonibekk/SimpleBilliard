<?php
# /app/Controller/PaymentController.php

class PaymentController extends AppController {
    public function choose() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/choose_payment_type');
    }
}