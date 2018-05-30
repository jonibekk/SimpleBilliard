<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');

use Goalous\Model\Enum as Enum;

/**
 * class ReorderCreditCardShell
 *
 * @see
 *     This sentence below is a things we want to achieve in future.
 *     This batch is reordering Stripe charge by manually.
 *     We want to automate reorder in future,
 *     when team's credit card is updated
 *     or team that latest payment is failed keep retrying reorder.
 *
 *     このShellは再請求を手動実行する物です.
 *     しかし, 将来的には以下のタイミング等で再請求を自動化したい.
 *     - チームのカード情報が更新された
 *     - 最新の決済が失敗しているチームに対して一定間隔で決済を行い続ける
 *
 * # Usage
 * ```
 * ./Console/cake Payment.reorder_credit_card --reorderChargeHistoryId=<charge_histories.id>
 * # e.g.
 * ./Console/cake Payment.reorder_credit_card --reorderChargeHistoryId=1
 * ```
 * @property GlEmailComponent $GlEmail
 * @property ChargeHistory $ChargeHistory
 * @property TeamMember       $TeamMember
 */
class ReorderCreditCardShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

    const OPTION_NAME_CHARGE_HISTORY_ID = 'reorderChargeHistoryId';

    public $uses = [
        'PaymentSetting',
        'ChargeHistory',
        'TeamMember',
    ];

    public function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
    }

    function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            static::OPTION_NAME_CHARGE_HISTORY_ID => [
                'short'    => 'r',
                'help'     => 'charge_histories.id to reordering (must be payed by credit card)',
                'required' => true,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        try {
            $reorderChargeHistoryId = Hash::get($this->params, static::OPTION_NAME_CHARGE_HISTORY_ID);
            if (empty($reorderChargeHistoryId)) {
                $this->logError(sprintf('Shell option `%s` is not specified. %s', static::OPTION_NAME_CHARGE_HISTORY_ID, AppUtil::jsonOneLine([
                    'reorderStripePaymentCode' => $reorderChargeHistoryId,
                ])));
                return;
            }
            $chargeHistory = $this->ChargeHistory->getById($reorderChargeHistoryId);
            if (empty($chargeHistory)) {
                $this->logError(sprintf('Target `%s` not found', static::OPTION_NAME_CHARGE_HISTORY_ID));
                return;
            }
            $teamId = $chargeHistory['team_id'];
            $paymentType = new Enum\PaymentSetting\Type(intval($chargeHistory['payment_type']));
            if (!$paymentType->equals(Enum\PaymentSetting\Type::CREDIT_CARD())) {
                $this->logError(sprintf('Target `%s` is not payed by credit card %s', static::OPTION_NAME_CHARGE_HISTORY_ID, AppUtil::jsonOneLine($chargeHistory)));
                return;
            }

            $this->hr();
            $this->logInfo('Target charge history to reorder');
            $this->logInfo(var_export($chargeHistory, true));
            $inputConfirmContinue = $this->in('Type "yes" to continue reorder [yes/no]');
            $this->logInfo(sprintf('confirm input: %s', $inputConfirmContinue));
            if ('yes' !== $inputConfirmContinue) {
                $this->logInfo('aborted');
                return;
            }

            /** @var PaymentService $PaymentService */
            $PaymentService = ClassRegistry::init('PaymentService');
            $stripeResult = $PaymentService->reorderCreditCardCharge($chargeHistory);

            $isSucceeded = ($stripeResult['status'] === Enum\Stripe\StripeStatus::SUCCEEDED);

            $this->logResult($isSucceeded, $stripeResult);

            if ($isSucceeded) {
                $this->sendMailRechargeToAdmins($teamId);
            }
        } catch (Exception $e) {
            $this->logEmergency(sprintf("Caught error on reordering by Stripe: %s", AppUtil::jsonOneLine([
                'message' => $e->getMessage(),
            ])));
            $this->logEmergency($e->getTraceAsString());
        }
    }

    /**
     * Output result to log
     *
     * @param bool  $isSucceeded
     * @param array $stripeResult
     */
    public function logResult(bool $isSucceeded, array $stripeResult)
    {
        $this->hr();
        if ($isSucceeded) {
            $this->logInfo('Succeeded');
            $newChargeHistoryId = $stripeResult['paymentData']['metadata']['history_id'];
            $newChargeHistory = $this->ChargeHistory->getById($newChargeHistoryId);
            $this->logInfo(var_export($newChargeHistory, true));
        } else {
            $this->logError('Failed');
            $this->logError(sprintf('Stripe order failed %s', AppUtil::jsonOneLine([
                'stripeResult' => $stripeResult,
            ])));
        }
    }

    /**
     * Send reorder complete email to target teams.id admins
     *
     * @param int $teamId
     */
    public function sendMailRechargeToAdmins(int $teamId)
    {
        // Send notification email
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            $this->logInfo(sprintf('Sending email to %d users', count($adminList)));
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailRecharge($toUid, $teamId);
            }
        } else {
            $this->logError("This team have no admin: $teamId");
        }
    }
}
