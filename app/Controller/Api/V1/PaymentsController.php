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
        $userId = $this->Auth->user('id');
        $requestData = Hash::insert($this->request->data, 'team_id', $teamId);
        $requestData = Hash::insert($requestData, 'type', PaymentSetting::PAYMENT_TYPE_CREDIT_CARD);

        // Check if is admin
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        if (!$TeamMember->isActiveAdmin($userId, $teamId)) {
            return $this->_getResponseForbidden();
        }

        // Validate Data
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validation = $PaymentService->validateCreate($requestData);

        if ($validation !== true) {
            return $this->_getResponseValidationFail($validation);
        }

        // Check if the Payment if in the correct currency
        $token = Hash::get($requestData, 'token');
        $currency = Hash::get($requestData, 'currency');
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        $creditCardData = $CreditCardService->retrieveToken($token);
        if ($creditCardData['creditCard']->country == 'JP' && $currency != PaymentSetting::CURRENCY_CODE_JPY) {
            // TODO: Add translation for message
            return $this->_getResponseBadFail("Your Credit Card does not match your country settings");
        }

        // Register Credit Card
        $contactEmail = Hash::get($requestData, 'contact_person_email');
        // Set description as "Team ID: 2" to identify it on Stripe Dashboard
        $description = "Team ID: $teamId";

        // Register customer at Stripe
        $stripeResponse = $CreditCardService->registerCustomer($token, $contactEmail, $description);
        if ($stripeResponse['error'] === true) {
            return $this->_getResponseBadFail($stripeResponse['message']);
        }

        // Stripe customer id
        $customerId = $stripeResponse['customer_id'];
        if (empty($customerId)) {
            // It never should happen
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Register Payment on database
        $userId = $this->Auth->user('id');
        $res = ($PaymentService->registerCreditCardPayment($requestData, $customerId, $userId));
        if (!$res) {
            // Remove the customer from Stripe
            $CreditCardService->deleteCustomer($customerId);
            $this->log("Stripe Customer: $customerId deleted due registration error.");
            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Set team status
        // Up to this point any failure do not directly affect user accounts or charge its credit card.
        // Team status will be set first in case of any failure team will be able to continue to use.
        $teamData = $this->Team->getCurrentTeam();
        $paymentDate = date('Y-m-d');
        $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_PAID, $paymentDate);

        // Apply charge to customer
        $membersCount = count($TeamMember->getTeamMemberListByStatus(TeamMember::USER_STATUS_ACTIVE));
        $currencySymbol = $currency != PaymentSetting::CURRENCY_CODE_JPY ? '¥' : '$';
        $amountPerUser = $currencySymbol . Hash::get($requestData,'amount_per_user');
        $paymentDescription = "Team: $teamId Unit: $amountPerUser Users: $membersCount";
        $chargeResult = $PaymentService->applyCreditCardCharge($teamId,
            PaymentSetting::CHARGE_TYPE_MONTHLY_FEE, $membersCount, $paymentDescription);

        // Error on charging the customer
        // This is error due problem on Stripe API or database
        // Log Customer ID and Team for later investigation
        if ($chargeResult['error'] === true) {
            $this->log("Error an payment transaction. CustomerID: $customerId, TeamId: $teamId");
            $this->log("RequestData: $requestData");
            return $this->_getResponse($chargeResult['errorCode'], null, null, $chargeResult['message']);
        }

        // Charging transaction succeed but payment fail. It can be on cause of fraud or credit transfer.
        if ($chargeResult['success'] === false) {
            // Set team back to previous status
            $this->Team->updateAllServiceUseStateStartEndDate(Hash::get($teamData, 'service_use_status'),
                Hash::get($teamData, 'service_use_state_start_date'));

            // At this point the payment setting is set but the card is not chargeable
            // the user have to enter an new credit card on the update card screen.

            return $this->_getResponseBadFail($chargeResult['status']);
        }

        // New Payment registered with success
        return $this->_getResponseSuccess();
    }


    /**
     * Get information for display form
     *
     * @query_params bool data_types `all` is returning all data_types, it can be selected individually(e.g. `countries,lang_code`)
     *
     * @param integer|null $id
     *
     * @return CakeResponse
     */
    function get_init_form()
    {
        $res = [];

        if ($this->request->query('data_types')) {
            $dataTypes = explode(',', $this->request->query('data_types'));
            if (in_array('all', $dataTypes)) {
                $dataTypes = 'all';
            }
        } else {
            $dataTypes = 'all';
        }

        if ($dataTypes == 'all' || in_array('countries', $dataTypes)) {
            $countries = Configure::read("countries");
            $res['countries'] = Hash::combine($countries, '{n}.code', '{n}.name');
        }

        if ($dataTypes == 'all' || in_array('lang_code', $dataTypes)) {
            App::uses('LangHelper', 'View/Helper');
            $LangHelper = new LangHelper(new View());
            $res['lang_code'] = $LangHelper->getLangCode();
        }
        return $this->_getResponseSuccess($res);
    }


    /**
     * Validation API
     *
     * @query_param fields
     * @return CakeResponse
     */
    function post_validate()
    {
        // TODO:implemnet
        return $this->_getResponseSuccess();
    }

}
