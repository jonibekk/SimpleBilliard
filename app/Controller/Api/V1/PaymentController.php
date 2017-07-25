<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentController
 */
class PaymentController extends ApiController
{
    /**
     * Create a new payment register
     * Endpoint: /api/v1/payment/credit_card
     * Parameters:
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
    function post_credit_card()
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
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        $token = Hash::get($requestData, 'token');
        $email = Hash::get($requestData, 'email');
        // Set description as "Team ID: 2" to identify it on Stripe Dashboard
        $description = "Team ID: $teamId";

        // Register customer at Stripe
        $stripeResponse = $CreditCardService->registerCustomer($token, $email, $description);
        if ($stripeResponse['error'] === true) {
            return $this->_getResponseBadFail($stripeResponse['message']);
        }

        // Stripe customer id
        $customerId = $stripeResponse['customer_id'];
        if (empty($customerId)) {
            // It never should happen
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Check if the Payment if in the correct currency
        // The checking of credit card token is made after the customer registration
        // and a deletion in case of customer do not match the country/currency requirements
        // because Stripe token can only be used once.
        // On this case its better to have a pre check of token by the frontend.
        $currency = Hash::get($requestData, 'currency');
        if ($stripeResponse['card']['country'] == 'JP' && $currency != PaymentSetting::CURRENCY_JPY) {
            // Delete customer from Stripe
            $CreditCardService->deleteCustomer($customerId);

            // TODO: Add translation for message
            return $this->_getResponseBadFail("Your Credit Card does not match your country settings");
        }

        // Register Payment on database
        $userId = $this->Auth->user('id');
        $res = ($PaymentService->createCreditCardPayment($requestData, $customerId, $userId));
        if (!$res) {
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Apply charge to customer
        $charge = $this->applyFirstCharge();

        // Error on charging the customer
        if ($charge['error'] === true) {
            return $this->_getResponseBadFail($charge['message']);
        }

        // Charging transaction succeed but payment fail. It can be on cause of fraud or credit transfer.
        if ($charge['success'] === false) {
            return $this->_getResponseBadFail($charge['status']);
        }

        // New Payment registered with success
        return $this->_getResponseSuccess();
    }

    /**
     * Charge the first payment
     *
     * @return array
     */
    private function applyFirstCharge()
    {
        $result = [
            'error'   => false,
            'message' => null
        ];

        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init('CreditCardService');

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $teamId = $this->current_team_id;
        $membersCount = count($TeamMember->getActiveTeamMembersList(false));

        // Get Payment settings
        $paymentSettings = $this->Team->PaymentSetting->getByTeamId($teamId);
        if (!$paymentSettings) {
            $result['error'] = true;
            $result['message'] = __('Payment settings does not exists.');

            return $result;
        }

        // Get credit card settings
        if (empty(Hash::get($paymentSettings, 'CreditCard')) || !isset($paymentSettings['CreditCard'][0])) {
            $result['error'] = true;
            $result['message'] = __('Credit card settings does not exists.');

            return $result;
        }
        $creditCard = $paymentSettings['CreditCard'][0];

        // Calculate value  (Number of Active users X Amount per user)
        $value = $membersCount * Hash::get($paymentSettings, 'PaymentSetting.amount_per_user');
        $customerId =  Hash::get($creditCard, 'customer_code');
        $currency = Hash::get($paymentSettings, 'PaymentSetting.currency') == PaymentSetting::CURRENCY_JPY ? 'JPY' : 'USD';


        // Apply the user charge on Stripe
        $charge = $CreditCardService->chargeCustomer($customerId, $currency, $value, 'Test charge');

        if ($charge['error'] === true) {
            $result['error'] = true;
            $result['message'] = $charge['message'];

            return $result;
        }

        // Customer charge processed
        $result['success'] = $charge['success'];
        $result['data'] = $charge['paymentDatas'];

        return $result;
    }
}
