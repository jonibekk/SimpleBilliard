<?php
App::import('Service', 'PaymentService');
App::uses('AppUtil', 'Util');

use Goalous\Model\Enum as Enum;

/**
 * DetectInconsistentChargeShell
 * Detect these inconsistent charge history data
 * Ref: https://confluence.goalous.com/display/GOAL/Payments+Recovery#PaymentsRecovery-1.checkinglists(検知対象項目)
 *
 * @property TeamMember     $TeamMember
 * @property PaymentSetting $PaymentSetting
 * @property ChargeHistory  $ChargeHistory
 * @property CreditCard  $CreditCard
 * @property InvoiceHistory  $InvoiceHistory
 */
class DetectInconsistentChargeShell extends AppShell
{
    const INCONSISTENT_TYPE_CHARGE_USER_CNT = 0;
    const INCONSISTENT_TYPE_AMOUNT_SUB_TOTAL = 1;
    const INCONSISTENT_TYPE_AMOUNT_TAX = 2;
    const INCONSISTENT_TYPE_STRIPE_CHARGE_MISSING = 3;
    const INCONSISTENT_TYPE_STRIPE_AMOUNT = 4;
    const INCONSISTENT_TYPE_ATOBARAI_COM_CHARGE_MISSING = 5;
    const INCONSISTENT_TYPE_ATOBARAI_COM_AMOUNT = 6;

    public $uses = array(
        'TeamMember',
        'PaymentSetting',
        'ChargeHistory',
        'InvoiceHistory',
        'CreditCard',
    );

