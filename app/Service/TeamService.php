<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::import('Service', 'PaymentService');
App::import('Service', 'UnreadCirclePostService');
App::import('Service', 'TeamMemberService');
App::uses('NotifyBizComponent', 'Controller/Component');
App::import('Lib/DataExtender', 'TeamExtender');
App::import('Service/Request/Resource', 'TeamResourceRequest');

// FIXME: to use GlEmailComponent
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('AppController', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::uses('CampaignTeam', 'Model');
App::uses('CreditCard', 'Model');
App::uses('Invoice', 'Model');
App::uses('PricePlanPurchaseTeam', 'Model');
App::uses('UnreadCirclePost', 'Model');

use Goalous\Enum as Enum;
use Goalous\Exception as GlException;

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
     * @param int[]  $targetTeamIds
     *
     * @return bool
     */
    public function changeStatusAllTeamExpired(
        string $targetExpireDate,
        int $currentStatus,
        int $nextStatus,
        array $targetTeamIds = []
    ): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        if (empty($targetTeamIds)) {
            $targetTeamIds = $Team->findTeamIdsStatusExpired($currentStatus, $targetExpireDate);
            if (empty($targetTeamIds)) {
                return false;
            }
        }
        CakeLog::info(sprintf('update teams service status and dates: %s', AppUtil::jsonOneLine([
            'teams.ids'                    => $targetTeamIds,
            'teams.service_use_status.old' => $currentStatus,
            'teams.service_use_status.new' => $nextStatus,
            'target_expire_date'           => $targetExpireDate,
        ])));
        $ret = $Team->updateServiceStatusAndDates($targetTeamIds, $nextStatus);
        if ($ret === false) {
            $this->log(sprintf("failed to save changeStatusAllTeamFromReadonlyToCannotUseService. targetTeamList: %s",
                AppUtil::varExportOneLine($targetTeamIds)));
            $this->log(Debugger::trace());
        }

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        // delete all team cache
        foreach ($targetTeamIds as $teamId) {
            $GlRedis->dellKeys("*current_team:team:{$teamId}");

            if ($nextStatus === Enum\Model\Team\ServiceUseStatus::CANNOT_USE) {
                /** @var UnreadCirclePostService $UnreadCirclePostService */
                $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');
                $UnreadCirclePostService->deleteAllInTeam($teamId);
            }
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

        $targetTeamIds = $Team->findTeamIdsStatusExpired(
            Team::SERVICE_USE_STATUS_CANNOT_USE,
            $targetExpireDate
        );

        if (empty($targetTeamIds)) {
            return false;
        }
        GoalousLog::info('These teams are deleted automatically', compact('targetTeamIds'));
        $errorTeamIds = [];
        foreach ($targetTeamIds as $teamId) {
            if (!$this->deleteTeam($teamId)) {
                $errorTeamIds[] = $teamId;
            }
        }

        if (!empty($errorTeamIds)) {
            GoalousLog::emergency('Failed to delete team automatically', compact('errorTeamIds'));
            return false;
        }

        return true;
    }

    /**
     * Update Service Use Status
     *
     * @param int  $teamId
     * @param bool $isManualDelete
     * @param null $opeUserId
     *
     * @return bool
     */
    public function deleteTeam(int $teamId, bool $isManualDelete = false, $opeUserId = null): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        try {
            $this->TransactionManager->begin();

            // Get team data before delete
            $team = $Team->getById($teamId);
            // Get all team members before delete
            $userIds = $TeamMember->getActiveTeamMembersList(false, $teamId);

            $now = GoalousDateTime::now();
            // CakePHP updateAll trap when update date column...
            $serviceUseStateStartDate = "'" . $now->setTimeZoneByHour($team['timezone'])->format('Y-m-d') . "'";

            // Created data for deleting
            $deleteData = [
                'service_use_state_start_date' => $serviceUseStateStartDate,
                'service_use_state_end_date'   => null,
                'deleted'                      => $now->timestamp,
                'modified'                     => $now->timestamp,
                'del_flg'                      => true
            ];
            if ($isManualDelete) {
                $deleteData['service_use_status'] = Enum\Model\Team\ServiceUseStatus::DELETED_MANUAL;
                // TODO: create db migration to add this column when implement manual team deletion
//                $deleteData['ope_user_id'] = $opeUserId;
            } else {
                $deleteData['service_use_status'] = Enum\Model\Team\ServiceUseStatus::DELETED_AUTO;
            }

            // Delete team
            if (!$Team->updateAll($deleteData, ['Team.id' => $teamId])) {
                throw new Exception(sprintf('Failed to delete team. data:%s', AppUtil::jsonOneLine($deleteData)));
            }

            // Delete team member
            if (!$TeamMember->softDeleteAll(['TeamMember.team_id' => $teamId], false)) {
                throw new Exception(sprintf('Failed to delete all team members. team_id:%s', $teamId));
            }

            // Delete unread circle post cache
            /** @var UnreadCirclePostService $UnreadCirclePostService */
            $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');
            $UnreadCirclePostService->deleteAllInTeam($teamId);

            // Update team member's default team id
            $this->updateDefaultTeamOnDeletion($teamId);

            /** @var GlRedis $GlRedis */
            $GlRedis = ClassRegistry::init("GlRedis");
            // delete all team cache
            $GlRedis->dellKeys("*team:{$teamId}:*");

            $GlRedis->deleteUserTeamList($userIds);

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::emergency($e->getMessage());
            GoalousLog::emergency($e->getTraceAsString());
            return false;
        }

        // Send mail
        $GlEmail = new GlEmailComponent(new ComponentCollection());
        $GlEmail->startup(new AppController());

        foreach ($userIds as $userId) {
            if ($isManualDelete) {
                $GlEmail->sendTeamDeletedManual(
                    $userId,
                    $teamId,
                    $team['name']
                );
            } else {
                $GlEmail->sendTeamDeletedAuto(
                    $userId,
                    $teamId,
                    $team['name']
                );
            }
        }

        return true;
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

        if ($serviceUseStatus == Enum\Model\Team\ServiceUseStatus::PAID) {
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
            if ($this->getServiceUseStatusByTeamId($teamId) == Enum\Model\Team\ServiceUseStatus::PAID &&
                $serviceUseStatus == Enum\Model\Team\ServiceUseStatus::READ_ONLY) {
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

    /**
     * Change paid team status to read only
     *
     * @param string $targetDate
     */
    public function changePaidTeamToReadOnly(string $targetDate)
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init('CreditCard');
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');

        $targetTeamIds = $Team->findTeamIdsStatusExpired(Enum\Model\Team\ServiceUseStatus::PAID, $targetDate);

        if (empty($targetTeamIds)) {
            return;
        }

        try {
            $this->TransactionManager->begin();
            $res = $this->changeStatusAllTeamExpired($targetDate,
                Enum\Model\Team\ServiceUseStatus::PAID,
                Enum\Model\Team\ServiceUseStatus::READ_ONLY,
                $targetTeamIds);
            if (!$res) {
                throw new RuntimeException();
            }
            foreach ($targetTeamIds as $teamId) {
                $CampaignTeam->softDeleteAllByTeamId($teamId);
                $CreditCard->softDeleteAllByTeamId($teamId);
                $Invoice->softDeleteAllByTeamId($teamId);
                $PaymentSetting->softDeleteAllByTeamId($teamId);
                $PricePlanPurchaseTeam->softDeleteAllByTeamId($teamId);
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::emergency("Failed to expire paid teams.", [
                'target_date' => $targetDate,
                'teams'       => $targetTeamIds
            ]);
        }

    }

    /**
     * Get a team information
     *
     * @param TeamResourceRequest $request
     *
     * @return array
     */
    public function get(TeamResourceRequest $request): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamExtender $TeamExtender */
        $TeamExtender = ClassRegistry::init('TeamExtender');

        $team = $Team->getEntity($request->getId());

        return $TeamExtender->extend($team->toArray(), $request->getUserId(), $request->getTeamId(),
            [TeamExtender::EXTEND_ALL]);
    }

    /**
     * Join an user to a team
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     *
     * @throws Exception
     */
    public function joinTeam(int $userId, int $teamId): bool
    {
        try {
            $this->TransactionManager->begin();
            /** @var TeamMemberService $TeamMemberService */
            $TeamMemberService = ClassRegistry::init('TeamMemberService');
            if (!$TeamMemberService->add($userId, $teamId)) {
                throw new RuntimeException("Failed to add team member");
            }

            /** @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');
            $teamAllCircleId = $Circle->getTeamAllCircleId($teamId);

            if (empty($teamAllCircleId)) {
                throw new GlException\GoalousNotFoundException("Team does not have default circle.");
            }

            // Add to default circle
            /** @var CircleMemberService $CircleMemberService */
            $CircleMemberService = ClassRegistry::init('CircleMemberService');
            if (!$CircleMemberService->add($userId, $teamId, $teamAllCircleId)) {
                throw new RuntimeException("Failed to join circle");
            }
            $this->TransactionManager->commit();
        } catch (GlException\GoalousConflictException $e) {
            $this->TransactionManager->rollback();
            return false;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }
        return true;
    }
}
