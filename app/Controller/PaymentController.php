<?php

# /app/Controller/PaymentController.php

class PaymentController extends AppController
{
    public function choose()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('/Payment/choose_payment_type');
    }

    public function cannot_use_service()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
    }
}
