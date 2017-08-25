<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Service', 'InvoiceService');
App::import('Service', 'TeamService');

/**
 * Class InquireInvoiceCreditStatusShell
 *
 * @property Invoice        $Invoice
 * @property InvoiceHistory $InvoiceHistory
 * @property InvoiceService $InvoiceService
 * @property PaymentSetting $PaymentSetting
 * @property TeamService    $TeamService
 */
class InquireInvoiceCreditStatusShell extends AppShell
{
    public $uses = [
        'Invoice',
        'InvoiceHistory',
        'InvoiceService',
        'PaymentSetting',
        'TeamService',
    ];

    public function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
    }

    /**
     * Entry point of the Shell
     */
    public function main()
    {
        // Get the waiting for approval invoices
        $orders = $this->InvoiceHistory->getByOrderStatus(Invoice::CREDIT_STATUS_WAITING);
        foreach ($orders as $order) {
            $invoiceHistory = Hash::get($order, 'InvoiceHistory');
            $orderCode = $invoiceHistory['system_order_code'];

            // check status at Atobarai.com
            $status = $this->InvoiceService->inquireCreditStatus($orderCode);

            // Wrong response, try again on the next batch
            if (empty($status) || $status['status'] == 'error') {
                $this->log("Error inquiring credit status: " . AppUtil::varExportOneLine($status), LOG_WARNING);
                continue;
            }

            // Process status
            $this->_processCreditStatus($invoiceHistory, $status);
        }
    }

    /**
     * Process the credit status for given invoice
     *
     * @param array $invoiceHistory
     * @param array $inquireResult
     */
    private function _processCreditStatus(array $invoiceHistory, array $inquireResult)
    {
        // Wrong status
        $result = Hash::get($inquireResult, 'results.result');
        if ($result == null) {
            $this->log("Credit inquire result not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return;
        }

        // Order status not available
        $orderStatus = Hash::get($result, 'orderStatus.@cd');
        if ($orderStatus == null) {
            $this->log("Credit inquire order status not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return;
        }

        // Waiting for credit check, do nothing
        if ($orderStatus == Invoice::CREDIT_STATUS_WAITING) {
            // Nothing to do right now.
            return;
        }

        // Check for valid order status
        if (!($orderStatus == Invoice::CREDIT_STATUS_NG || $orderStatus == Invoice::CREDIT_STATUS_OK)) {
            $this->log("Invalid order status: " .
                AppUtil::varExportOneLine($inquireResult));
            return;
        }

        // Get invoice
        $teamId = $invoiceHistory['team_id'];
        $invoice = $this->Invoice->getByTeamId($teamId);
        $serviceUseStatus = $orderStatus == Invoice::CREDIT_STATUS_OK ?
            Team::SERVICE_USE_STATUS_PAID : Team::SERVICE_USE_STATUS_READ_ONLY;

        // Send email for the first time the credit is approved or when the credit is declined
        if (($orderStatus == Invoice::CREDIT_STATUS_OK && $invoice['credit_status'] == Invoice::CREDIT_STATUS_WAITING)
            || $orderStatus == Invoice::CREDIT_STATUS_NG) {
            // Send notification email
            $this->_sendCreditStatusNotification($teamId, $orderStatus);
        }

        // Update credit status for invoices tables
        $this->_updateCreditStatus($invoiceHistory, $orderStatus);

        // Set service status to ready only if credit status is NG
        if ($orderStatus == Invoice::CREDIT_STATUS_NG) {
            $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_READ_ONLY, date('Y-m-d'));
        }
    }

    /**
     * Send notification email for credit status
     *
     * @param int $teamId
     * @param int $creditStatus
     */
    private function _sendCreditStatusNotification(int $teamId, int $creditStatus)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailCreditStatusNotification($toUid, $teamId, $creditStatus);
            }
        } else {
            $this->log("TeamId:{$teamId} There is no admin..", LOG_WARNING);
        }
    }

    /**
     * Update invoice and invoice history tables with credit status
     *
     * @param array $invoiceHistory
     * @param int   $creditStatus
     *
     * @return bool
     */
    private function _updateCreditStatus(array $invoiceHistory, int $creditStatus): bool
    {
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');

        $invoiceHistory['order_status'] = $creditStatus;
        $invoice = $Invoice->getByTeamId($invoiceHistory['team_id']);
        $invoice['credit_status'] = $creditStatus;

        try {
            $InvoiceHistory->begin();

            if (!$InvoiceHistory->save($invoiceHistory)) {
                throw new Exception(sprintf("Failed to save Invoice history. data: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistory),
                    AppUtil::varExportOneLine($InvoiceHistory->validationErrors)));
            }

            if (!$Invoice->save($invoice)) {
                throw new Exception(sprintf("Failed to save Invoice order status. data: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoice),
                    AppUtil::varExportOneLine($Invoice->validationErrors)));
            }

            $InvoiceHistory->commit();
        } catch (Exception $e) {
            $InvoiceHistory->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }
}
