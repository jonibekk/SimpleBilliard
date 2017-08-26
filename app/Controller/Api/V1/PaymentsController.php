<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');

/**
 * Class PaymentsController
 */
class PaymentsController extends ApiController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin();
    }

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
                'contact_person_first_name',
                'contact_person_first_name_kana',
                'contact_person_last_name',
                'contact_person_last_name_kana',
                'contact_person_tel',
                'contact_person_email',
            ]
        ],
        'invoice' => [
            'Invoice' => [
                'company_name',
                'company_post_code',
                'company_region',
                'company_city',
                'company_street',
                'contact_person_first_name',
                'contact_person_first_name_kana',
                'contact_person_last_name',
                'contact_person_last_name_kana',
                'contact_person_tel',
                'contact_person_email',
            ],
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

        // Check if not already paid plan
        if ($this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden(__("You have already registered the paid plan."));
        }

        // Validate Data
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validation = $PaymentService->validateCreate($requestData);

        if ($validation !== true) {
            return $this->_getResponseValidationFail($validation);
        }

        // Check if the Payment if in the correct currency
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");

        $token = Hash::get($requestData, 'token');
        $companyCountry = Hash::get($requestData, 'company_country');
        $creditCardData = $CreditCardService->retrieveToken($token);

        // Check to prevent illegal choice of dollar or yen
        $ccCountry = $creditCardData['creditCard']->country;
        if (!$PaymentService->checkIllegalChoiceCountry($ccCountry, $companyCountry)) {
            // TODO.Payment: Add translation for message
            return $this->_getResponseBadFail(__("Your Credit Card does not match your country settings"));
        }

        // Register credit card, and apply payment
        $timezone = $this->Team->getTimezone();
        $requestData['payment_base_day'] = date('d',strtotime(AppUtil::todayDateYmdLocal($timezone)));
        $res = $PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $requestData);
        if ($res['error'] === true) {
            return $this->_getResponse($res['errorCode'], null, null, $res['message']);
        }

        // New Payment registered with success
        return $this->_getResponseSuccess();
    }


    /**
     * Register invoice info
     * Endpoint: /api/v1/payments/invoice
     *
     * @return CakeResponse
     */
    function post_invoice()
    {
        // TODO.Payment: implement
        $this->_getResponseSuccess();
    }

    /**
     * Update credit card info
     * Endpoint: /api/v1/payments/udpate_credit_card
     *
     * @return CakeResponse
     */
    function post_update_credit_card()
    {
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $token = Hash::get($this->request->data, 'token');
        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');

        // Check if paid plan
        if (!$this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden();
        }

        // Validation
        $customerCode = $CreditCard->getCustomerCode($teamId);
        if (empty($customerCode)) {
            return $this->_getResponseNotFound();
        }
        if (!$TeamMember->isActiveAdmin($userId, $teamId)) {
            return $this->_getResponseForbidden();
        }

        // Update
        $updateResult = $CreditCardService->update($customerCode, $token);
        if ($updateResult['error'] === true) {
            $this->_getResponseBadFail($updateResult['message']);
        }

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
            $companyCountry = $this->request->query('company_country');
            $amountPerUser = $PaymentService->getDefaultAmountPerUserByCountry($companyCountry);
            $currencyType = $PaymentService->getCurrencyTypeByCountry($companyCountry);
            // Calc charge user count
            $chargeUserCnt = $TeamMember->countChargeTargetUsers();
            $paymentSetting = [
                'currency' => $currencyType,
                'amount_per_user' => $amountPerUser,
                'company_country' => $companyCountry
            ];
            $chargeInfo = $PaymentService->calcRelatedTotalChargeByUserCnt($this->current_team_id, $chargeUserCnt, $paymentSetting);
            $res = am($res, [
                'amount_per_user' => $PaymentService->formatCharge($amountPerUser, $currencyType),
                'charge_users_count' => $chargeUserCnt,
                'sub_total_charge' => $PaymentService->formatCharge($chargeInfo['sub_total_charge'], $currencyType),
                'tax' => $PaymentService->formatCharge($chargeInfo['tax'], $currencyType),
                'total_charge' => $PaymentService->formatCharge($chargeInfo['total_charge'], $currencyType),
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

    /**
     * Update Payer info
     *
     * @param int $teamId
     *
     * @return CakeResponse
     */
    function put_company_info(int $teamId)
    {
        if ($teamId != $this->current_team_id) {
            return $this->_getResponseNotFound();
        }

        // Check if paid plan
        if (!$this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden();
        }

        $userId = $this->Auth->user('id');

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        // Validate input
        $validationFields = Hash::get($this->validationFieldsEachPage, 'company');
        $data = array('payment_setting' => $this->request->data);
        $validationErrors = $PaymentService->validateSave($data, $validationFields);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }

        // Update payer info
        $result = $PaymentService->updatePayerInfo($teamId, $this->request->data);
        if ($result !== true) {
            return $this->_getResponseInternalServerError();
        }
        return $this->_getResponseSuccess();
    }

}
