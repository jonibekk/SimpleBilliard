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
        // TODO.Payment:Change view dynamically and must delete

        /** @var PaymentSetting $PaymentSetting */
        $paymentSettings = $this->PaymentSetting->getByTeamId($this->current_team_id);

        // Invoice
        if ($paymentSettings['type'] == PaymentSetting::PAYMENT_TYPE_INVOICE) {
            return $this->_invoice();
        }

        // start
        $type = $this->request->query('type');
        if (empty($type)) {
            return $this->render('method_cc');
        }
        return $this->render('method_' . $type);
        // end

        // TODO.Payment: release comment out.
//        $this->render('method');
    }

    private function _invoice()
    {
        // Payment data
        $paymentSettings = $this->PaymentSetting->getInvoiceByTeamId($this->current_team_id);
        $invoice = Hash::get($paymentSettings, 'Invoice')[0];

        $this->set(compact('invoice'));

        return $this->render('method_invoice');
    }

    private function _creditCard()
    {
        $paymentSettings = $this->PaymentSetting->getCcByTeamId($this->current_team_id);
        $creditCard = Hash::get($paymentSettings, 'CreditCard')[0];

        $this->set(compact('creditCard'));

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
        // TODO.Payment: Check access permission
        $this->render('update_credit_card');
    }
}
