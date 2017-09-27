<?php
App::import('Service', 'PaymentService');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');

use Goalous\Model\Enum as Enum;

/**
 * The shell for creating next next term
 * Console/cake monthly credit card charge
 * Description
 * - Charge each only credit card payment team that payment base date came
 *
 * @property Team             $Team
 * @property TeamMember       $TeamMember
 * @property Term             $Term
 * @property PaymentSetting   $PaymentSetting
 * @property GlEmailComponent $GlEmail
 */
class MonthlyCreditCardChargeShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

    public $uses = [
        'Team',
        'TeamMember',
        'Term',
        'PaymentSetting',
        'ChargeHistory',
        'CreditCard'
    ];

    public function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
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

        // Get charge target teams
        $targetChargeTeams = $PaymentService->findMonthlyChargeCcTeams($targetTimestamp);
        if (empty($targetChargeTeams)) {
            $this->logInfo("Billing team does not exist");
            return;
        }
        $this->logInfo(sprintf('target charge teams: %s', AppUtil::jsonOneLine($targetChargeTeams)));

        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each team and get the number of users
        $teamIds = Hash::extract($targetChargeTeams, '{n}.PaymentSetting.team_id');
        $chargeMemberCountEachTeam = [];
        foreach (array_chunk($teamIds, 100) as $chunkTeamIds) {
            $chargeMemberCountEachTeam += $TeamMember->countChargeTargetUsersEachTeam($chunkTeamIds);
        }
        $this->logInfo(sprintf('charge member count each teams: %s', AppUtil::jsonOneLine($chargeMemberCountEachTeam)));

        // Charge each team
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
                // Charge
                $chargeRes = $PaymentService->applyCreditCardCharge(
                    $teamId,
                    Enum\ChargeHistory\ChargeType::MONTHLY_FEE(),
                    $chargeMemberCount
                );
                if ($chargeRes['error']) {
                    throw new CreditCardStatusException(AppUtil::jsonOneLine($chargeRes));
                }
            } catch (CreditCardStatusException $e) {
                $this->logInfo(sprintf("Monthly charge was failed! The card has problem. msg: %s, teamId: %s",
                    $e->getMessage(),
                    $teamId
                ));
                // send e-mail to team admins for informing a charge failure
                $this->_sendingEmailToAdmins($teamId);
            } catch (Exception $e) {
                $this->logEmergency(sprintf("caught error on applyCreditCardCharge: %s", AppUtil::jsonOneLine([
                    'message' => $e->getMessage()
                ])));
                $this->logEmergency($e->getTraceAsString());
            }
        }

        if (!empty($noMemberTeams)) {
            $this->logError(
                sprintf('There are teams with no members. team_ids:',
                    AppUtil::varExportOneLine($noMemberTeams)
                )
            );
        }
    }

    /**
     * Sending Email to admins on the team.
     *
     * @param int $teamId
     */
    function _sendingEmailToAdmins(int $teamId)
    {
        $adminList = $this->TeamMember->findAdminList($teamId);
        $team = $this->Team->getById($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailChargeFailure($toUid, $teamId, $team['name']);
            }
        } else {
            $this->logInfo("TeamId:{$teamId} There is no admin..");
        }
    }
}
