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
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Get charge target teams
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams();
        if (empty($targetChargeTeams)) {
            $this->log('Billing team does not exist', LOG_INFO);
            exit;
        }

    }
}
