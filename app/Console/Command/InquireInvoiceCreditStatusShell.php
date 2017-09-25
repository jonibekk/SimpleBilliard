<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::uses('XmlAtobaraiResponse', 'AtobaraiCom');
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
    protected $enableOutputLogStartStop = true;

    const OPTION_SIMULATE_INQUIRE_CREDIT_STATUS = 'simulate_inquire_credit_status';
    const OPTION_SPECIFY_TEAM_ID = 'specify_team_id';

    /**
     * @var null|Enum\AtobaraiCom\Credit
     */
    private $simulateInquireCreditStatus = null;

    /**
     * if value is int, inquiring limit to specified team id
     * @var null|int
     */
    private $specifyTeamId = null;

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

        $simulateInquireCreditStatus = Hash::get($this->params, self::OPTION_SIMULATE_INQUIRE_CREDIT_STATUS);
        if (!is_null($simulateInquireCreditStatus)) {
            if ($this->isEnvironmentProduction()) {
                $this->out(sprintf('cant take option %s on env: %s', self::OPTION_SIMULATE_INQUIRE_CREDIT_STATUS, ENV_NAME));
                die();
            }
            $this->simulateInquireCreditStatus = new Enum\AtobaraiCom\Credit(intval($simulateInquireCreditStatus));
        }
        $specifyTeamId = Hash::get($this->params, self::OPTION_SPECIFY_TEAM_ID);
        if (AppUtil::isInt($specifyTeamId)) {
            $this->logInfo(sprintf('teams.id limited: %s', AppUtil::jsonOneLine([
                'specify_team_id' => $specifyTeamId,
            ])));
            $this->specifyTeamId = intval($specifyTeamId);
        }
    }

    /**
     * @override
     * @return ConsoleOptionParser
     */
    function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            self::OPTION_SIMULATE_INQUIRE_CREDIT_STATUS => [
                'help'    => 'this batch simulate the credit result from Atobarai.com'
                // change enums to string
                // '0: OK' . '1: NG' ...
                . array_reduce(
                    Enum\AtobaraiCom\Credit::values(), function(string $string, Enum\AtobaraiCom\Credit $creditStatus) {
                    return $string . sprintf('%d: %s', $creditStatus->getValue(), $creditStatus->getKey()) . PHP_EOL;
                }, PHP_EOL),
                'default' => null,
                'choices' => array_values(Enum\AtobaraiCom\Credit::toArray()),
            ],
            self::OPTION_SPECIFY_TEAM_ID => [
                'help'    => 'pass the teams.id to limit the inquiring history by teams.id',
                'default' => null,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
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
            $invoiceHistory = Hash::get($order, 'InvoiceHistory');
            $this->logInfo(sprintf('- Processing %d/%d orders: %s', $count, count($orders), AppUtil::jsonOneLine([
                'invoice_histories.id' => $invoiceHistory['id'],
                'teams.id' => $invoiceHistory['team_id'],
            ])));
            $count++;

            // skip if specified team id has set
            if (is_int($this->specifyTeamId) && $this->specifyTeamId != $invoiceHistory['team_id']) {
                $this->logInfo(sprintf('skipped: %s', AppUtil::jsonOneLine([
                    'invoice_histories.id' => $invoiceHistory['id'],
                    'teams.id' => $invoiceHistory['team_id'],
                ])));
                continue;
            }

            if (empty($invoiceHistory)) {
                $this->logError("Error getting order history: Order: " . AppUtil::varExportOneLine($order));
                continue;
            }
            $orderCode = Hash::get($invoiceHistory, 'system_order_code');

            if ($this->simulateInquireCreditStatus instanceof Enum\AtobaraiCom\Credit) {
                $this->registerHttpClientMock(
                    $invoiceHistory['system_order_code'],
                    $this->simulateInquireCreditStatus
                );
            }

            // check status at Atobarai.com
            $status = $this->InvoiceService->inquireCreditStatus($orderCode);

            // Wrong response, try again on the next batch
            // TODO.Payment:save error log to db
            if (empty($status) || $status['status'] == 'error') {
                $this->logEmergency(sprintf("Error inquiring credit status: %s", AppUtil::jsonOneLine($status)));
                $this->logEmergency(sprintf('Failed to inquire order code. OrderCode: %s', $orderCode));
                continue;
            }

            // Process status
            if ($this->_processCreditStatus($invoiceHistory, $status)) {
                $this->logInfo(sprintf('Invoice order inquired with success: %s', AppUtil::jsonOneLine([
                    'invoice_histories.id' => $invoiceHistory['id'],
                    'orderStatus@cd' => $status['results']['result']['orderStatus']['@cd'] ?? '',
                    'orderCode' => $orderCode,
                ])));
            } else {
                $this->logInfo(sprintf('Invoice order failed to process: %s', AppUtil::jsonOneLine([
                    'invoice_histories.id' => $invoiceHistory['id'],
                    'orderStatus@cd' => $status['results']['result']['orderStatus']['@cd'] ?? '',
                    'orderCode' => $orderCode,
                ])));
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
        if (!in_array($orderStatus, [
            Enum\Invoice\CreditStatus::NG,
            Enum\Invoice\CreditStatus::OK,
            Enum\Invoice\CreditStatus::CANCELED,
        ])) {
            $this->logError("Invalid order status: " . AppUtil::jsonOneLine($inquireResult));
            $this->logError(sprintf('Invalid order status: %s', AppUtil::jsonOneLine($inquireResult)));
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
            return true;
        }

        // Credit canceled
        if ($orderStatus == Enum\Invoice\CreditStatus::CANCELED) {
            // Get timezone
            $timezone = $this->TeamService->getTeamTimezone($teamId);
            if ($timezone === null) {
                $this->logError("Invalid timezone for team: " . $teamId);
                return false;
            }

            // Update credit status for invoices tables
            $this->InvoiceService->updateCreditStatus($invoiceHistory['id'], $orderStatus);
            return true;
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

    /**
     * @param string                  $orderId
     * @param Enum\AtobaraiCom\Credit $creditStatus
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function registerHttpClientMock(string $orderId, Enum\AtobaraiCom\Credit $creditStatus)
    {
        $r = XmlAtobaraiResponse::getInquireCreditStatus([
            [
                'orderId'     => $orderId,
                'entOrderId'  => '',
                'orderCreditStatus' => $creditStatus,
            ],
        ]);
        $response = new \GuzzleHttp\Psr7\Response($r['status'], [], $r['xml']);

        $handler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            $response,
        ]));
        $client = new \GuzzleHttp\Client(['handler' => $handler]);

        $objectKey = \GuzzleHttp\Client::class;
        ClassRegistry::removeObject($objectKey);
        ClassRegistry::addObject($objectKey, $client);
    }
}
