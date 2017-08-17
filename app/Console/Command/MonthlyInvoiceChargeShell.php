<?php
App::import('Service', 'PaymentService');
App::import('Service', 'InvoiceService');
App::uses('AppUtil', 'Util');

/**
 * The shell for charging by invoice to team admins
 * Console/cake monthly_invoice_charge
 * Description
 * - Sending invoice each only invoice that payment base date came
 */
class MonthlyInvoiceChargeShell extends AppShell
{
    public $uses = [
    ];

    public function startup()
    {
        parent::startup();
    }

    /**
     * Entry point of the Shell
     */
    public function main()
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var InvoiceService $InvoiceService */
        $InvoiceService = ClassRegistry::init('InvoiceService');

        // Get charge target teams that is not charged yet.
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams();
        if (empty($targetChargeTeams)) {
            $this->out("Target team doesn't exist.");
            return;
        }

        // send invoice via atobarai.com for each teams
        $InvoiceService->registerOrder(1, []);

    }
}
