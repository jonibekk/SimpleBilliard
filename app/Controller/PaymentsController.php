<?php
# /app/Controller/PaymentsController.php

class PaymentsController extends AppController {
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
        $this->render('/Payment/company_info');
    }
    public function thankyou() {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/thank_you');
    }

    public function cannot_use_service()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/cannot_use_service');
    }
}
