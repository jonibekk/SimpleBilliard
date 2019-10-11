<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TranslationLanguage', 'Model');

/**
 * Class TranslationLanguageTest
 */
class TranslationLanguageTest extends GoalousTestCase
{
    public $fixtures = [
        'app.mst_translation_language'
    ];

    public function test_getAllLanguages_success()
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $result = $TranslationLanguage->getAllLanguages();

        $this->assertCount(11, $result);
        foreach ($result as $lang) {
            $this->assertNotEmpty($lang['language']);
            $this->assertNotEmpty($lang['importance']);
            $this->assertNotEmpty($lang['intl_name']);
            $this->assertNotEmpty($lang['local_name']);
        }
    }

    public function test_getLanguage_success()
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $queryResult = $TranslationLanguage->getLanguage("en");

        $this->assertEquals('en', $queryResult['language']);
        $this->assertEquals('English', $queryResult['intl_name']);
        $this->assertEquals('English', $queryResult['local_name']);
    }

    public function test_getLanguageByCode_success()
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $queryResult = $TranslationLanguage->getLanguageByCode("en");

        $this->assertEquals('en', $queryResult['language']);
        $this->assertEquals('English', $queryResult['intl_name']);
        $this->assertEquals('English', $queryResult['local_name']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_getLanguageCodeNotSupported_failure()
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $TranslationLanguage->getLanguageByCode('xx');
    }

    public function test_getMostImportantLanguage_success()
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $queryResult = $TranslationLanguage->getMostImportantLanguage(['en', 'ja', 'es']);
        $this->assertEquals('en', $queryResult['language']);

        $queryResult = $TranslationLanguage->getMostImportantLanguage(['ja', 'de']);
        $this->assertEquals('ja', $queryResult['language']);
    }
}
