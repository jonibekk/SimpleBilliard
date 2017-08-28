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
 * - Check invoice_histories table for order status as CREDIT_STATUS_WAITING
 * - Inquire theses these invoices orders with Atobarai.com API
 * - Send email with credit results in case of credit denied or on the first time
 * the credit is approved.
 * - Save credit result status.
 * - Set account as ready-only in case of credit denied.
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
        $this->out('Starting credit inquire batch.');
        // Get the waiting for approval invoices
        $orders = $this->InvoiceHistory->getByOrderStatus(Invoice::CREDIT_STATUS_WAITING);

        $count = 1;
        $this->out('Number of invoice orders is: ' . count($orders));
        foreach ($orders as $order) {
            $this->out(sprintf('- Processing %d of %d orders', $count, count($orders)));
            $count++;

            $invoiceHistory = Hash::get($order, 'InvoiceHistory');
            if (empty($invoiceHistory)) {
                $this->log("Error getting order history: Order: " . AppUtil::varExportOneLine($order));
                continue;
            }
            $orderCode = Hash::get($invoiceHistory, 'system_order_code');

            // check status at Atobarai.com
            $status = $this->InvoiceService->inquireCreditStatus($orderCode);

            // Wrong response, try again on the next batch
            // TODO.Payment:save error log to db
            if (empty($status) || $status['status'] == 'error') {
                $this->log("Error inquiring credit status: " . AppUtil::varExportOneLine($status));
                $this->out(sprintf('Failed to inquire order code. OrderCode: %s', $orderCode));
                continue;
            }

            // Process status
            if ($this->_processCreditStatus($invoiceHistory, $status)) {
                $this->out(sprintf('Invoice order inquired with success. OrderCode: %s', $orderCode));
            } else {
                $this->out(sprintf('Invoice order failed to process. OrderCode: %s', $orderCode));
            }
        }
        $this->out('Done inquiring invoice orders.');
    }

    /**
     * Process the credit status for given invoice
     *
     * @param array $invoiceHistory
     * @param array $inquireResult
     *
     * @return bool
     */
    private function _processCreditStatus(array $invoiceHistory, array $inquireResult): bool
    {
        // Wrong status
        $result = Hash::get($inquireResult, 'results.result');
        if ($result == null) {
            $this->log("Credit inquire result not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Order status not available
        $orderStatus = Hash::get($result, 'orderStatus.@cd');
        if ($orderStatus == null) {
            $this->log("Credit inquire order status not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Waiting for credit check, do nothing
        if ($orderStatus == Invoice::CREDIT_STATUS_WAITING) {
            // Nothing to do right now.
            return false;
        }

        // Check for valid order status
        if (!($orderStatus == Invoice::CREDIT_STATUS_NG || $orderStatus == Invoice::CREDIT_STATUS_OK)) {
            $this->log("Invalid order status: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Get invoice
        $teamId = Hash::get($invoiceHistory, 'team_id');
        $invoice = $this->Invoice->getByTeamId($teamId);

        // Credit accepted
        if ($orderStatus == Invoice::CREDIT_STATUS_OK) {
            // Invoice status is waiting, means it is the first time
            if (Hash::get($invoice, 'credit_status') == Invoice::CREDIT_STATUS_WAITING) {
                // Send notification email
                $this->_sendCreditStatusNotification($teamId, $orderStatus);

                // Update credit status for invoices tables
                $this->_updateCreditStatus($invoiceHistory, $orderStatus);
            }
            return true;
        }

        // Credit denied
        if ($orderStatus == Invoice::CREDIT_STATUS_NG) {
            // Get timezone
            $timezone = $this->TeamService->getTeamTimezone($teamId);
            if ($timezone === null) {
                $this->log("Invalid timezone for team: " . $teamId);
                return false;
            }

            // Send notification email
            $this->_sendCreditStatusNotification($teamId, $orderStatus);

            // Update credit status for invoices tables
            $this->_updateCreditStatus($invoiceHistory, $orderStatus);

            // Set service to ready only
            $startDate = AppUtil::todayDateYmdLocal($timezone);
            $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_READ_ONLY, $startDate);
        }

        return true;
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
        if (empty($invoice)) {
            $this->log("Invoice not found for invoice history: " . AppUtil::varExportOneLine($invoiceHistory));
            return false;
        }
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
