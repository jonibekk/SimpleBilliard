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

        foreach ($result as $singleResult) {
            $this->assertNotEmpty($singleResult['language']);
            $this->assertNotEmpty($singleResult['language_name']);
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

    public function test_calculateDefaultLanguage_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_CN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());
        $this->insertTranslationLanguage($teamId, LanguageEnum::DE());

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $calcResult = $TeamTranslationLanguageService->calculateDefaultLanguage($teamId);

        $this->assertEquals(LanguageEnum::JA, $calcResult);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_calculateDefaultLanguageNoLanguage_failure()
    {
        $teamId = 1;
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $TeamTranslationLanguageService->calculateDefaultLanguage($teamId);
    }

    public function test_getDefaultLanguage_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_CN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());
        $this->insertTranslationLanguage($teamId, LanguageEnum::DE());

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $Team->setDefaultTranslationLanguage($teamId, LanguageEnum::DE);

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultLanguage($teamId);

        $this->assertEquals(LanguageEnum::DE, $defaultLanguage['language']);
    }


    public function test_getDefaultLanguageFromEmpty_success()
    {
        $teamId = 1;

        $this->insertTranslationLanguage($teamId, LanguageEnum::TH());
        $this->insertTranslationLanguage($teamId, LanguageEnum::ID());
        $this->insertTranslationLanguage($teamId, LanguageEnum::ZH_TW());

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $defaultLanguage = $TeamTranslationLanguageService->getDefaultLanguage($teamId);

        $this->assertEquals(LanguageEnum::ZH_TW, $defaultLanguage['language']);
    }


    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getDefaultLanguageNoLanguage_failure()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        $TeamTranslationLanguageService->getDefaultLanguage($teamId);
    }
}