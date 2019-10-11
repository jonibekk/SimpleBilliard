<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ActionResult', 'Model');
App::uses('Comment', 'Model');
App::uses('Post', 'Model');
App::uses('Translation', 'Model');
App::uses('Team', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::import('Service', 'TranslationService');
App::import('Service', 'TeamTranslationStatusService');
App::import('Lib/Translation', 'TranslationResult');

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\Status as TranslationStatus;

class TranslationServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.action_result',
        'app.translation',
        'app.post',
        'app.comment',
        'app.mst_translation_language',
        'app.team_translation_status',
        'app.team_translation_language',
        'app.user',
        'app.team'
    ];

    public function test_canTranslate_success()
    {
        $trialTeamId = 1;
        $paidTeamId = 2;
        $translationLimit = 100;

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        // No translation
        $this->assertFalse($TranslationService->canTranslate($trialTeamId));

        // With translation
        $this->insertTranslationLanguage($trialTeamId, "en");
        $TeamTranslationStatus->createEntry($trialTeamId, $translationLimit);
        $this->assertTrue($TranslationService->canTranslate($trialTeamId));

        // Limit reached
        $TeamTranslationStatus->incrementCirclePostCount($trialTeamId, $translationLimit);
        $this->assertFalse($TranslationService->canTranslate($trialTeamId));

        // Paid team
        $Team->updatePaidPlan($paidTeamId, GoalousDateTime::now()->toDateString());

        // No translation
        $this->assertFalse($TranslationService->canTranslate($paidTeamId));

        // With translation
        $this->insertTranslationLanguage($paidTeamId, "en");
        $TeamTranslationStatus->createEntry($paidTeamId, $translationLimit);
        $this->assertTrue($TranslationService->canTranslate($paidTeamId));

        // Limit reached
        $TeamTranslationStatus->incrementCirclePostCount($paidTeamId, $translationLimit);
        $this->assertFalse($TranslationService->canTranslate($paidTeamId));

        // Read only team
        $TeamTranslationStatus->resetAllTranslationCount($paidTeamId);
        $this->assertTrue($TranslationService->canTranslate($paidTeamId));

        $Team->updateServiceStatusAndDates([$paidTeamId], Team::SERVICE_USE_STATUS_READ_ONLY);
        $this->assertFalse($TranslationService->canTranslate($paidTeamId));
    }

    public function test_eraseTranslation_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $contentType = TranslationContentType::ACTION_POST();
        $contendId = 2213;
        $language = "zh-TW";

        $Translation->createEntry($contentType, $contendId, $language);

        $this->assertTrue($Translation->hasTranslation($contentType, $contendId, $language));

        $TranslationService->eraseTranslation($contentType, $contendId, $language);

        $this->assertFalse($Translation->hasTranslation($contentType, $contendId, $language));
    }

    public function test_createTranslation_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $teamId = 1;

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 1;
        $targetLanguage = "es";

        $TeamTranslationStatus->createEntry($teamId);

        $TranslationService->createTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals('en', $Post->getById($contentId)['language']);

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(355, $translationStatus->getCirclePostUsageCount());
        $this->assertEquals(355, $translationStatus->getTotalUsageCount());

        $contentType = TranslationContentType::ACTION_POST();
        $contentId = 9;
        $targetLanguage = "ja";

        $TranslationService->createTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals('en', $Post->getById($contentId)['language']);

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(4, $translationStatus->getActionPostUsageCount());
        $this->assertEquals(359, $translationStatus->getTotalUsageCount());
    }

    public function test_createTranslationUnknownSourceLanguage_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock('la');

        $teamId = 1;

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 1;
        $targetLanguage = "es";

        $TeamTranslationStatus->createEntry($teamId);

        $TranslationService->createTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals('la', $Post->getById($contentId)['language']);

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(355, $translationStatus->getCirclePostUsageCount());
        $this->assertEquals(355, $translationStatus->getTotalUsageCount());
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_createTranslationNoSource_failure()
    {
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $contentType = TranslationContentType::ACTION_POST();
        $contendId = 2213;
        $language = "zh-TW";

        $TranslationService->createTranslation($contentType, $contendId, $language);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_createTranslationInvalidLanguage_failure()
    {
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $contentType = TranslationContentType::CIRCLE_POST();
        $contendId = 1;
        $language = 'avd';

        $TranslationService->createTranslation($contentType, $contendId, $language);
    }

    public function test_createDefaultTranslation_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $teamId = 1;

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 1;

        $TeamTranslationStatus->createEntry($teamId, 1000);
        $this->insertTranslationLanguage($teamId, "es");

        $TranslationService->createDefaultTranslation($teamId, $contentType, $contentId);

        $this->assertEquals('en', $Post->getById($contentId)['language']);
        $translation = $Translation->getTranslation($contentType, $contentId, "es");
        $this->assertNotEmpty($translation);
    }


    public function test_getTranslation_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $teamId = 1;

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 1;
        $targetLanguage = "es";

        $TeamTranslationStatus->createEntry($teamId);

        $translationResult = $TranslationService->getTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals("en", $translationResult->getSourceLanguage());
        $this->assertEquals('Esta es una muestra de traducción.', $translationResult->getTranslation());

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(355, $translationStatus->getCirclePostUsageCount());
        $this->assertEquals(355, $translationStatus->getTotalUsageCount());

        $this->assertEquals('en', $Post->getById($contentId)['language']);

        $contentType = TranslationContentType::CIRCLE_POST_COMMENT();
        $contentId = 2;
        $targetLanguage = "es";

        $translationResult = $TranslationService->getTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals("en", $translationResult->getSourceLanguage());
        $this->assertEquals('Esta es una muestra de traducción.', $translationResult->getTranslation());

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(28, $translationStatus->getCirclePostCommentUsageCount());
        $this->assertEquals(383, $translationStatus->getTotalUsageCount());

        $this->assertEquals('en', $Comment->getById($contentId)['language']);
    }

    public function test_getTranslationStuckProcessing_success()
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $teamId = 1;

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 1;
        $targetLanguage = "es";

        $TeamTranslationStatus->createEntry($teamId);

        $Translation->createEntry($contentType, $contentId, $targetLanguage);

        $translationResult = $TranslationService->getTranslation($contentType, $contentId, $targetLanguage);

        $this->assertEquals("en", $translationResult->getSourceLanguage());
        $this->assertEquals('Esta es una muestra de traducción.', $translationResult->getTranslation());

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($targetLanguage, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals('Esta es una muestra de traducción.', $translation['body']);

        $translationStatus = $TeamTranslationStatus->getUsageStatus($teamId);
        $this->assertEquals(355, $translationStatus->getCirclePostUsageCount());
        $this->assertEquals(355, $translationStatus->getTotalUsageCount());
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getTranslationNoSource_failure()
    {
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $contentType = TranslationContentType::ACTION_POST();
        $contendId = 2213;
        $language = "zh-TW";

        $TranslationService->createTranslation($contentType, $contendId, $language);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_getTranslationInvalidLanguage_failure()
    {
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $this->createTranslatorClientMock();

        $contentType = TranslationContentType::CIRCLE_POST();
        $contendId = 1;
        $language = 'avd';

        $TranslationService->getTranslation($contentType, $contendId, $language);
    }
}
