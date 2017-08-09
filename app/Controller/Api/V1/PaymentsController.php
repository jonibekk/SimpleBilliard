<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentsController
 */
class PaymentsController extends ApiController
{
    /**
     * Create a new payment register
     * Endpoint: /api/v1/payments/credit_card
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
        if ($stripeResponse['card']['country'] == 'JP' && $currency != PaymentSetting::CURRENCY_CODE_JPY) {
            // Delete customer from Stripe
            $CreditCardService->deleteCustomer($customerId);

            // TODO: Add translation for message
            return $this->_getResponseBadFail("Your Credit Card does not match your country settings");
        }

        // Register Payment on database
        $userId = $this->Auth->user('id');
        $res = ($PaymentService->registerCreditCardPayment($requestData, $customerId, $userId));
        if (!$res) {
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Apply charge to customer
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $membersCount = count($TeamMember->getTeamMemberListByStatus(TeamMember::USER_STATUS_ACTIVE));

        // Apply charge
        $chargeResult = $PaymentService->applyCreditCardCharge($teamId, PaymentSetting::CHARGE_TYPE_MONTHLY_FEE,
            $membersCount, "Payment for team: $teamId");

        // Error on charging the customer
        if ($chargeResult['error'] === true) {
            return $this->_getResponse($chargeResult['errorCode'], null, null, $chargeResult['message']);
        }

        // Charging transaction succeed but payment fail. It can be on cause of fraud or credit transfer.
        if ($chargeResult['success'] === false) {
            return $this->_getResponseBadFail($chargeResult['status']);
        }

        // New Payment registered with success
        return $this->_getResponseSuccess();
    }

    /**
     * Update credit card info
     * Endpoint: /api/v1/payments/credit_card
     * method: PUT
     *
     * @return void
     */
    function put_credit_card()
    {
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $token = Hash::get($this->request->data, 'token');
        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');

        // Validation
        $customerCode = $CreditCardService->getCustomerCode($teamId);
        if (empty($customerCode)) {
            return $this->_getResponseNotFound();
        }
        if (!$TeamMember->isActiveAdmin($userId, $teamId)) {
            return $this->_getResponseForbidden();
        }

        // Update
        $updateResult = $CreditCardService->update($customerCode, $token);
        if ($updateResult !== true) {
            $this->_getResponseBadFail($updateResult);
        }

        return $this->_getResponseSuccess();
    }

}
