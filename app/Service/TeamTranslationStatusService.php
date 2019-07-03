<?php

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\NotificationFlag\Name as NotificationFlagName;

App::import('Service', 'AppService');
App::import('Service', 'PaymentService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamTranslationUsageLog', 'Model');
App::import('Lib/Cache/Redis/NotificationFlag', 'NotificationFlagClient');
App::import('Lib/Cache/Redis/NotificationFlag', 'NotificationFlagKey');


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

        $notificationFlagClient = new NotificationFlagClient();

        foreach ($teamsToReset as $teamId) {

            $this->logAndResetTranslationStatus($teamId, $currentTimeStamp);

            $limitReachedKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_REACHED());
            $limitClosingKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_CLOSING());
            $notificationFlagClient->del($limitReachedKey);
            $notificationFlagClient->del($limitClosingKey);
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
            $startDate = $this->calculateLogStartDate($teamId)->format("Y-m-d");
            $endDate = $this->calculateLogEndDate($teamId, $currentTimeStamp)->format("Y-m-d");

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

            $paymentBaseDate = $PaymentService->getCurrentMonthBaseDate($paidTeamId, $currentTimeStamp)->format('Y-m-d');

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
                date_create($paymentBaseDate)->modify('-1 day')->format('Y-m-d');
        });

        // To reset array index
        return array_values($teamsToReset);
    }

    /**
     * Calculate log start date
     *
     * @param int $teamId
     *
     * @return GoalousDateTime
     */
    private function calculateLogStartDate(int $teamId): GoalousDateTime
    {
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $previousLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        // If it's the first log
        if (empty($previousLog)) {
            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');

            $team = $Team->getById($teamId);
            return GoalousDateTime::createFromFormat('Y-m-d', Hash::get($team, 'service_use_state_start_date'));
        }

        return GoalousDateTime::createFromFormat('Y-m-d', $previousLog['end_date'])->modify("+1 day");
    }

    /**
     * Calculate log end date
     *
     * @param int $teamId
     * @param int $currentTimeStamp
     *
     * @return GoalousDateTime
     */
    private function calculateLogEndDate(int $teamId, int $currentTimeStamp): GoalousDateTime
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

        $endDate = $PaymentService->getCurrentMonthBaseDate($teamId, $endDateTimeStamp);

        return $endDate->modify("-1 day");
    }

    /**
     * Increment translation usage count
     *
     * @param int                    $teamId
     * @param TranslationContentType $contentType
     * @param int                    $count
     *
     * @throws Exception
     */
    public function incrementUsageCount(int $teamId, TranslationContentType $contentType, int $count)
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        switch ($contentType->getValue()) {
            case TranslationContentType::CIRCLE_POST:
                $TeamTranslationStatus->incrementCirclePostCount($teamId, $count);
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
                $TeamTranslationStatus->incrementCircleCommentCount($teamId, $count);
                break;
            case TranslationContentType::ACTION_POST:
                $TeamTranslationStatus->incrementActionPostCount($teamId, $count);
                break;
            case TranslationContentType::ACTION_POST_COMMENT:
                $TeamTranslationStatus->incrementActionCommentCount($teamId, $count);
                break;
        }
    }
}