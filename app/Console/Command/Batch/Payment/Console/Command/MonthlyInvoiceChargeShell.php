<?php
App::import('Service', 'PaymentService');
App::import('Service', 'InvoiceService');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');

/**
 * The shell for charging by invoice to team admins
 * Console/cake monthly_invoice_charge
 * If retry: Console/cake Payment.monthly_invoice_charge -t <target timestamp>
 * Description
 * - Sending invoice each only invoice that payment base date came
 */
class MonthlyInvoiceChargeShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

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
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");

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

            try {
                PaymentUtil::logCurrentTeamChargeUsers($teamId);

                $pricePlanId = null;
                if ($CampaignService->isCampaignTeam($teamId)) {
                    $pricePlan = $CampaignService->getTeamPricePlan($teamId);
                    $pricePlanId = $pricePlan['id'];
                }

                // TODO: fix 6th arguments: https://jira.goalous.com/browse/GL-6378
                $retRegistration = $PaymentService->registerInvoice($teamId, $chargeMemberCount, $targetTimestamp, null, true, $pricePlanId);
                if ($retRegistration === true) {
                    $this->logInfo(sprintf('Order registration was succeeded! teamId: %s', $teamId));
                } else {
                    $this->logInfo(sprintf('Order registration was skipped or failed! teamId: %s', $teamId));
                }
            } catch (Exception $e) {
                $this->logEmergency(sprintf("caught error on registerInvoice: %s", AppUtil::jsonOneLine([
                    'message' => $e->getMessage()
                ])));
                $this->logEmergency($e->getTraceAsString());
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