    public function startup()
    {
        parent::startup();
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'startDatetime' => [
                'short'   => 's',
                'help'    => 'This is start date for filtering charge history',
                'default' => null,
            ],
            'endDatetime'   => [
                'short'   => 'e',
                'help'    => 'This is end date for filtering charge history',
                'default' => null,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        // Get start/end timestamp from arguments for filtering charge history.
        list($startTimestamp, $endTimestamp) = $this->getStartEndTimestamp();

        // Get charge histories for the specified period(default: a day ago)
        $histories = $this->ChargeHistory->findByChargeDatetimeRange($startTimestamp, $endTimestamp);

        if (empty($histories)) {
            $this->logInfo("Target charge history doesn't exist");
            return;
        }

        $this->logInfo(sprintf('Target charge history count:%d',
            count($histories)));

        // Check related amount
        $checkPassedHistories = $this->checkAmountEachHistory($histories);

        if (empty($checkPassedHistories)) {
            return;
        }

        $stripeChargeCheckHistories = [];
        $atobaraiChargeCheckHistories = [];
        foreach ($checkPassedHistories as $history) {
            // Set data for checking amount registered external services(stripe and atobarai.com
            if ($history['payment_type'] == Enum\PaymentSetting\Type::CREDIT_CARD) {
                $stripeChargeCheckHistories[] = $history;
            } elseif ($history['charge_type'] == Enum\ChargeHistory\ChargeType::MONTHLY_FEE) {
                $atobaraiChargeCheckHistories[] = $history;
            }
        }

        $this->logInfo(sprintf("Target history count to be checked if history amount match stripe amount:%d", count($stripeChargeCheckHistories)));
        $this->logInfo(sprintf("Target history count to be checked if history amount match atobarai.com amount:%d", count($atobaraiChargeCheckHistories)));

        // Check if history amount match stripe amount
        if (!empty($stripeChargeCheckHistories)) {
            $this->checkStripeAmountEachHistory($stripeChargeCheckHistories, $startTimestamp, $endTimestamp);
        }

        // Check if history amount match atobarai.com amount
        if (!empty($stripeChargeCheckHistories)) {
            $this->checkAtobaraiComAmountEachHistory($atobaraiChargeCheckHistories, $startTimestamp, $endTimestamp);
        }
    }

    /**
     * Get start/end timestamp from arguments.
     * Default: 1 day ago
     * @return array
     */
    private function getStartEndTimestamp(): array
    {
        /* Initialize parameters */
        // Get start timestamp
        $startDatetime = $this->param('startDatetime');
        if (empty($startDatetime)) {
            $startDatetime = GoalousDateTime::now()->subDay(1)->format('Y-m-d 00:00:00');
        }
        $startTimestamp = strtotime($startDatetime);

        // Get end timestamp
        $endDatetime = $this->param('endDatetime');
        if (empty($endDatetime)) {
            $endDatetime = GoalousDateTime::now()->subDay(1)->format('Y-m-d 23:59:59');
        }
        $endTimestamp = strtotime($endDatetime);

        $this->logInfo(sprintf('start/end datetime for filtering charge history. %s',
            AppUtil::jsonOneLine(compact('startDatetime', 'endDatetime'))));

        return [
            $startTimestamp,
            $endTimestamp
        ];
    }

    /**
     * Check No1 & No2 in this check list
     * https://confluence.goalous.com/display/GOAL/Payments+Recovery#PaymentsRecovery-1.checkinglists(検知対象項目)
     * @param array $histories
     *
     * @return array
     */
    private function checkAmountEachHistory(array $histories) :array
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        $teamIds = array_unique(Hash::extract($histories, '{n}.team_id'));
        // Get target charge members count each teams for comparing max charge users each charge history
        $chargeTargetUsersCntEachTeam = $this->TeamMember->countChargeTargetUsersEachTeam($teamIds);
        // Get payment settings.
        $paySettingsEachTeam = Hash::combine($this->PaymentSetting->findAllByTeamId($teamIds), '{n}.PaymentSetting.team_id', '{n}.PaymentSetting');

        $errors = [];
        $stripeChargeCheckHistories = [];
        foreach ($histories as $history) {
            // Check if max_charge_users don't over current charge users count
            $currentChargeTargetUsersCnt = $chargeTargetUsersCntEachTeam[$history['team_id']];
            if ($history['max_charge_users'] > $currentChargeTargetUsersCnt) {
                $errors[self::INCONSISTENT_TYPE_CHARGE_USER_CNT][] = [
                    'history_id'                  => $history['id'],
                    'team_id'                     => $history['team_id'],
                    'history_max_charge_users'    => $history['max_charge_users'],
                    'current_target_charge_users' => $currentChargeTargetUsersCnt,
                ];
                continue;
            }

            // Check if sub total amount
            $subTotal = $PaymentService->processDecimalPointForAmount($history['currency'],
                $history['amount_per_user'] * $history['charge_users']);

            $taxCompareRes = bccomp($history['total_amount'], $subTotal, 2);
            if ((int)$history['charge_type'] == Enum\ChargeHistory\ChargeType::MONTHLY_FEE) {
                if ($taxCompareRes !== 0) {
                    $errors[self::INCONSISTENT_TYPE_AMOUNT_SUB_TOTAL][] = [
                        'history_id'        => $history['id'],
                        'team_id'           => $history['team_id'],
                        'history_sub_total' => $history['total_amount'],
                        'compare_sub_total' => $subTotal,
                    ];
                    continue;
                }
            } else {
                // It is impossible to check amount strictly in case of pay by the day.
                // So we compare if only history's sub total doesn't over max sub total
                if ($taxCompareRes === 1) {
                    $errors[self::INCONSISTENT_TYPE_AMOUNT_SUB_TOTAL][] = [
                        'history_id'        => $history['id'],
                        'team_id'           => $history['team_id'],
                        'history_sub_total' => $history['total_amount'],
                        'compare_sub_total' => $subTotal,
                    ];
                    continue;
                }
            }

            // Check if tax
            $paySetting = $paySettingsEachTeam[$history['team_id']];
            $tax = $PaymentService->calcTax($paySetting['company_country'], $subTotal);

            $taxCompareRes = bccomp($history['tax'], $tax, 2);
            if ((int)$history['charge_type'] == Enum\ChargeHistory\ChargeType::MONTHLY_FEE) {
                if ($taxCompareRes !== 0) {
                    $errors[self::INCONSISTENT_TYPE_AMOUNT_TAX][] = [
                        'history_id'  => $history['id'],
                        'team_id'     => $history['team_id'],
                        'history_tax' => $history['tax'],
                        'compare_tax' => $tax,
                    ];
                    continue;
                }
            } else {
                // It is impossible to check amount strictly in case of pay by the day.
                // So we compare if only history's sub total doesn't over max sub total
                if ($taxCompareRes === 1) {
                    $errors[self::INCONSISTENT_TYPE_AMOUNT_TAX][] = [
                        'history_id'  => $history['id'],
                        'team_id'     => $history['team_id'],
                        'history_tax' => $history['tax'],
                        'compare_tax' => $tax,
                    ];
                    continue;
                }
            }

            $checkPassedHistories[] = $history;
        }

        // Save inconsistent data to log
        if (!empty($errors)) {
            foreach ($errors as $errType => $errData) {
                switch ($errType) {
                    case self::INCONSISTENT_TYPE_CHARGE_USER_CNT:
                        $this->logEmergency(sprintf("Inconsistent charge users: %s", AppUtil::jsonOneLine($errData)));
                        break;
                    case self::INCONSISTENT_TYPE_AMOUNT_TAX:
                        $this->logEmergency(sprintf("Inconsistent sub total: %s", AppUtil::jsonOneLine($errData)));
                        break;
                    case self::INCONSISTENT_TYPE_CHARGE_USER_CNT:
                        $this->logEmergency(sprintf("Inconsistent tax: %s", AppUtil::jsonOneLine($errData)));
                        break;
                }
            }
        }

        return $checkPassedHistories;
    }

