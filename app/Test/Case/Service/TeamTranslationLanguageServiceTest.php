<?php

App::uses('GoalousTestCase', 'Test');
App::uses('Team', 'Model');
App::import('Service', 'TeamTranslationLanguageService');

use Goalous\Enum\Language as LanguageEnum;

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

        $this->insertTranslationLanguage($teamId, LanguageEnum::EN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());
        $this->insertTranslationLanguage($teamId, LanguageEnum::DE());

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $result = $TeamTranslationLanguageService->getAllLanguages($teamId);
        $this->assertCount(3, $result);

        foreach ($result as $langCode => $langName) {
            $this->assertTrue(LanguageEnum::isValid($langCode));
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

        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_CN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());
        $this->insertTranslationLanguage($teamId, LanguageEnum::DE());

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $calcResult = $TeamTranslationLanguageService->calculateDefaultTranslationLanguage($teamId);

        $this->assertEquals(LanguageEnum::JA, $calcResult);
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

        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_CN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());
        $this->insertTranslationLanguage($teamId, LanguageEnum::DE());

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals(LanguageEnum::JA, array_keys($defaultLanguage)[0]);

        $Team->setDefaultTranslationLanguage($teamId, LanguageEnum::DE);
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals(LanguageEnum::DE, array_keys($defaultLanguage)[0]);

        $Team->setDefaultTranslationLanguage($teamId, LanguageEnum::TH);
        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);
        $this->assertEquals(LanguageEnum::JA, array_keys($defaultLanguage)[0]);

    }


    public function test_getDefaultLanguageFromEmpty_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, LanguageEnum::TH());
        $this->insertTranslationLanguage($teamId, LanguageEnum::ID());
        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_TW());

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($teamId);

        $this->assertEquals(LanguageEnum::ZH_TW, array_keys($defaultLanguage)[0]);
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
}