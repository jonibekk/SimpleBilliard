<?php
App::import('Service', 'TeamService');
App::uses('AppUtil', 'Util');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');

use Goalous\Model\Enum as Enum;

/**
 * The shell for moving team status to read only
 * `./Console/cake move_read_only_if_cc_charge_fails`
 * Description
 * - Move team status to read only automatically when credit card monthly payment has failed three consecutive times
 *
 * @property Team           $Team
 * @property TeamMember           $TeamMember
 * @property GlEmailComponent $GlEmail
 */
class MoveReadOnlyIfCcChargeFailsShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

    public $uses = [
        'Team',
        'TeamMember'
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

        // To check if it failed three times certainly, filter charge history from the date four months ago rather than three months ago to the current day
        $startTimestamp = GoalousDateTime::now()->subMonth(4)->getTimestamp();
        $targetTeamIds = $this->Team->findTargetsForMovingReadOnly($startTimestamp, $targetTimestamp);

        if (empty($targetTeamIds)) {
            $this->logInfo("Target team does not exist");
            return;
        }

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        $updateErrTeamIds = [];
        foreach ($targetTeamIds as $teamId) {
            $res = $TeamService->updateServiceUseStatus(
                $teamId,
                Enum\Team\ServiceUseStatus::READ_ONLY,
                GoalousDateTime::now()->format('Y-m-d')
            );
            if (!$res) {
                $updateErrTeamIds[] = $teamId;
                continue;
            }
            $this->_sendEmailToAdmins($teamId);
        }

        if (!empty($updateErrTeamIds)) {
            $this->logError(
                sprintf("Failed to update team status to read only. %s"),
                AppUtil::jsonOneLine(compact('updateErrTeamIds'))
            );
        }
    }

    /**
     * Sending Email to admins on the team.
     *
     * @param int $teamId
     */
    function _sendEmailToAdmins(int $teamId)
    {
        $adminList = $this->TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailMovedReadOnlyForChargeFailure($toUid, $teamId);
            }
        } else {
            $this->logError("TeamId:{$teamId} There is no admin..");
        }
    }

}
