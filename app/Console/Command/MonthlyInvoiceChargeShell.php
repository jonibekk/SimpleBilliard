<?php
App::import('Service', 'PaymentService');
App::import('Service', 'InvoiceService');
App::uses('AppUtil', 'Util');

/**
 * The shell for charging by invoice to team admins
 * Console/cake monthly_invoice_charge
 * If retry: Console/cake monthly_invoice_charge -t <target timestamp>
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
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'targetTimestamp' => [
                'short'   => 't',
                'help'    => 'This is current unix timestamp  as default, if retry, specify the param as target timestamp',
                'default' => REQUEST_TIMESTAMP,
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
        $targetTs = $this->param('targetTimestamp');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        // Get charge target teams that is not charged yet.
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams($targetTs);
        if (empty($targetChargeTeams)) {
            $this->out("Target team doesn't exist.");
            return;
        }

        foreach ($targetChargeTeams as $team) {
            $teamId = Hash::get($team, 'PaymentSetting.team_id');
            $timezone = Hash::get($team, 'team.timezone');
            $PaymentService->registerInvoice($teamId, $targetTs, $timezone);
        }

    }
}
