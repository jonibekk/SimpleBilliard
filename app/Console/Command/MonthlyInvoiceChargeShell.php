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
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Get charge target teams that is not charged yet.
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams($targetTs);
        if (empty($targetChargeTeams)) {
            $this->out("Target team doesn't exist.");
            return;
        }

        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each team and get the number of users
        $teamIds = Hash::extract($targetChargeTeams, '{n}.PaymentSetting.team_id');
        $chargeMemberCountEachTeam = [];
        foreach (array_chunk($teamIds, 100) as $chunkTeamIds) {
            $chargeMemberCountEachTeam += $TeamMember->countChargeTargetUsersEachTeam($chunkTeamIds);
        }
        foreach ($targetChargeTeams as $team) {
            $teamId = Hash::get($team, 'PaymentSetting.team_id');
            $timezone = Hash::get($team, 'Team.timezone');

            $chargeMemberCount = Hash::get($chargeMemberCountEachTeam, $teamId);
            // Check if exist member
            if (empty($chargeMemberCount)) {
                $noMemberTeams[] = $teamId;
                continue;
            }
            $targetHistories = $PaymentService->findChargeTargetHistories($teamId, $targetTs, $timezone);
            $PaymentService->registerInvoice($teamId, $chargeMemberCount, $targetTs, $timezone, $targetHistories);

        }

        if (!empty($noMemberTeams)) {
            $this->log(
                sprintf('There are teams with no members. team_ids:',
                    AppUtil::varExportOneLine($noMemberTeams)
                )
            );
        }
    }
}