    /**
     * Check No3 in this check list (only stripe)
     * https://confluence.goalous.com/display/GOAL/Payments+Recovery#PaymentsRecovery-1.checkinglists(検知対象項目)

     * @param array $histories
     * @param int   $startTimestamp
     * @param int   $endTimestamp
     */
    private function checkStripeAmountEachHistory(array $histories, int $startTimestamp, int $endTimestamp)
    {
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init('CreditCardService');

        $teamIds = array_unique(Hash::extract($histories, '{n}.team_id'));
        $customerCodes = $this->CreditCard->findCustomerCode($teamIds);

        // Get charge list each customer code
        // Because we can't specify multiple customer codes when call list charge api.
        $stripeCharges = [];
        foreach ($customerCodes as $customerCode) {
            $stripeCharges = am($stripeCharges, $CreditCardService->findChargesByCreatedRange($startTimestamp, $endTimestamp, $customerCode));
        }

        $this->logInfo(sprintf("Stripe charge count:%d", count($stripeCharges)));

        $histories = Hash::combine($histories, '{n}.stripe_payment_code', '{n}');
        $errors = [];
        foreach ($stripeCharges as $stripeCharge) {
            $history = Hash::get($histories, $stripeCharge->id);
            if (empty($history)) {
                $errors[self::INCONSISTENT_TYPE_STRIPE_CHARGE_MISSING][] = [
                    'id'        => $stripeCharge->id,
                    'meta_data' => $stripeCharge->metadata
                ];
                continue;
            }

            $totalAmount = bcadd($history['total_amount'], $history['tax'], 2);
            if ($history['currency'] == Enum\PaymentSetting\Currency::USD) {
                $totalAmount = $totalAmount * 100;
            }
            if (bccomp($totalAmount, $stripeCharge->amount, 2) !== 0) {
                $errors[self::INCONSISTENT_TYPE_STRIPE_AMOUNT][] = [
                    'id'        => $stripeCharge->id,
                    'meta_data' => $stripeCharge->metadata
                ];
            }
        }

        // Save inconsistent data to log
        if (!empty($errors)) {
            foreach ($errors as $errType => $errData) {
                switch ($errType) {
                    case self::INCONSISTENT_TYPE_STRIPE_CHARGE_MISSING:
                        $this->logEmergency(sprintf("Stripe charge is missing: %s", AppUtil::jsonOneLine($errData)));
                        break;
                    case self::INCONSISTENT_TYPE_STRIPE_AMOUNT:
                        $this->logEmergency(sprintf("Inconsistent stripe charge amount: %s", AppUtil::jsonOneLine($errData)));
                        break;
                }
            }
        }
    }

    /**
     * Check No3 in this check list (only stripe)
     * https://confluence.goalous.com/display/GOAL/Payments+Recovery#PaymentsRecovery-1.checkinglists(検知対象項目)

     * @param array $histories
     * @param int   $startTimestamp
     * @param int   $endTimestamp
     */
    private function checkAtobaraiComAmountEachHistory(array $histories, int $startTimestamp, int $endTimestamp)
    {
        // [Note]
        // In fact, we can't get charge information by system order code from Atobarai.com api. (I don't know why)
        // TODO: If Atobarai.com create that API, we implement.


//        /** @var InvoiceService $InvoiceService */
//        $InvoiceService = ClassRegistry::init('InvoiceService');
//
//        // Get Atobarai.com id(invoice_histories.order_id)
//        $chargeHistoryIds = Hash::extract($histories, '{n}.id');
//        $orderCodeEachChargeHistoryId = $this->InvoiceHistory->findEachChargeHistoryId($chargeHistoryIds);

    }
}
