<?php
App::import('Service', 'TeamTranslationStatusService');

/**
 * Reset translation usage for paid teams with translation feature enabled.
 * Reset date is team's payment base date.
 *
 * Class ResetTranslationCountShell
 */
class ResetTranslationCountShell extends AppShell
{
    /**
     * Entry point of the Shell
     */
    public function main()
    {
        $targetTimestamp = GoalousDateTime::now()->getTimestamp();
        $this->logInfo(sprintf('target time stamp: %d (%s)',
            $targetTimestamp,
            GoalousDateTime::createFromTimestamp($targetTimestamp)->format('Y-m-d H:i:s')));

        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');

        try {
            $TeamTranslationStatusService->resetTranslationStatusInPaidTeams($targetTimestamp);
        } catch (Exception $exception) {
            GoalousLog::error('Failed to reset translation usage', [
                'target_timestamp' => $targetTimestamp,
                'message'          => $exception->getMessage(),
                'trace'            => $exception->getTraceAsString()
            ]);
        }
    }
}