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
 * # Usage
 *
 * ```
 * ./Console/cake Payment.reorder_credit_card --reorderChargeHistoryId=<charge_histories.id>
 * # e.g.
 * ./Console/cake Payment.reorder_credit_card --reorderChargeHistoryId=1
 * ```
 *
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

            $this->hr();
            if ($stripeResult['status'] === 'succeeded') {
                $this->logInfo('Succeeded');
                $newChargeHistoryId = $stripeResult['paymentData']['metadata']['history_id'];
                $newChargeHistory = $this->ChargeHistory->getById($newChargeHistoryId);
                $teamId = $newChargeHistory['team_id'];
                $this->logInfo(var_export($newChargeHistory, true));

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
            } else {
                $this->logError('Failed');
                $this->logError(sprintf('Stripe order failed %s', AppUtil::jsonOneLine([
                    'stripeResult' => $stripeResult,
                ])));
            }
        } catch (Exception $e) {
            $this->logError(sprintf("Caught error on reordering by Stripe: %s", AppUtil::jsonOneLine([
                'message' => $e->getMessage(),
            ])));
            $this->logError($e->getTraceAsString());
        }
    }
}
