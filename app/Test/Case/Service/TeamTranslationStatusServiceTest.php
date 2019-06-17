<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamTranslationUsageLog', 'Model');
App::import('Service', 'TeamTranslationStatusService');
App::import('Service', 'PaymentService');

use Goalous\Enum\Language as LanguageEnum;

class TeamTranslationStatusServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.payment_setting',
        'app.team',
        'app.team_translation_language',
        'app.team_translation_status',
        'app.team_translation_usage_log'
    ];

    public function test_logAndResetTranslationStatusCheckLog_success()
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        GoalousDateTime::setTestNow("2019-06-14");
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();

        $teamId = 1;

        $Team->updatePaidPlan($teamId, '2019-05-25');

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 14
        ], false);

        $TeamTranslationStatus->createEntry($teamId);
        $TeamTranslationStatus->incrementCirclePostCount($teamId, 1111);
        $TeamTranslationStatus->incrementCircleCommentCount($teamId, 222);
        $TeamTranslationStatus->incrementActionPostCount($teamId, 33);
        $TeamTranslationStatus->incrementActionCommentCount($teamId, 4);

        $rawLog = $TeamTranslationStatus->exportUsageAsJson($teamId);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);

        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);

        $this->assertEquals('2019-05-25', $latestLog['start_date']);
        $this->assertEquals('2019-06-13', $latestLog['end_date']);
        $this->assertEquals($rawLog, $latestLog['translation_log']);
        $this->assertEquals(0, $TeamTranslationStatus->getTotalUsageCount($teamId));
    }

    public function test_logAndResetTranslationStatusCheckDates_success()
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        GoalousDateTime::setTestNow("2019-06-30");
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();
        $teamId = 1;

        $Team->updatePaidPlan($teamId, '2019-05-25');

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $teamId,
            'payment_base_day' => 31
        ], false);

        $TeamTranslationStatus->createEntry($teamId);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(1, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-05-25', $latestLog['start_date']);
        $this->assertEquals('2019-06-29', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(2, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-06-30', $latestLog['start_date']);
        $this->assertEquals('2019-07-30', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(3, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-07-31', $latestLog['start_date']);
        $this->assertEquals('2019-08-30', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(4, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-08-31', $latestLog['start_date']);
        $this->assertEquals('2019-09-29', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(5, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-09-30', $latestLog['start_date']);
        $this->assertEquals('2019-10-30', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(6, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-10-31', $latestLog['start_date']);
        $this->assertEquals('2019-11-29', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(7, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-11-30', $latestLog['start_date']);
        $this->assertEquals('2019-12-30', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(8, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2019-12-31', $latestLog['start_date']);
        $this->assertEquals('2020-01-30', $latestLog['end_date']);

        $TeamTranslationStatusService->logAndResetTranslationStatus($teamId, $currentTimeStamp);
        $this->assertCount(9, $TeamTranslationUsageLog->find('all'));
        $latestLog = $TeamTranslationUsageLog->getLatestLog($teamId);
        $this->assertEquals('2020-01-31', $latestLog['start_date']);
        $this->assertEquals('2020-02-28', $latestLog['end_date']);
    }

    public function test_findPaidTeamToReset_success()
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        GoalousDateTime::setTestNow("2019-06-14");
        $paidTeamWithTranslation = 1;
        $paidTeamWithoutTranslation = 2;
        $paidTeamWithTranslationAndLog = 3;
        $trialTeamWithTranslation = 6;
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();

        $Team->updatePaidPlan($paidTeamWithTranslation, '2019-05-14');
        $Team->updatePaidPlan($paidTeamWithoutTranslation, '2019-05-14');
        $Team->updatePaidPlan($paidTeamWithTranslationAndLog, '2019-05-14');

        $queryResult = $TeamTranslationStatusService->findPaidTeamIdsToReset($currentTimeStamp);
        $this->assertEmpty($queryResult);

        $this->insertTranslationLanguage($paidTeamWithTranslation, LanguageEnum::DE());
        $this->insertTranslationLanguage($paidTeamWithTranslationAndLog, LanguageEnum::ZH_TW());
        $this->insertTranslationLanguage($trialTeamWithTranslation, LanguageEnum::ES());

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $paidTeamWithTranslation,
            'payment_base_day' => 14,
        ], false);
        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $paidTeamWithoutTranslation,
            'payment_base_day' => 14,
        ], false);
        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $paidTeamWithTranslationAndLog,
            'payment_base_day' => 14,
        ], false);

        $queryResult = $TeamTranslationStatusService->findPaidTeamIdsToReset($currentTimeStamp);
        $this->assertEquals([$paidTeamWithTranslation, $paidTeamWithTranslationAndLog], $queryResult);

        $PaymentSetting->updateAll(['payment_base_day' => 15], ['team_id' => $paidTeamWithTranslation]);

        $queryResult = $TeamTranslationStatusService->findPaidTeamIdsToReset($currentTimeStamp);
        $this->assertEquals([$paidTeamWithTranslationAndLog], $queryResult);

        $TeamTranslationUsageLog->saveLog($paidTeamWithTranslationAndLog, '2019-05-14', '2019-06-13', "{}");

        $queryResult = $TeamTranslationStatusService->findPaidTeamIdsToReset($currentTimeStamp);
        $this->assertEmpty($queryResult);

        GoalousDateTime::setTestNow("2019-06-15");
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();

        $queryResult = $TeamTranslationStatusService->findPaidTeamIdsToReset($currentTimeStamp);
        $this->assertEquals([$paidTeamWithTranslation], $queryResult);
    }

    public function test_resetTranslationStatusInPaidTeams_success()
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');
        /** @var TeamTranslationUsageLog $TeamTranslationUsageLog */
        $TeamTranslationUsageLog = ClassRegistry::init('TeamTranslationUsageLog');

        GoalousDateTime::setTestNow("2019-06-14");
        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();

        $teamWithMatchingDate = 1;
        $teamWithDifferentDate = 2;
        $teamWithMatchingDateAndLog = 3;

        $Team->updatePaidPlan($teamWithMatchingDate, '2019-05-25');
        $Team->updatePaidPlan($teamWithDifferentDate, '2019-05-26');
        $Team->updatePaidPlan($teamWithMatchingDateAndLog, '2019-05-27');

        $this->insertTranslationLanguage($teamWithMatchingDate, LanguageEnum::DE());
        $this->insertTranslationLanguage($teamWithDifferentDate, LanguageEnum::ZH_TW());
        $this->insertTranslationLanguage($teamWithMatchingDateAndLog, LanguageEnum::ES());

        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $teamWithMatchingDate,
            'payment_base_day' => 14,
        ], false);
        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $teamWithDifferentDate,
            'payment_base_day' => 15,
        ], false);
        $PaymentSetting->create();
        $PaymentSetting->save([
            'team_id'          => $teamWithMatchingDateAndLog,
            'payment_base_day' => 14,
        ], false);

        $TeamTranslationStatus->createEntry($teamWithMatchingDate);
        $TeamTranslationStatus->incrementCirclePostCount($teamWithMatchingDate, 1111);
        $TeamTranslationStatus->incrementCircleCommentCount($teamWithMatchingDate, 222);
        $TeamTranslationStatus->incrementActionPostCount($teamWithMatchingDate, 33);
        $TeamTranslationStatus->incrementActionCommentCount($teamWithMatchingDate, 4);
        $rawLog1 = $TeamTranslationStatus->exportUsageAsJson($teamWithMatchingDate);

        $TeamTranslationStatus->createEntry($teamWithDifferentDate);
        $TeamTranslationStatus->incrementCirclePostCount($teamWithDifferentDate, 123);
        $TeamTranslationStatus->incrementCircleCommentCount($teamWithDifferentDate, 234);
        $TeamTranslationStatus->incrementActionPostCount($teamWithDifferentDate, 775);
        $TeamTranslationStatus->incrementActionCommentCount($teamWithDifferentDate, 589);
        $rawLog2 = $TeamTranslationStatus->exportUsageAsJson($teamWithDifferentDate);

        $TeamTranslationStatus->createEntry($teamWithMatchingDateAndLog);
        $TeamTranslationStatus->incrementCirclePostCount($teamWithMatchingDateAndLog, 86575);
        $TeamTranslationStatus->incrementCircleCommentCount($teamWithMatchingDateAndLog, 45);
        $TeamTranslationStatus->incrementActionPostCount($teamWithMatchingDateAndLog, 34735);
        $TeamTranslationStatus->incrementActionCommentCount($teamWithMatchingDateAndLog, 34);
        $rawLog3 = $TeamTranslationStatus->exportUsageAsJson($teamWithMatchingDateAndLog);

        $TeamTranslationUsageLog->saveLog($teamWithMatchingDateAndLog, '2019-05-27', '2019-06-13', $rawLog3);

        $TeamTranslationStatusService->resetTranslationStatusInPaidTeams($currentTimeStamp);

        $this->assertCount(2, $TeamTranslationUsageLog->find('all'));

        $currentTimeStamp += DAY;
        $TeamTranslationStatusService->resetTranslationStatusInPaidTeams($currentTimeStamp);

        $this->assertCount(3, $TeamTranslationUsageLog->find('all'));

        $log = $TeamTranslationUsageLog->getLatestLog($teamWithMatchingDate);
        $this->assertEquals('2019-05-25', $log['start_date']);
        $this->assertEquals('2019-06-13', $log['end_date']);
        $this->assertEquals($rawLog1, $log['translation_log']);

        $log = $TeamTranslationUsageLog->getLatestLog($teamWithDifferentDate);
        $this->assertEquals('2019-05-26', $log['start_date']);
        $this->assertEquals('2019-06-14', $log['end_date']);
        $this->assertEquals($rawLog2, $log['translation_log']);

        $log = $TeamTranslationUsageLog->getLatestLog($teamWithMatchingDateAndLog);
        $this->assertEquals('2019-05-27', $log['start_date']);
        $this->assertEquals('2019-06-13', $log['end_date']);
        $this->assertEquals($rawLog3, $log['translation_log']);
    }
}