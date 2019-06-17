<?php
App::import('Service', 'AppService');
App::import('Service', 'PaymentService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamTranslationUsageLog', 'Model');


class TeamTranslationStatusService extends AppService
{
    /**
     * Reset translation status in paid teams, based on their payment base date
     *
     * @param int $currentTimeStamp
     *
     * @throws Exception
     */
    public function resetTranslationStatusInPaidTeams(int $currentTimeStamp)
    {
        $teamsToReset = $this->findPaidTeamIdsToReset($currentTimeStamp);

        if (empty($teamsToReset)) {
            return;
        }

        foreach ($teamsToReset as $teamId) {
            $this->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        }
    }

    /**
     * Log translation usage of a team and reset it
     *
     * @param int $teamId
     * @param int $currentTimeStamp
     *
     * @throws Exception
     */
    public function logAndResetTranslationStatus(int $teamId, int $currentTimeStamp)
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        try {
            $this->TransactionManager->begin();
            $startDate = $this->calculateLogStartDate($teamId);
            $endDate = $this->calculateLogEndDate($teamId, $currentTimeStamp);

            $translationLog = $TeamTranslationStatus->exportUsageAsJson($teamId);

            $TeamTranslationUsageLog->saveLog(
                $teamId,
                $startDate,
                $endDate,
                $translationLog
            );

            $TeamTranslationStatus->resetAllTranslationCount($teamId);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to reset translation status.', [
                'exception_msg'   => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'team_id'         => $teamId
            ]);
        }
    }

    /**
     * Find paid teams to reset their translation status
     *
     * @param int $currentTimeStamp
     *
     * @return int[] Team Ids
     */
    public function findPaidTeamIdsToReset(int $currentTimeStamp): array
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $teamIdsWithTranslation = $TeamTranslationLanguage->getAllTeamIds();

        $paidTeamIds = $Team->filterPaidTeam($teamIdsWithTranslation);

        // Get teams with payment base date of given timestamp, without any log for previous interval
        $teamsToReset = array_filter($paidTeamIds, function ($paidTeamId) use ($PaymentService, $Team, $TeamTranslationUsageLog, $currentTimeStamp) {

            $paymentBaseDate = $PaymentService->getCurrentMonthBaseDate($paidTeamId, $currentTimeStamp);

            $teamTimezone = Hash::get($Team->getById($paidTeamId), 'Team.timezone');
            $localCurrentTs = $currentTimeStamp + ($teamTimezone * HOUR);

            if ($paymentBaseDate != AppUtil::dateYmd($localCurrentTs)) {
                return false;
            }

            $latestLog = $TeamTranslationUsageLog->getLatestLog($paidTeamId);

            if (empty($latestLog)) {
                return true;
            }

            return $latestLog['end_date'] !=
                date_create($paymentBaseDate)->modify('-1 day')->format('Y-m-d');;
        });

        // To reset array index
        return array_values($teamsToReset);
    }

    /**
     * Calculate log start date
     *
     * @param int $teamId
     *
     * @return string
     */
    private function calculateLogStartDate(int $teamId): string
    {
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $previousLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        // If it's the first log
        if (empty($previousLog)) {
            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');

            $team = $Team->getById($teamId);

            return Hash::get($team, 'service_use_state_start_date');
        }

        return date_create($previousLog['end_date'])->modify('+1 day')->format('Y-m-d');
    }

    /**
     * Calculate log end date
     *
     * @param int $teamId
     * @param int $currentTimeStamp
     *
     * @return string
     */
    private function calculateLogEndDate(int $teamId, int $currentTimeStamp): string
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $previousLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        $endDateTimeStamp = $currentTimeStamp;

        // If it's not the first log
        if (!empty($previousLog)) {
            $endDateTimeStamp = strtotime($previousLog['end_date']) + MONTH;
        }

        return date_create($PaymentService->getCurrentMonthBaseDate($teamId, $endDateTimeStamp))
            ->modify('-1 day')->format('Y-m-d');
    }

}