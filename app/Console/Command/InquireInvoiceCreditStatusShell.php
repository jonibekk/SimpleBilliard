<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Service', 'InvoiceService');
App::import('Service', 'TeamService');

use Goalous\Model\Enum as Enum;

/**
 * Class InquireInvoiceCreditStatusShell
 *
 * - Check invoice_histories table for order status as CREDIT_STATUS_WAITING
 * - Inquire theses these invoices orders with Atobarai.com API
 * - Send email with credit results in case of credit denied or on the first time
 * the credit is approved.
 * - Save credit result status.
 * - Set account as read-only in case of credit denied.
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
        $orders = $this->InvoiceHistory->getByOrderStatus(Enum\Invoice\CreditStatus::WAITING);

        $count = 1;
        $this->logInfo('Number of invoice orders is: ' . count($orders));
        foreach ($orders as $order) {
            $this->logInfo(sprintf('- Processing %d of %d orders', $count, count($orders)));
            $count++;

            $invoiceHistory = Hash::get($order, 'InvoiceHistory');
            if (empty($invoiceHistory)) {
                $this->logError("Error getting order history: Order: " . AppUtil::varExportOneLine($order));
                continue;
            }
            $orderCode = Hash::get($invoiceHistory, 'system_order_code');

            // check status at Atobarai.com
            $status = $this->InvoiceService->inquireCreditStatus($orderCode);

            // Wrong response, try again on the next batch
            // TODO.Payment:save error log to db
            if (empty($status) || $status['status'] == 'error') {
                $this->logInfo("Error inquiring credit status: " . AppUtil::varExportOneLine($status));
                $this->logInfo(sprintf('Failed to inquire order code. OrderCode: %s', $orderCode));
                continue;
            }

            // Process status
            if ($this->_processCreditStatus($invoiceHistory, $status)) {
                $this->logInfo(sprintf('Invoice order inquired with success. OrderCode: %s', $orderCode));
            } else {
                $this->logInfo(sprintf('Invoice order failed to process. OrderCode: %s', $orderCode));
            }
        }
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
            $this->logInfo("Credit inquire result not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Order status not available
        $orderStatus = Hash::get($result, 'orderStatus.@cd');
        if ($orderStatus == null) {
            $this->logError("Credit inquire order status not found: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Waiting for credit check, do nothing
        if ($orderStatus == Enum\Invoice\CreditStatus::WAITING) {
            // Nothing to do right now.
            return false;
        }

        // Check for valid order status
        if (!($orderStatus == Enum\Invoice\CreditStatus::NG || $orderStatus == Enum\Invoice\CreditStatus::OK)) {
            $this->logInfo("Invalid order status: " .
                AppUtil::varExportOneLine($inquireResult));
            return false;
        }

        // Get invoice
        $teamId = Hash::get($invoiceHistory, 'team_id');
        $invoice = $this->Invoice->getByTeamId($teamId);

        // Credit accepted
        if ($orderStatus == Enum\Invoice\CreditStatus::OK) {
            // Invoice status is waiting, means it is the first time
            if (Hash::get($invoice, 'credit_status') == Enum\Invoice\CreditStatus::WAITING) {
                // Send notification email
                $this->_sendCreditStatusNotification($teamId, $orderStatus);

                // Update credit status for invoices tables
                $this->InvoiceService->updateCreditStatus($invoiceHistory['id'], $orderStatus);
            }
            return true;
        }

        // Credit denied
        if ($orderStatus == Enum\Invoice\CreditStatus::NG) {
            // Get timezone
            $timezone = $this->TeamService->getTeamTimezone($teamId);
            if ($timezone === null) {
                $this->logError("Invalid timezone for team: " . $teamId);
                return false;
            }

            // Send notification email
            $this->_sendCreditStatusNotification($teamId, $orderStatus);

            // Update credit status for invoices tables
            $this->InvoiceService->updateCreditStatus($invoiceHistory['id'], $orderStatus);

            // Set service to read only
            $currentDateTimeOfTeamTimeZone = GoalousDateTime::now()->setTimeZoneByHour($timezone);
            $this->TeamService->updateServiceUseStatus($teamId, Enum\Team\ServiceUseStatus::READ_ONLY, $currentDateTimeOfTeamTimeZone->format('Y-m-d'));
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
            $this->logInfo("TeamId:{$teamId} There is no admin..", LOG_WARNING);
        }
    }
}
