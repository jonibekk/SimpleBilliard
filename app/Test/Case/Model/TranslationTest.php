<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Translation', 'Model');

use Goalous\Enum\Language as LanguageEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\TranslationStatus as TranslationStatus;

class TranslationTest extends GoalousTestCase
{
    public $fixtures = [
        'app.translation'
    ];

    public function test_createEntry_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::ACTION_POST(), 1, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 2, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, LanguageEnum::ID));

        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), 1, LanguageEnum::MS);

        $this->assertTrue($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::ACTION_POST(), 1, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 2, LanguageEnum::MS));
        $this->assertFalse($Translation->hasTranslation(TranslationContentType::CIRCLE_POST(), 1, LanguageEnum::ID));
    }

    public function test_getTranslation_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;
        $language = LanguageEnum::DE;

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
        $language = LanguageEnum::DE;
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
        $language = LanguageEnum::DE;

        $Translation->updateTranslationBody($contentType, $contentId, $language, 'Danke');
    }

    public function test_eraseTranslation_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();
        $contentId = 202;

        $Translation->createEntry($contentType, $contentId, LanguageEnum::ES);
        $Translation->createEntry($contentType, $contentId, LanguageEnum::FR);
        $Translation->createEntry($contentType, $contentId, LanguageEnum::IT);

        $Translation->eraseTranslation($contentType, $contentId, LanguageEnum::ES);
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, LanguageEnum::ES));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, LanguageEnum::FR));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, LanguageEnum::IT));

        $Translation->eraseTranslation($contentType, $contentId, LanguageEnum::FR);
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, LanguageEnum::ES));
        $this->assertFalse($Translation->hasTranslation($contentType, $contentId, LanguageEnum::FR));
        $this->assertTrue($Translation->hasTranslation($contentType, $contentId, LanguageEnum::IT));
    }

    public function test_eraseAllTranslations_success()
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $contentType = TranslationContentType::CIRCLE_POST();

        $Translation->createEntry($contentType, 202, LanguageEnum::ES);
        $Translation->createEntry($contentType, 202, LanguageEnum::FR);
        $Translation->createEntry($contentType, 209, LanguageEnum::ES);

        $Translation->eraseAllTranslations($contentType, 202);
        $this->assertFalse($Translation->hasTranslation($contentType, 202, LanguageEnum::ES));
        $this->assertFalse($Translation->hasTranslation($contentType, 202, LanguageEnum::FR));
        $this->assertTrue($Translation->hasTranslation($contentType, 209, LanguageEnum::ES));
    }

}