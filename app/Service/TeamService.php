<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');

/**
 * Class TeamService
 */
class TeamService extends AppService
{

    function add(array $data, int $userId)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        try {
            $Team->begin();

            if (!$Team->add($data, $userId)) {
                throw new Exception(sprintf("Failed to create team. data: %s userId: %s",
                    var_export($data, true), $userId));
            }
            $teamId = $Team->getLastInsertID();

            // save current & next & next next term
            $nextStartDate = date('Y-m-01', strtotime($data['Team']['next_start_ym']));
            $termRange = $data['Team']['border_months'];
            $currentStartDate = date('Y-m-01');
            if (!$Term->createInitialDataAsSignup($currentStartDate, $nextStartDate, $termRange, $teamId)) {
                throw new Exception(sprintf("Failed to create term. data: %s teamId: %s userId: %s",
                    var_export($data, true), $teamId, $userId));
            }

            $Team->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Team->rollback();
            return false;
        }

        return true;
    }

    /**
     * get team service use status
     * # Warning
     * - In Team::getCurrentTeam, use CACHE_KEY_CURRENT_TEAM cache.
     * - So when change service use status, must delete this team cache.
     *
     * @return int
     */
    public function getServiceUseStatus(): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $team = $Team->getCurrentTeam();
        return $team['Team']['service_use_status'];
    }

    /**
     * get team end of read only date
     * # Warning
     * - In Team::getCurrentTeam, use CACHE_KEY_CURRENT_TEAM cache.
     * - So when change service use status, must delete this team cache.
     *
     * @return string
     */
    public function getReadOnlyEndDate(): string
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $team = $Team->getCurrentTeam();
        $freeTrialStartDate = $team['Team']['service_use_state_start_date'];
        $readOnlyDays = Team::DAYS_SERVICE_USE_STATUS[Team::SERVICE_USE_STATUS_READ_ONLY];

        $readOnlyEndDate = AppUtil::dateAfter($freeTrialStartDate, $readOnlyDays);
        return $readOnlyEndDate;
    }

    public function isCannotUseService(): bool
    {
        return $this->getServiceUseStatus() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }

    /**
     * find `team.service_use_state_start_date` of status expired team
     *
     * @param int    $serviceStatus
     * @param string $targetExpiredDate
     *
     * @return array ['team_id'=>'service_use_state_start_date',...]
     */
    public function findServiceStatusExpiredTeamList(int $serviceStatus, string $targetExpiredDate)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $statusDays = Team::DAYS_SERVICE_USE_STATUS[$serviceStatus];
        $targetStartUseService = AppUtil::dateBefore($targetExpiredDate, $statusDays);
        $ret = $Team->findTeamListByStartStatusOrLess($serviceStatus, $targetStartUseService);
        return $ret;
    }

    /**
     * changing service status from Read-only to Cannot-use-service
     *
     * @param string $targetExpireDate
     *
     * @return bool
     */
    public function changeStatusAllTeamFromReadonlyToCannotUseService(string $targetExpireDate): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $targetTeamList = $this->findServiceStatusExpiredTeamList(Team::SERVICE_USE_STATUS_READ_ONLY,
            $targetExpireDate);
        if (empty($targetTeamList)) {
            return false;
        }
        $ret = $Team->updateServiceStatus(Team::SERVICE_USE_STATUS_CANNOT_USE, $targetTeamList);

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        // delete all team cache
        foreach ($targetTeamList as $teamId) {
            $GlRedis->dellKeys("*current_team:team:{$teamId}");
        }

        return $ret;
    }

    /**
     * deleting expired team that status is cannot-use-service
     *
     * @param string $targetExpireDate
     *
     * @return bool
     */
    public function deleteTeamCannotUseServiceExpired(string $targetExpireDate): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $targetTeamList = $this->findServiceStatusExpiredTeamList(
            Team::SERVICE_USE_STATUS_CANNOT_USE,
            $targetExpireDate
        );
        if (empty($targetTeamList)) {
            return false;
        }

        $ret = $Team->softDeleteAll(['Team.id' => $targetTeamList], false);

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        // delete all team cache
        foreach ($targetTeamList as $teamId) {
            $GlRedis->dellKeys("*current_team:team:{$teamId}");
        }

        return $ret;
    }
}
