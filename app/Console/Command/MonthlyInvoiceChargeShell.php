<?php
App::import('Service', 'PaymentService');
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

        // Get charge target teams
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams();
        if (empty($targetChargeTeams)) {
            $this->out("Target team doesn't exist.");
            return;
        }

        // fetching not charged charge_history

        // send invoice via atobarai.com for each teams

    }
}
