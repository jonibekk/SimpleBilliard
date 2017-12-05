<?php
App::import('Service', 'TeamService');
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
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

    const OPTION_FORCE_UPDATE_TEAM_ID = 'force_update_team_id';

    // Batch argument for force updating status
    private $forceUpdateTeamId = null;


    public $uses = [
        'Team',
        'TeamMember',
    ];

    public function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());

        $forceUpdateTeamId = Hash::get($this->params, self::OPTION_FORCE_UPDATE_TEAM_ID);
        if (AppUtil::isInt($forceUpdateTeamId)) {
            $team = $this->Team->getById($forceUpdateTeamId);
            if (empty($team)) {
                $this->logError(sprintf("Team by argument `force_update_team_id` doesn't exist. %s", AppUtil::jsonOneLine([
                    'force_update_team_id' => $forceUpdateTeamId,
                ])));
                exit();
            }
            if ((int)$team['service_use_status'] != Enum\Team\ServiceUseStatus::PAID) {
                $this->logError(sprintf("Team's service use status by argument `force_update_team_id` doesn't equal to paid plan. %s", AppUtil::jsonOneLine([
                    'force_update_team_id' => $forceUpdateTeamId,
                ])));
                exit();
            }

            $this->logInfo(sprintf('teams.id limited: %s', AppUtil::jsonOneLine([
                'force_update_team_id' => $forceUpdateTeamId,
            ])));
            $this->forceUpdateTeamId = intval($forceUpdateTeamId);
        }
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
            self::OPTION_FORCE_UPDATE_TEAM_ID => [
                'help'    => 'pass the teams.id to move forcibly Read-Only status by teams.id',
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
        $this->logInfo(sprintf('start time stamp: %d (%s)',
            $startTimestamp,
            GoalousDateTime::createFromTimestamp($startTimestamp)->format('Y-m-d H:i:s')));

        // Prioritize argument
        if (!empty($this->forceUpdateTeamId)) {
            $targetTeamIds = [$this->forceUpdateTeamId];
        } else {
            $targetTeamIds = $this->Team->findTargetsForMovingReadOnly($startTimestamp, $targetTimestamp);
        }

        if (empty($targetTeamIds)) {
            $this->logInfo("Target team does not exist");
            return;
        }

        $this->logInfo(AppUtil::jsonOneLine(compact('targetTeamIds')));

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");

        $teams = $this->Team->find('all', ['conditions' => ['id' => $targetTeamIds], 'fields' => ['id','timezone']]);
        $timezoneEachTeamId = Hash::combine($teams, '{n}.Team.id', '{n}.Team.timezone');

        $updateErrTeamIds = [];
        foreach ($targetTeamIds as $teamId) {
            $currentDateTimeOfTeamTimeZone = GoalousDateTime::now()->setTimeZoneByHour($timezoneEachTeamId[$teamId]);
            // Update team's service use status
            $res = $TeamService->updateServiceUseStatus(
                $teamId,
                Enum\Team\ServiceUseStatus::READ_ONLY,
                $currentDateTimeOfTeamTimeZone->format('Y-m-d')
            );
            if (!$res) {
                $updateErrTeamIds[] = $teamId;
                continue;
            }
            // Delete cache team info
            $GlRedis->dellKeys("*current_team:team:{$teamId}");
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
