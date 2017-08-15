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

        // Register credit card, and apply payment
        $res = $PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $requestData);
        if ($res['error'] === true) {
            return $this->_getResponse($res['errorCode'], null, null, $res['message']);
        }

        // New Payment registered with success
        return $this->_getResponseSuccess();
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
