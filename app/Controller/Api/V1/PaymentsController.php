<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentsController
 */
class PaymentsController extends ApiController
{
    private $validationFieldsEachPage = [
        'country' => [
            'PaymentSetting' => [
                'company_country',
                'payment_type'
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
