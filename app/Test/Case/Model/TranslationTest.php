<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Translation', 'Model');

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\Status as TranslationStatus;

class TranslationTest extends GoalousTestCase
{
    public $fixtures = [
        'app.translation'
    ];

    public function test_createEntry_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::ACTION_POST(), 1, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 2, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, "id"));

        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), 1, "ms");

        $this->assertTrue($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::ACTION_POST(), 1, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 2, "ms"));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, "id"));
    }

    public function test_getTranslation_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;
        $language = "de";

        $this->assertEmpty($Translation->getTranslation($contentType, $contentId, $language));

        $Translation->createEntry($contentType, $contentId, $language);

        $translation = $Translation->getTranslation($contentType, $contentId, $language);
        $this->assertTrue($translation instanceof TranslationEntity);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($language, $translation['language']);
        $this->assertEquals(TranslationStatus::PROCESSING, $translation['status']);
    }

    public function test_updateTranslationBody_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;
        $language = "de";
        $body = "Danke";

        $Translation->createEntry($contentType, $contentId, $language);
        $Translation->updateTranslationBody($contentType, $contentId, $language, $body);

        $translation = $Translation->getTranslation($contentType, $contentId, $language);
        $this->assertTrue($translation instanceof TranslationEntity);
        $this->assertEquals($contentType->getValue(), $translation['content_type']);
        $this->assertEquals($contentId, $translation['content_id']);
        $this->assertEquals($language, $translation['language']);
        $this->assertEquals(TranslationStatus::DONE, $translation['status']);
        $this->assertEquals($body, $translation['body']);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_updateTranslationBodyNoEntry_failed()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;
        $language = "de";

        $Translation->updateTranslationBody($contentType, $contentId, $language, 'Danke');
    }

    public function test_eraseTranslation_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;

        $Translation->createEntry($contentType, $contentId, "es");
        $Translation->createEntry($contentType, $contentId, "fr");
        $Translation->createEntry($contentType, $contentId, "it");

        $Translation->eraseTranslation($contentType, $contentId, "es");
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, "es"));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, "fr"));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, "it"));

        $Translation->eraseTranslation($contentType, $contentId, "fr");
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, "es"));
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, "fr"));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, "it"));
    }

    public function test_eraseAllTranslations_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();

        $Translation->createEntry($contentType, 202, "es");
        $Translation->createEntry($contentType, 202, "fr");
        $Translation->createEntry($contentType, 209, "es");

        $Translation->eraseAllTranslations($contentType, 202);
        $this->assertFalse($Translation->hasTranslation($contentType, 202, "es"));
        $this->assertFalse($Translation->hasTranslation($contentType, 202, "fr"));
        $this->assertTrue($Translation->hasTranslation($contentType, 209, "es"));
    }

}
