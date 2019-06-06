<?php
App::import('Service', 'TeamTranslationStatusService');

class ResetTranslationCount extends AppShell
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

        $TeamTranslationStatusService->findPaidTeamToReset($targetTimestamp);
    }
}