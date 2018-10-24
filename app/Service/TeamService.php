<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::import('Service', 'PaymentService');

use Goalous\Model\Enum as Enum;

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
     * Delete team CACHE_KEY_CURRENT_TEAM
     *
     * @param int $teamId
     */
    function deleteTeamCache(int $teamId)
    {
        Cache::delete(CACHE_KEY_CURRENT_TEAM . ":team:" . $teamId, 'team_info');
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
     * Get team service user status
     *
     * @param int $teamId
     *
     * @return int
     */
    public function getServiceUseStatusByTeamId(int $teamId): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $condition = [
            'conditions' => [
                'id'      => $teamId,
                'del_flg' => [true, false]
            ],
            'fields'     => [
                'service_use_status'
            ]
        ];
        $team = $Team->find('first', $condition);

        return $team['Team']['service_use_status'];
    }

    /**
     * get team end state date
     * # Warning
     * - In Team::getCurrentTeam, use CACHE_KEY_CURRENT_TEAM cache.
     * - So when change service use status, must delete this team cache.
     *
     * @return string|null
     */
    public function getStateEndDate()
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $team = $Team->getCurrentTeam();
        return Hash::get($team, 'Team.service_use_state_end_date');
    }

    public function isCannotUseService(): bool
    {
        return $this->getServiceUseStatus() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }

    /**
     * changing service status expired teams
     *
     * @param string $targetExpireDate
     * @param int    $currentStatus
     * @param int    $nextStatus
     *
     * @return bool
     */
    public function changeStatusAllTeamExpired(string $targetExpireDate, int $currentStatus, int $nextStatus): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $targetTeamList = $Team->findTeamListStatusExpired($currentStatus, $targetExpireDate);
        if (empty($targetTeamList)) {
            return false;
        }
        CakeLog::info(sprintf('update teams service status and dates: %s', AppUtil::jsonOneLine([
            'teams.ids'                    => array_values($targetTeamList),
            'teams.service_use_status.old' => $currentStatus,
            'teams.service_use_status.new' => $nextStatus,
            'target_expire_date'           => $targetExpireDate,
        ])));
        $ret = $Team->updateServiceStatusAndDates($targetTeamList, $nextStatus);
        if ($ret === false) {
            $this->log(sprintf("failed to save changeStatusAllTeamFromReadonlyToCannotUseService. targetTeamList: %s",
                AppUtil::varExportOneLine($targetTeamList)));
            $this->log(Debugger::trace());
        }

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

        $targetTeamList = $Team->findTeamListStatusExpired(
            Team::SERVICE_USE_STATUS_CANNOT_USE,
            $targetExpireDate
        );
        if (empty($targetTeamList)) {
            return false;
        }

        try {
            $this->TransactionManager->begin();
            CakeLog::info(sprintf('delete teams service status expired: %s', AppUtil::jsonOneLine([
                'teams.ids'          => array_values($targetTeamList),
                'target_expire_date' => $targetExpireDate,
            ])));
            $ret = $Team->softDeleteAll(['Team.id' => $targetTeamList], false);
            if ($ret === false) {
                $this->log(sprintf("failed to save deleteTeamCannotUseServiceExpired. targetTeamList: %s",
                    AppUtil::varExportOneLine($targetTeamList)));
                $this->log(Debugger::trace());
            }
            foreach ($targetTeamList as $team) {
                $this->updateDefaultTeamOnDeletion($team);
            }
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            throw $exception;
        }

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        // delete all team cache
        foreach ($targetTeamList as $teamId) {
            $GlRedis->dellKeys("*current_team:team:{$teamId}");
        }

        return $ret;
    }

    /**
     * Update Service Use Status
     *
     * @param int    $teamId
     * @param int    $serviceUseStatus
     * @param string $startDate
     *
     * @return bool
     */
    public function updateServiceUseStatus(int $teamId, int $serviceUseStatus, string $startDate): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        if ($serviceUseStatus == Enum\Team\ServiceUseStatus::PAID) {
            $endDate = null;
        } else {
            $statusDays = Team::DAYS_SERVICE_USE_STATUS[$serviceUseStatus];
            $endDate = AppUtil::dateAfter($startDate, $statusDays);
        }

        $data = [
            'id'                           => $teamId,
            'service_use_status'           => $serviceUseStatus,
            'service_use_state_start_date' => "'$startDate'",
            'service_use_state_end_date'   => $endDate ? "'$endDate'" : null,
            'modified'                     => GoalousDateTime::now()->getTimestamp(),
        ];
        $condition = [
            'Team.id' => $teamId,
        ];

        try {
            $this->TransactionManager->begin();

            // Delete all payment data only when changing from PAID to READ_ONLY
            if ($this->getServiceUseStatusByTeamId($teamId) == Enum\Team\ServiceUseStatus::PAID &&
                $serviceUseStatus == Enum\Team\ServiceUseStatus::READ_ONLY) {
                /** @var PaymentService $PaymentService */
                $PaymentService = ClassRegistry::init('PaymentService');
                if (!$PaymentService->deleteTeamsAllPaymentSetting($teamId)) {
                    throw new Exception("Failed to update service status for team_id: $teamId");
                }
            }

            if (!$Team->updateAll($data, $condition)) {
                throw new Exception(sprintf("Failed update Team use status. data: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($data),
                    AppUtil::varExportOneLine($Team->validationErrors)));
            }
            $this->deleteTeamCache($teamId);

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();

            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());

            return false;
        }
        return true;
    }

    /**
     * Get given team timezone.
     * It will return null in case of the error on the query.
     *
     * @param int $teamId
     *
     * @return int|null
     */
    public function getTeamTimezone(int $teamId)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        try {
            $team = $Team->findById($teamId);
            return Hash::get($team, 'Team.timezone');
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

            return null;
        }
    }

    /**
     * Update the default_team_id if the previous one is being deleted
     *
     * @param int $oldTeamId
     *
     * @return bool
     */
    public function updateDefaultTeamOnDeletion(int $oldTeamId): bool
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $userSearchCondition = [
            'conditions' => [
                'User.default_team_id' => $oldTeamId
            ],
            'fields'     => [
                'User.id'
            ]
        ];

        $userList = $User->find('all', $userSearchCondition);

        foreach ($userList as $user) {
            $userId = $user['User']['id'];

            //If user doesn't have next active item, set the default team id to null
            $newTeamId = $TeamMember->getLatestLoggedInActiveTeamId($userId) ?: null;

            $User->updateDefaultTeam($newTeamId, true, $userId);
        }

        return true;

    }
}
