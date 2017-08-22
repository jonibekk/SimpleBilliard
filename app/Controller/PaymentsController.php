<?php
App::import('Service', 'TeamService');
App::uses('PaymentSetting', 'Model');

class PaymentsController extends AppController
{
    public $uses = [
        'ChargeHistory'
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
        $TeamService = ClassRegistry::init("TeamService");
        $this->set('teamMemberCount', count($this->Team->TeamMember->getAllMemberUserIdList(true, true, true)));
        $this->set('serviceUseStatus', $TeamService->getServiceUseStatus());
        $this->render('index');
    }

    /**
     * Display billing information
     */
    public function method()
    {
        // TODO.Payment:Change view dynamically and must delete
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
