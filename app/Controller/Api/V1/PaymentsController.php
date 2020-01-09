<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');
App::import('Service', 'CampaignService');
App::uses('PaymentSetting', 'Model');

use Goalous\Enum as Enum;

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
        'country'        => [
            'PaymentSetting' => [
                'company_country',
                'type'
            ],
        ],
        'campaign'       => [
            'PricePlanPurchaseTeam' => [
                'price_plan_code'
            ]
        ],
        'company'        => [
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
        'invoice'        => [
            'Invoice' => [
                'company_name',
                'company_post_code',
                'company_region',
                'company_city',
                'company_street',
                'contact_person_first_name',
                'contact_person_last_name',
                'contact_person_last_name_kana',
                'contact_person_first_name_kana',
                'contact_person_tel',
                'contact_person_email',
            ],
        ],
        'update_company' => [
            'PaymentSetting' => [
                'company_name',
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
        $requestData = Hash::get($this->request->data, 'payment_setting');
        $requestData = Hash::insert($requestData, 'team_id', $teamId);
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
            return $this->_getResponseBadFail(__("Your Credit Card does not match your country settings"));
        }

        // Check valid campaign
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        if ($CampaignService->isCampaignTeam($teamId)) {
            $pricePlanCode = Hash::get($this->request->data, 'price_plan_purchase_team.price_plan_code');
            if (!$pricePlanCode || !$CampaignService->isAllowedPricePlan($teamId, $pricePlanCode, $companyCountry)) {
                return $this->_getResponseBadFail(__("Your selected campaign is not allowed."));
            }
            $requestData = Hash::insert($requestData, 'price_plan_code', $pricePlanCode);
        }

        // Register credit card, and apply payment
        $timezone = $this->Team->getTimezone();
        $requestData['payment_base_day'] = date('d', strtotime(AppUtil::todayDateYmdLocal($timezone)));
        $res = $PaymentService->registerCreditCardPaymentAndCharge($userId, $teamId, $token, $requestData);
        if ($res['error'] === true) {
            return $this->_getResponse($res['errorCode'], null, null, $res['message']);
        }

        // Send notification email
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailRegisterCreditCardPaidPlan($toUid, $teamId);
            }
        } else {
            CakeLog::error("This team have no admin: $teamId");
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

        // Check if not already paid plan
        if ($this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden(__("You have already registered the paid plan."));
        }

        // Validate input
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $requestData = $this->request->data;
        $validationErrors = $PaymentService->validateCreateInvoice($requestData);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }

        // Check if the country is Japan
        if (Hash::get($requestData, 'payment_setting.company_country') !== 'JP') {
            // TODO.Payment: Add translation for message
            return $this->_getResponseBadFail(__("Invoice payment are available for Japan only"));
        }

        // Check valid campaign
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        $pricePlanCode = null;
        if ($CampaignService->isCampaignTeam($teamId)) {
            $pricePlanCode = Hash::get($requestData, 'price_plan_purchase_team.price_plan_code');
            if (!$pricePlanCode || !$CampaignService->isAllowedPricePlan($teamId, $pricePlanCode, 'JP')) {
                // TODO.Payment: Add translation for message
                return $this->_getResponseBadFail(__("Your selected campaign is not allowed."));
            }
            $requestData = Hash::insert($requestData, 'price_plan_code', $pricePlanCode);
        }

        // Register invoice
        $paymentData = Hash::get($requestData, 'payment_setting');
        $invoiceData = Hash::get($requestData, 'invoice');
        $regResponse = $PaymentService->registerInvoicePayment($userId, $teamId, $paymentData, $invoiceData, false, $pricePlanCode);
        if ($regResponse !== true) {
            return $this->_getResponseInternalServerError();
        }

        // Send notification email
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailRegisterInvoicePaidPlan($toUid, $teamId);
            }
        } else {
            CakeLog::error("This team have no admin: $teamId");
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
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");

        $teamId = $this->current_team_id;
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
            $res['is_campaign_team'] = $CampaignService->isCampaignTeam($teamId);
            $res['charge_users_count'] = $TeamMember->countChargeTargetUsers($teamId);
        }

        if ($dataTypes == 'all' || in_array('lang_code', $dataTypes)) {
            App::uses('LangHelper', 'View/Helper');
            $LangHelper = new LangHelper(new View());
            $res['lang_code'] = $LangHelper->getLangCode();
        }

        if ($dataTypes == 'all' || in_array('charge', $dataTypes)) {
            // Get payment setting by team id
            $companyCountry = $this->request->query('company_country');
            $amountPerUser = $PaymentService->getAmountPerUserBeforePayment($teamId, $companyCountry);
            $currencyType = $PaymentService->getCurrencyTypeByCountry($companyCountry);
            // Calc charge user count
            $chargeUserCnt = $TeamMember->countChargeTargetUsers($teamId);
            $paymentSetting = [
                'currency'        => $currencyType,
                'amount_per_user' => $amountPerUser,
                'company_country' => $companyCountry
            ];
            $chargeInfo = $PaymentService->calcRelatedTotalChargeByUserCnt($teamId, $chargeUserCnt,
                $paymentSetting);
            $res = am($res, [
                'amount_per_user'    => $PaymentService->formatCharge($amountPerUser, $currencyType),
                'charge_users_count' => $chargeUserCnt,
                'sub_total_charge'   => $PaymentService->formatCharge($chargeInfo['sub_total_charge'], $currencyType),
                'tax'                => $PaymentService->formatCharge($chargeInfo['tax'], $currencyType),
                'total_charge'       => $PaymentService->formatCharge($chargeInfo['total_charge'], $currencyType),
            ]);
        }

        if ($dataTypes == 'all' || in_array('campaigns', $dataTypes)) {
            /** @var CampaignService $CampaignService */
            $CampaignService = ClassRegistry::init("CampaignService");
            $res['price_plans'] = $CampaignService->findList($teamId);
        }

        return $this->_getResponseSuccess($res);
    }

    /**
     * Get information for display form
     *
     * @query_params bool data_types `all` is returning all data_types, it can be selected individually(e.g. `countries,lang_code`)
     * @return CakeResponse
     * @internal     param int|null $id
     */
    function get_upgrade_plan()
    {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $teamId = $this->current_team_id;
        $res = [];

        // Get current campaign plan
        $currentPricePlan = $CampaignService->getTeamPricePlan($teamId);
        if (empty($currentPricePlan)) {
            return $this->_getResponseForbidden();
        }

        // Get campaign plan list
        $res['price_plans'] = $CampaignService->findPlansForUpgrading($teamId, $currentPricePlan);
        $res['charge_users_count'] = $TeamMember->countHeadCount($teamId);
        $res['current_price_plan_code'] = $currentPricePlan['code'];
        return $this->_getResponseSuccess($res);
    }

    /**
     * Upgrade price plan
     *
     * @return CakeResponse
     */
    function post_upgrade_plan()
    {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        $teamId = $this->current_team_id;

        $pricePlanCode = $this->request->data('plan_code');
        // Validate
        $validateRes = $this->_validateUpgradePlan($teamId, $pricePlanCode);
        if ($validateRes !== true) {
            return $validateRes;
        }

        // Upgrade plan
        $userId = $this->Auth->user('id');
        if(!$CampaignService->upgradePlan($teamId, $pricePlanCode, $userId)) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }

    /**
     * Validation for upgrading plan
     *
     * @param int $teamId
     * @param     $upgradePlanCode No type hinting because of validation
     *
     * @return bool|CakeResponse
     */
    private function _validateUpgradePlan(int $teamId, $upgradePlanCode)
    {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init("PricePlanPurchaseTeam");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $validationError = $CampaignService->validateUpgradePlan($upgradePlanCode);
        if (!empty($validationError)) {
            return $this->_getResponseValidationFail($validationError);
        }

        // Check if plan purchased
        $purchasedTeam = $CampaignService->getPricePlanPurchaseTeam($teamId);
        $currentPlanCode = Hash::get($purchasedTeam, 'PricePlanPurchaseTeam.price_plan_code');
        if (empty($purchasedTeam)) {
            return $this->_getResponseForbidden();
        }

        // Check if exist upgrading plan
        $currentPlan = $CampaignService->getPlanByCode($currentPlanCode);
        $upgradePlan = $CampaignService->getPlanByCode($upgradePlanCode);
        if (empty($upgradePlan) || empty($currentPlan)) {
            return $this->_getResponseForbidden();
        }

        // Check if price plan group of upgrading plan equals the group of current plan
        if ($upgradePlan['group_id'] != $currentPlan['group_id']) {
            return $this->_getResponseForbidden();
        }

        // Check if not downgrade
        if ($upgradePlan['max_members'] <= $currentPlan['max_members']) {
            return $this->_getResponseForbidden();
        }

        // Check if charge members don't over max members of upgrading plan
        $chargeMemberCount = $TeamMember->countHeadCount($teamId);
        if ($chargeMemberCount > $upgradePlan['max_members']) {
            return $this->_getResponseForbidden();
        }

        return true;
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
            $paymentType = Hash::get($data, 'payment_setting.type');
            if (!AppUtil::isInt($paymentType)) {
                return $this->_getResponseBadFail(__("Invalid Request"));
            }
            if ((int)$paymentType === Enum\Model\PaymentSetting\Type::INVOICE) {
                $validationFields['PaymentSetting'] = am(
                    $validationFields['PaymentSetting'],
                    [
                        'contact_person_last_name_kana',
                        'contact_person_first_name_kana',
                    ]
                );
            }
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
        $validationFields = Hash::get($this->validationFieldsEachPage, 'update_company');
        $data = array('payment_setting' => $this->request->data);

        $paymentSetting = $PaymentService->get($teamId);
        $paymentType = Hash::get($paymentSetting, 'type');
        if ((int)$paymentType === Enum\Model\PaymentSetting\Type::INVOICE) {
            $validationFields['PaymentSetting'] = am(
                $validationFields['PaymentSetting'],
                [
                    'contact_person_last_name_kana',
                    'contact_person_first_name_kana',
                ]
            );
        }
        $data['payment_setting']['type'] = $paymentType;
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

        // Check if the team is of invoice payment
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        if ($PaymentService->getPaymentType($teamId) != Enum\Model\PaymentSetting\Type::INVOICE) {
            return $this->_getResponseForbidden();
        }

        // Validate input
        $validationFields = Hash::get($this->validationFieldsEachPage, 'company');
        // Its an update that means the invoice country IS already JP
        // setting there to avoid creating another validation method.
        $this->request->data['company_country'] = 'JP';
        $data = array('payment_setting' => $this->request->data);
        $paymentSetting = $PaymentService->get($teamId);
        $paymentType = Hash::get($paymentSetting, 'type');
        if ((int)$paymentType === Enum\Model\PaymentSetting\Type::INVOICE) {
            $validationFields['PaymentSetting'] = am(
                $validationFields['PaymentSetting'],
                [
                    'contact_person_last_name_kana',
                    'contact_person_first_name_kana',
                ]
            );
        }
        $data['payment_setting']['type'] = $paymentType;

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

        // Check if paid plan
        if (!$this->Team->isPaidPlan($teamId)) {
            return $this->_getResponseForbidden();
        }

        // Check if the team is of Credit card payment
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        if ($PaymentService->getPaymentType($teamId) != Enum\Model\PaymentSetting\Type::CREDIT_CARD) {
            return $this->_getResponseForbidden();
        }

        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        $token = Hash::get($this->request->data, 'token');

        // Check if the Payment if in the correct currency
        $creditCardData = $CreditCardService->retrieveToken($token);
        $ccCountry = $creditCardData['creditCard']->country;
        $companyCountry = Hash::get($PaymentSetting->getByTeamId($teamId), 'company_country');
        if (!$PaymentService->checkIllegalChoiceCountry($ccCountry, $companyCountry)) {
            // TODO.Payment: Add translation for message
            return $this->_getResponseBadFail(__("Your Credit Card does not match your country settings"));
        }

        // Validation
        $customerCode = $CreditCard->getCustomerCode($teamId);
        if (empty($customerCode)) {
            return $this->_getResponseNotFound();
        }

        // Update
        $updateResult = $CreditCardService->updateCreditCard($customerCode, $token, $teamId);
        if ($updateResult['error'] === true) {
            return $this->_getResponseBadFail($updateResult['message']);
        }

        return $this->_getResponseSuccess();
    }
}
