<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');

use Goalous\Model\Enum as Enum;

/**
 * Invoice reorder for past failed order
 * [Note]
 * This shell should be executed as batch after Atobarai.com created API for getting order info
 *
 * @property GlEmailComponent $GlEmail
 * @property ChargeHistory    $ChargeHistory
 * @property TeamMember       $TeamMember
 * @property InvoiceHistory   $InvoiceHistory
 */
class ReorderInvoiceShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

    public $uses = [
        'PaymentSetting',
        'ChargeHistory',
        'TeamMember',
        'InvoiceHistory',
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
            'reorderTargetCode' => [
                'short'    => 'r',
                'help'     => 'This is `invoice_histories`.`system_order_code` of atobarai.com reorder target',
                'required' => true, // TODO: delete after set as Batch
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        $reorderTargetCode = Hash::get($this->params, 'reorderTargetCode');
        if (empty($reorderTargetCode)) {
            $this->logError(sprintf('Shell option `reorderTargetCode` is not specified. %s', AppUtil::jsonOneLine([
                'reorderTargetCode' => $reorderTargetCode,
            ])));
        }
        $invoiceHistory = $this->InvoiceHistory->findBySystemOrderCode($reorderTargetCode);
        $invoiceHistory = Hash::get($invoiceHistory, 'InvoiceHistory');
        if (empty($invoiceHistory)) {
            $this->logError(sprintf("Invoice history by Shell option `reorderTargetCode` doesn't exit. %s",
                AppUtil::jsonOneLine([
                    'reorderTargetCode' => $reorderTargetCode,
                ])));
        }
        $this->logInfo(sprintf('reorderTargetCode: %s', $reorderTargetCode));
        if ((int)$invoiceHistory['order_status'] !== Enum\Invoice\CreditStatus::NG) {
            $this->logError(sprintf("Invoice status which you specified was not failed. %s", AppUtil::jsonOneLine([
                'reorderTargetCode' => $reorderTargetCode,
            ])));
        }
        $this->logInfo(sprintf('reorderTargetCode: %s', $reorderTargetCode));

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        try {
            $teamId = (int)$invoiceHistory['team_id'];
            PaymentUtil::logCurrentTeamChargeUsers($teamId);

            // Reorder
            $result = $PaymentService->reorderInvoice($teamId, $invoiceHistory['id']);
            if ($result === true) {
                $this->logInfo(sprintf('Reorder registration was succeeded! teamId: %s', $teamId));
            } else {
                $this->logInfo(sprintf('Reorder registration was skipped or failed! teamId: %s', $teamId));
            }

            // Send notification email
            /** @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init('TeamMember');
            $adminList = $TeamMember->findAdminList($teamId);
            if (!empty($adminList)) {
                // sending emails to each admins.
                foreach ($adminList as $toUid) {
                    $this->GlEmail->sendMailRecharge($toUid, $teamId);
                }
            } else {
                CakeLog::error("This team have no admin: $teamId");
            }
        } catch (Exception $e) {
            $this->logError(sprintf("caught error on registerInvoice: %s", AppUtil::jsonOneLine([
                'message' => $e->getMessage()
            ])));
            $this->logError($e->getTraceAsString());
        }

    }

    /**
     * @param int $historyId
     *
     * @return array
     */
    function getHistory(int $historyId): array
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        $history = $this->ChargeHistory->getById($historyId);

        if (empty($history)) {
            $this->logError(sprintf("Recharge target history doesn't exit. %s", AppUtil::jsonOneLine([
                'reorderTargetCode' => $historyId,
            ])));
            exit();
        }
        $paymentSetting = $PaymentService->get($history['team_id']);
        if (empty($paymentSetting)) {
            $this->logError(sprintf("Payment setting doesn't exit. %s", AppUtil::jsonOneLine([
                'team_id' => $history['team_id']
            ])));
            exit();
        }

        if ((int)$history['payment_type'] !== Enum\PaymentSetting\Type::INVOICE) {
            $this->logError(sprintf("Payment type is not invoice %s", AppUtil::jsonOneLine([
                'team_id' => $history['team_id']
            ])));
            exit();
        }
        if ((int)$history['result_type'] === Enum\ChargeHistory\ResultType::FAIL) {
            $this->logError(sprintf("Payment type is not invoice %s", AppUtil::jsonOneLine([
                'team_id' => $history['team_id']
            ])));
            exit();
        }

        return $history;
    }
}
