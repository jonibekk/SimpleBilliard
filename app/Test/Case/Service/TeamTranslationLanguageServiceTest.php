<?php

App::uses('GoalousTestCase', 'Test');
App::uses('Team', 'Model');
App::import('Service', 'TeamTranslationLanguageService');

class TeamTranslationLanguageServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_translation_language',
        'app.mst_translation_language'
    ];

    public function test_getAllLanguages_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $result = $TeamTranslationLanguageService->getAllLanguages($teamId);
        $this->assertCount(3, $result);

        foreach ($result as $langCode => $langName) {
            $this->assertTrue($TranslationLanguage->isValidLanguage($langCode));
            $this->assertNotEmpty($langName);
        }
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getAllLanguagesNoLanguage_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $TeamTranslationLanguageService->getAllLanguages($teamId);
    }

    public function test_calculateDefaultTranslationLanguage_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "zh-CN");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $calcResult = $TeamTranslationLanguageService->calculateDefaultTranslationLanguage($teamId);

        $this->assertEquals("ja", $calcResult);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_calculateDefaultTranslationLanguageNoLanguage_failure()
    {
        $teamId = 1;
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $TeamTranslationLanguageService->calculateDefaultTranslationLanguage($teamId);
    }

    public function test_getDefaultTranslationLanguage_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "zh-CN");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals("ja", array_keys($defaultLanguage)[0]);

        $Team->setDefaultTranslationLanguage($teamId, "de");
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals("de", array_keys($defaultLanguage)[0]);

        $Team->setDefaultTranslationLanguage($teamId, "th");
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals("ja", array_keys($defaultLanguage)[0]);
    }

    public function test_getDefaultTranslationLanguageCode_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "zh-CN");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguageCode($teamId);
        $this->assertEquals("ja", $defaultLanguage);

        $Team->setDefaultTranslationLanguage($teamId, "de");
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguageCode($teamId);
        $this->assertEquals("de", $defaultLanguage);

        $Team->setDefaultTranslationLanguage($teamId, "th");
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguageCode($teamId);
        $this->assertEquals("ja", $defaultLanguage);
    }


    public function test_getDefaultLanguageFromEmpty_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "th");
        $this->insertTranslationLanguage($teamId, "id");
        $this->insertTranslationLanguage($teamId, "zh-TW");

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);

        $this->assertEquals("zh-TW", array_keys($defaultLanguage)[0]);
    }


    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getDefaultLanguageNoLanguage_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
    }

    public function test_selectFirstSupportedLanguage_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "id");
        $this->insertTranslationLanguage($teamId, "zh-TW");

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        //Valid language
        $language = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId, ["es", "de", "en"]);
        $this->assertEquals("en", $language);
        $language = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId, ["qq", "id", "ww"]);
        $this->assertEquals("id", $language);
        $language = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId, ["id", "en", "zh-TW"]);
        $this->assertEquals("id", $language);

        //Containing country
        $language = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId, ["qq", "qw", "en-US"]);
        $this->assertEquals("en", $language);

        //Valid localized language
        $language = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId, ["qq", "zh-TW", "en"]);
        $this->assertEquals("zh-TW", $language);
    }
}
