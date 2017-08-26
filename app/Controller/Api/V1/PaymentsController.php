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
                'contact_person_last_name',
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
                'contact_person_last_name',
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

        // Check if not already paid plan
        if ($this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden(__("You have already registered the paid plan."));
        }

        // Validate Data
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validation = $PaymentService->validateCreateCc($requestData);

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
        $requestData['payment_base_day'] = date('d', strtotime(AppUtil::todayDateYmdLocal($timezone)));
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
        // Set teamId and payment type for validation
        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');
        $requestData = Hash::insert($this->request->data, 'team_id', $teamId);
        $requestData = Hash::insert($requestData, 'type', PaymentSetting::PAYMENT_TYPE_INVOICE);

        // Check if not already paid plan
        if ($this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden(__("You have already registered the paid plan."));
        }

        // Validate input
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $validationFields = Hash::get($this->validationFieldsEachPage, 'company');
        $data = array('payment_setting' => $this->request->data);
        $validationErrors = $PaymentService->validateSave($data, $validationFields);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }

        // Check if the country is Japan
        if ($requestData['company_country'] != 'JP') {
            // TODO.Payment: Add translation for message
            return $this->_getResponseBadFail(__("Invoice payment are available for Japan only"));
        }

        // Register invoice
        // Invoices for only Japanese team. So, $timezone will be always Japan time.
        $timezone = 9;
        $requestData['payment_base_day'] = date('d', strtotime(AppUtil::todayDateYmdLocal($timezone)));
        $requestData['currency'] = PaymentSetting::CURRENCY_TYPE_JPY;
        $requestData['type'] = PaymentSetting::PAYMENT_TYPE_INVOICE;

        $regResponse = $PaymentService->registerInvoicePayment($userId, $teamId, $requestData);
        if ($regResponse !== true) {
            return $this->_getResponse($regResponse['errorCode'], null, null, $regResponse['message']);
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
            $companyCountry = $this->request->query('company_country');
            $amountPerUser = $PaymentService->getDefaultAmountPerUserByCountry($companyCountry);
            $currencyType = $PaymentService->getCurrencyTypeByCountry($companyCountry);
            // Calc charge user count
            $chargeUserCnt = $TeamMember->countChargeTargetUsers();
            $paymentSetting = [
                'currency'        => $currencyType,
                'amount_per_user' => $amountPerUser,
                'company_country' => $companyCountry
            ];
            $chargeInfo = $PaymentService->calcRelatedTotalChargeByUserCnt($this->current_team_id, $chargeUserCnt,
                $paymentSetting);
            $res = am($res, [
                'amount_per_user'    => $PaymentService->formatCharge($amountPerUser, $currencyType),
                'charge_users_count' => $chargeUserCnt,
                'sub_total_charge'   => $PaymentService->formatCharge($chargeInfo['sub_total_charge'], $currencyType),
                'tax'                => $PaymentService->formatCharge($chargeInfo['tax'], $currencyType),
                'total_charge'       => $PaymentService->formatCharge($chargeInfo['total_charge'], $currencyType),
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

        $data = $this->request->data;
        if ($page === 'company') {
            $companyCountry = Hash::get($data, 'payment_setting.company_country');
            if (empty($companyCountry)) {
                return $this->_getResponseBadFail(__("Invalid Request"));
            }
            if ($companyCountry === 'JP') {
                $validationFields['PaymentSetting'] = am(
                    $validationFields['PaymentSetting'],
                    [
                        'contact_person_last_name_kana',
                        'contact_person_first_name_kana',
                    ]
                );
            }
        } elseif ($page === 'invoice') {
            $validationFields['Invoice'] = am(
                $validationFields['Invoice'],
                [
                    'contact_person_last_name_kana',
                    'contact_person_first_name_kana',
                ]
            );
        }

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
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
        $result = $PaymentService->updatePayerInfo($teamId, $userId, $this->request->data);
        if ($result !== true) {
            if (empty($result['errorCode'])) {
                return $this->_getResponseValidationFail($result);
            } else {
                return $this->_getResponse($result['errorCode'], null, null, $result['message']);
            }
        }
        return $this->_getResponseSuccess();
    }

    /**
     * Update invoice info
     * Endpoint: /api/v1/payments/{$teamId}/invoice
     *
     * @param int $teamId
     *
     * @return CakeResponse
     */
    function put_invoice(int $teamId)
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

        $result = $PaymentService->updateInvoice($teamId, $this->request->data);
        if ($result !== true) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }

    /**
     * Update credit card info
     * Endpoint: /api/v1/payments/{$teamId}/credit_card
     *
     * @param int $teamId
     *
     * @return CakeResponse
     */
    function put_credit_card(int $teamId)
    {
        if ($teamId != $this->current_team_id) {
            return $this->_getResponseNotFound();
        }

        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $token = Hash::get($this->request->data, 'token');

        // Validation
        $customerCode = $CreditCard->getCustomerCode($teamId);
        if (empty($customerCode)) {
            return $this->_getResponseNotFound();
        }

        // Update
        $updateResult = $CreditCardService->update($customerCode, $token);
        if ($updateResult['error'] === true) {
            $this->_getResponseBadFail($updateResult['message']);
        }

        return $this->_getResponseSuccess();
    }
}
