<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamTranslationUsageLog', 'Model');

class TeamTranslationUsageLogTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team_translation_usage_log'
    ];

    public function test_saveAndGetLog_success()
    {
        $teamId = 1;

        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $log = [
            'something' => 1
        ];

        $TeamTranslationUsageLog->saveLog($teamId, '2019-01-01', '2019-01-31', json_encode($log));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        $this->assertEquals($log, json_decode($latestLog['translation_log'], true));

        $log1 = [
            'something_1' => 2
        ];

        $TeamTranslationUsageLog->saveLog($teamId, '2019-02-01', '2019-02-28', json_encode($log1));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        $this->assertEquals($log1, json_decode($latestLog['translation_log'], true));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_saveLogInvalidJson_failure()
    {
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        $TeamTranslationUsageLog->saveLog(1, '2019-01-01', '2019-01-31', "{\"\"}");
    }
}