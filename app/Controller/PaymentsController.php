<?php
App::import('Service', 'TeamService');
App::uses('PaymentSetting', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PaymentsController
 *
 * @property PaymentSetting $PaymentSetting
 */
class PaymentsController extends AppController
{
    public $uses = [
        'ChargeHistory',
        'PaymentSetting',
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin();
        $this->layout = LAYOUT_ONE_COLUMN;
    }

    /**
     * Display billing information
     */
    public function index()
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");

        $teamId = $this->current_team_id;
        $payment = $PaymentService->get($teamId);
        $chargeMemberCount = $this->Team->TeamMember->countChargeTargetUsers($teamId);
        if (empty($payment)) {
            App::uses('LangHelper', 'View/Helper');
            $Lang = new LangHelper(new View());
            $userCountryCode = $Lang->getUserCountryCode();
            $amountPerUser = $PaymentService->getAmountPerUser($this->current_team_id);
            $currencyType = $userCountryCode == 'JP' ? Enum\PaymentSetting\Currency::JPY : Enum\PaymentSetting\Currency::USD;
            $subTotal = $PaymentService->formatCharge($amountPerUser * $chargeMemberCount, $currencyType);
            $amountPerUser = $PaymentService->formatCharge($amountPerUser, $currencyType);
        } else {
            $chargeInfo = $PaymentService->calcRelatedTotalChargeByUserCnt($teamId, $chargeMemberCount,
                $payment);
            $subTotal = $PaymentService->formatCharge($chargeInfo['sub_total_charge'], $payment['currency']);
            $amountPerUser = $PaymentService->formatCharge($payment['amount_per_user'], $payment['currency']);
        }
        $serviceUseStatus = $TeamService->getServiceUseStatus();
        $this->set(compact(
            'payment',
            'chargeMemberCount',
            'serviceUseStatus',
            'chargeInfo',
            'subTotal',
            'amountPerUser'
        ));
    }

    /**
     * Display billing information
     */
    public function method()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        // Check if paid plan
        if (!$this->Team->isPaidPlan($this->current_team_id)) {
            // Redirect to payment page
            return $this->redirect('/payments');
        }

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        $paymentType = $PaymentService->getPaymentType($this->current_team_id);

        // Credit Card payment
        if ($paymentType == Enum\PaymentSetting\Type::CREDIT_CARD) {
            return $this->_creditCard();
        }

        // Invoice
        return $this->_invoice();
    }

    private function _invoice()
    {
        // Payment data
        $paymentSettings = $this->PaymentSetting->getInvoiceByTeamId($this->current_team_id);
        $invoice = Hash::get($paymentSettings, 'Invoice');

        $this->set(compact('invoice'));

        return $this->render('method_invoice');
    }

    private function _creditCard()
    {
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init('CreditCardService');

        // Get customer id
        $paymentSettings = $this->PaymentSetting->getCcByTeamId($this->current_team_id);
        $customerCode = Hash::get($paymentSettings, 'CreditCard.customer_code');

        // Get card data from API
        $creditCard = $CreditCardService->retrieveCreditCard($customerCode);
        if ($creditCard['error'] === true) {
            CakeLog::error("Error retrieving credit card for customerCode: $customerCode");
            return $this->redirect('/payments');
        }
        $creditCard = $creditCard['creditCard'];

        // Get Credit card info
        $brand = $creditCard->brand;
        $lastDigits = $creditCard->last4;
        $expYear = $creditCard->exp_year;
        $expMonth = $creditCard->exp_month;
        $expMonthName = date('F', mktime(0, 0, 0, $expMonth, 10, $expYear));

        // Check if the card is expired
        $dateNow = GoalousDateTime::now();
        $expDate = $CreditCardService->getRealExpireDateTimeFromCreditCardExpireDate($expYear, $expMonth);
        $isExpired = $dateNow->greaterThanOrEqualTo($expDate);

        $this->set(compact('brand', 'expMonth', 'expYear', 'lastDigits', 'expMonthName', 'isExpired'));
        return $this->render('method_cc');
    }

    /**
     * Register paid plan(SPA)
     *
     * @param null $step
     *
     * @return \Cake\Network\Response|null
     */
    public function apply($step = null)
    {
        // Deny direct access
        if (!empty($step)) {
            return $this->redirect('/payments/apply');
        }

        // Check if not already paid plan
        $teamId = $this->current_team_id;
        if ($this->Team->isPaidPlan($teamId)) {
            $this->Notification->outError(__("You have already registered the paid plan."));
            return $this->redirect('/');
        }
    }

    /**
     * @return \Cake\Network\Response|null
     */
    public function history()
    {
        // Check if already paid plan
        $teamId = $this->current_team_id;
        if (!$this->Team->isPaidPlan($teamId)) {
            $this->Notification->outError(__("You have no permission."));
            return $this->redirect('/');
        }
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $histories = Hash::extract($this->ChargeHistory->find('all'), '{n}.ChargeHistory');
        $paymentSetting = $PaymentService->get($this->current_team_id);
        foreach ($histories as &$v) {
            $v['total'] = $PaymentService->formatCharge($v['total_amount'] + $v['tax'], $paymentSetting['currency']);
        }
        $this->set(compact('histories'));
    }

    public function cannot_use_service()
    {
        $this->render('cannot_use_service');
    }

    public function update_cc_info()
    {
        $this->render('update_credit_card');
    }

    public function contact_settings()
    {
        // Check if paid plan
        if (!$this->Team->isPaidPlan($this->current_team_id)) {
            // Redirect to payment page
            return $this->redirect('/payments');
        }

        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        $setting = $PaymentSetting->getByTeamId($this->current_team_id);

        // Get country list
        $countries = Configure::read("countries");
        $countries = array_map(function($tag) {
            return array(
                'name' => $tag['name'],
                'value' => $tag['code']
            );
        }, $countries);

        $this->set(compact('setting', 'countries'));

        return $this->render('contact_settings');
    }
}
