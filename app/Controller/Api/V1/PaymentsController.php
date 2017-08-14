<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentsController
 */
class PaymentsController extends ApiController
{
    // Need validation fields for validation API of changing to paid plan
    private $validationFieldsEachPage = [
        'country' => [
            'PaymentSetting' => [
                'company_country',
                'type'
            ],
        ],
        'company' => [
            'PaymentSetting' => [
                'company_name',
                'company_country',
                'company_post_code',
                'company_region',
                'company_city',
                'company_street',
                'company_tel',
                'contact_person_first_name',
                'contact_person_first_name_kana',
                'contact_person_last_name',
                'contact_person_last_name_kana',
                'contact_person_tel',
                'contact_person_email',
            ]
        ],
        'invoice' => [
            'PaymentSetting' => [
                'company_name',
                'company_country',
                'company_post_code',
                'company_region',
                'company_city',
                'company_street',
                'company_tel',
                'contact_person_first_name',
                'contact_person_first_name_kana',
                'contact_person_last_name',
                'contact_person_last_name_kana',
                'contact_person_tel',
                'contact_person_email',
            ],
            'Invoice' => [
                // TODO
            ]
        ],
    ];

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
        if ($creditCardData['creditCard']->country == 'JP' && $currency != PaymentSetting::CURRENCY_TYPE_JPY) {
            // TODO: Add translation for message
            return $this->_getResponseBadFail(__("Your Credit Card does not match your country settings"));
        }

        // Register Credit Card to stripe
        // Set description as "Team ID: 2" to identify it on Stripe Dashboard
        $contactEmail = Hash::get($requestData, 'contact_person_email');
        $description = "Team ID: $teamId";
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

        // Use nested transaction for guarantee of rollback in case of transaction failure
        /** @var AppModel $AppModel */
        $AppModel = ClassRegistry::init('AppModel');
        $AppModel->begin();

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
        try {
            $this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_PAID, $paymentDate);
        }
        catch (Exception $e) {
            // Remove the customer from Stripe
            $CreditCardService->deleteCustomer($customerId);
            $this->log("Stripe Customer: $customerId deleted due registration error.");

            $AppModel->rollback();
            $this->log("Error updating team id: $teamId status");
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

            return $this->_getResponseBadFail(__("An error occurred while processing."));
        }

        // Apply charge to customer
        $membersCount = count($TeamMember->getTeamMemberListByStatus(TeamMember::USER_STATUS_ACTIVE));
        $currencySymbol = $currency != PaymentSetting::CURRENCY_TYPE_JPY ? '¥' : '$';
        $amountPerUser = $currencySymbol . Hash::get($requestData,'amount_per_user');
        $paymentDescription = "Team: $teamId Unit: $amountPerUser Users: $membersCount";
        $chargeResult = $PaymentService->applyCreditCardCharge($teamId,
            PaymentSetting::CHARGE_TYPE_MONTHLY_FEE, $membersCount, $paymentDescription);

        // Error on charging the customer
        // This is error due problem on Stripe API or database
        // Log Customer ID and Team for later investigation
        if ($chargeResult['error'] === true) {

            if ($chargeResult['success'] === false) {
                // Customer was not charged so, rollback this transaction
                $AppModel->rollback();
                $CreditCardService->deleteCustomer($customerId);
            }
            $this->log("Error an payment transaction. CustomerID: $customerId, TeamId: $teamId");
            $this->log("RequestData: $requestData");
            return $this->_getResponse($chargeResult['errorCode'], null, null, $chargeResult['message']);
        }

        // Commit Update status and charge transactions
        // Do not matter if apply charge fail. We still want to have user be able to use
        $AppModel->commit();

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
     * @return CakeResponse
     * @internal     param int|null $id
     */
    function get_init_form()
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

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

        if ($dataTypes == 'all' || in_array('charge', $dataTypes)) {
            // Get payment setting by team id
            $paymentSetting = $PaymentService->get($this->current_team_id);
            $amountPerUser = $PaymentService->formatCharge($paymentSetting['amount_per_user']);
            // Calc charge user count
            $chargeUserCnt = $TeamMember->countChargeTargetUsers();
            // Calc total charge
            $totalCharge = $PaymentService->formatCharge($paymentSetting['amount_per_user'] * $chargeUserCnt);
            $res = am($res, [
                'amount_per_user' => $amountPerUser,
                'charge_users_count' => $chargeUserCnt,
                'total_charge' => $totalCharge,
            ]);
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
        $page = $this->request->query('page');
        // Required page parameter
        if (empty($page)) {
            return $this->_getResponseBadFail(__("Invalid Request"));
        }

        $validationFields = Hash::get($this->validationFieldsEachPage, $page);
        if (empty($validationFields)) {
            return $this->_getResponseBadFail(__("Invalid Request"));
        }

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $data = $this->request->data;
        $validationErrors = $PaymentService->validateSave($data, $validationFields);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }
        return $this->_getResponseSuccess();
    }

}
