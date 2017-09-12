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
        $targetTimestamp = $this->param('targetTimestamp') ?? GoalousDateTime::now()->getTimestamp();
        $this->logInfo(sprintf('target time stamp: %d (%s)',
            $targetTimestamp,
            GoalousDateTime::createFromTimestamp($targetTimestamp)->format('Y-m-d H:i:s')));
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Get charge target teams that is not charged yet.
        $targetChargeTeams = $PaymentService->findMonthlyChargeInvoiceTeams($targetTimestamp);
        if (empty($targetChargeTeams)) {
            $this->logInfo("Target team doesn't exist.");
            return;
        }
        $this->logInfo('count $targetChargeTeams is ' . count($targetChargeTeams));
        $this->logInfo(sprintf('target teams: %s', AppUtil::jsonOneLine($targetChargeTeams)));

        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each team and get the number of users
        $teamIds = Hash::extract($targetChargeTeams, '{n}.PaymentSetting.team_id');
        $chargeMemberCountEachTeam = [];
        foreach (array_chunk($teamIds, 100) as $chunkTeamIds) {
            $chargeMemberCountEachTeam += $TeamMember->countChargeTargetUsersEachTeam($chunkTeamIds);
        }
        $this->logInfo('$chargeMemberCountEachTeam');
        $this->logInfo(sprintf('chargeMemberCountEachTeam: %s', AppUtil::jsonOneLine($chargeMemberCountEachTeam)));

        foreach ($targetChargeTeams as $team) {
            $teamId = Hash::get($team, 'PaymentSetting.team_id');

            $chargeMemberCount = Hash::get($chargeMemberCountEachTeam, $teamId);
            // Check if exist member
            if (empty($chargeMemberCount)) {
                $noMemberTeams[] = $teamId;
                continue;
            }
            $retRegistration = $PaymentService->registerInvoice($teamId, $chargeMemberCount, $targetTimestamp);
            if ($retRegistration === true) {
                $this->logInfo(sprintf('Order registration was succeeded! teamId: %s', $teamId));
            } else {
                $this->logInfo(sprintf('Order registration was skipped or failed! teamId: %s', $teamId));
            }
        }

        if (!empty($noMemberTeams)) {
            $this->logInfo(
                sprintf('There are teams with no members. team_ids:',
                    AppUtil::varExportOneLine($noMemberTeams)
                )
            );
        }
    }
}
