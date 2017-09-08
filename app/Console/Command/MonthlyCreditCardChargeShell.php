<?php
App::import('Service', 'PaymentService');
App::uses('AppUtil', 'Util');

use Goalous\Model\Enum as Enum;

/**
 * The shell for creating next next term
 * Console/cake monthly credit card charge
 * Description
 * - Charge each only credit card payment team that payment base date came
 *
 * @property Team           $Team
 * @property Term           $Term
 * @property PaymentSetting $PaymentSetting
 */
class MonthlyCreditCardChargeShell extends AppShell
{
    public $uses = [
        'Team',
        'Term',
        'PaymentSetting',
        'ChargeHistory',
        'CreditCard'
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
        $targetChargeTeams = $PaymentService->findMonthlyChargeCcTeams();
        if (empty($targetChargeTeams)) {
            $this->log('Billing team does not exist', LOG_INFO);
            exit;
        }
        $this->out(print_r(compact('targetChargeTeams'), true));

        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each team and get the number of users
        $teamIds = Hash::extract($targetChargeTeams, '{n}.PaymentSetting.team_id');
        $this->out(print_r(compact('teamIds'), true));
        $chargeMemberCountEachTeam = [];
        foreach (array_chunk($teamIds, 100) as $chunkTeamIds) {
            $chargeMemberCountEachTeam += $TeamMember->countChargeTargetUsersEachTeam($chunkTeamIds);
        }
        $this->out(print_r(compact('chargeMemberCountEachTeam'), true));

        // Charge each team
        foreach ($targetChargeTeams as $team) {
            $teamId = Hash::get($team, 'PaymentSetting.team_id');
            $chargeMemberCount = Hash::get($chargeMemberCountEachTeam, $teamId);
            $this->out(print_r(compact('teamId', 'chargeMemberCount'), true));
            // Check if exist member
            if (empty($chargeMemberCount)) {
                $noMemberTeams[] = $teamId;
                continue;
            }

            try {
                // Charge
                $PaymentService->applyCreditCardCharge(
                    $teamId,
                    Enum\ChargeHistory\ChargeType::MONTHLY_FEE(),
                    $chargeMemberCount
                );
            } catch (Exception $e) {
                // TODO: implement
            }
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
