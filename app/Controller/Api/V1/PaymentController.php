<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiPaymentService');
App::import('Service', 'PaymentService');

class PaymentController extends ApiController
{
    /**
     * Create a new payment register
     *
     * Parameters:
     *
     * Payment.token            Stripe Credit Card Token
     * Payment.team_id          Team ID
     * Payment.type             Payment Type (0 = Invoice, 1 = Credit Card)
     * Payment.amount_per_user  Amount Per user
     * Payment.payer_name       Credit Card Name or Invoice Payer
     * Payment.company_name     Company Name
     * Payment.company_address  Company Address
     * Payment.company_tel      Company Telephone number
     * Payment.email            Payer email address
     * Payment.payment_base_day Payment day every month
     * Payment.currency         Currency (1 = JPY, 2 = USD)
     *
     * @return CakeResponse
     */
    function post()
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validation = $PaymentService->validateCreate(Hash::get($this->request->data, 'Payment'));

        if ($validation !== true) {
            return $this->_getResponseValidationFail($validation);
        }

        $paymentType = Hash::get($this->request->data, 'Payment.type');
        // Register Credit Card
        if ($paymentType == PaymentSetting::PAYMENT_TYPE_CREDIT_CARD) {
            /** @var ApiPaymentService $ApiPaymentService */
            $ApiPaymentService = ClassRegistry::init("ApiPaymentService");
            $token = Hash::get($this->request->data, 'Payment.token');
            $email = Hash::get($this->request->data, 'Payment.email');
            $companyName = Hash::get($this->request->data, 'Payment.company_name');

            // Register customer at Stripe
            $stripeResponse = $ApiPaymentService->registerCustomer($token, $email, $companyName);
            if ($stripeResponse['error'] === true) {
                return $this->_getResponseBadFail($stripeResponse['message']);
            }

            // Stripe customer id
            $customerID = $stripeResponse['customer_id'];
            if (empty($customerID)) {
                // It never should happen
                return $this->_getResponseBadFail(__("An error occurred while processing."));
            }

            // Check if the Payment if in the correct currency
            $currency = Hash::get($this->request->data, 'Payment.currency');
            if ($stripeResponse['card']['country'] == 'JP' && $currency !== PaymentSetting::CURRENCY_JPY) {
                // Delete customer from Stripe
                $ApiPaymentService->deleteCustomer($customerID);

                // TODO: Add translation for message
                return $this->_getResponseBadFail("Your Credit Card does not match your country settings");
            }

            // Register Payment on database
            $res = ($PaymentService->createCreditCardPayment(Hash::get($this->request->data, 'Payment'), $customerID));
            if ($res) {
                return $this->_getResponseSuccess();
            }
        }

        // TODO: Register Invoice
        return $this->_getResponseSuccess();
    }
}
