<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiStripeService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentController
 */
class PaymentController extends ApiController
{
    /**
     * Create a new payment register
     *
     * Endpoint: /api/v1/payment/creditCard
     *
     * Parameters:
     *
     * token            Stripe Credit Card Token
     * amount_per_user  Amount Per user
     * payer_name       Credit Card Name or Invoice Payer
     * company_name     Company Name
     * company_address  Company Address
     * company_tel      Company Telephone number
     * email            Payer email address
     * payment_base_day Payment day every month
     * currency         Currency (1 = JPY, 2 = USD)
     *
     * @return CakeResponse
     */
    function post_creditCard()
    {
        // Set teamId and payment type for validation
        $teamId = $this->current_team_id;
        $requestData = Hash::insert($this->request->data, 'team_id', $teamId);
        $requestData = Hash::insert($requestData, 'type', PaymentSetting::PAYMENT_TYPE_CREDIT_CARD);

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validation = $PaymentService->validateCreate($requestData);

        if ($validation !== true) {
            return $this->_getResponseValidationFail($validation);
        }

        // Register Credit Card
        /** @var ApiStripeService $ApiStripeService */
        $ApiStripeService = ClassRegistry::init("ApiStripeService");
        $token = Hash::get($requestData, 'token');
        $email = Hash::get($requestData, 'email');
        // Set description as "Team ID: 2" to identify it on Stripe Dashboard
        $description = "Team ID: $teamId";

        // Register customer at Stripe
        $stripeResponse = $ApiStripeService->registerCustomer($token, $email, $description);
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
        $currency = Hash::get($requestData, 'currency');
        if ($stripeResponse['card']['country'] == 'JP' && $currency != PaymentSetting::CURRENCY_JPY) {
            // Delete customer from Stripe
            $ApiStripeService->deleteCustomer($customerID);

            // TODO: Add translation for message
            return $this->_getResponseBadFail("Your Credit Card does not match your country settings");
        }

        // Register Payment on database
        $userId = $this->Auth->user('id');
        $res = ($PaymentService->createCreditCardPayment($requestData, $customerID, $userId));
        if (!$res) {
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // New Payment registered with sucess
        return $this->_getResponseSuccess();
    }
}
