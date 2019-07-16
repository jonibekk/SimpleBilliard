<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamTranslationLanguage', 'Model');

use Goalous\Enum\Language as LanguageEnum;

/**
 * TeamTranslationLanguage model test case
 *
 * Class TeamTranslationLanguageTest
 */
class TeamTranslationLanguageTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_translation_language',
        'app.mst_translation_language'
    ];

    public function test_canTranslate_success()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $queryResult = $TeamTranslationLanguage->canTranslate(1);
        $this->assertFalse($queryResult);

        $this->insertTranslationLanguage($teamId, LanguageEnum::EN());

        $queryResult = $TeamTranslationLanguage->canTranslate(1);
        $this->assertTrue($queryResult);
    }

    public function test_getLanguagesByTeam_success()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        $this->insertTranslationLanguage($teamId, LanguageEnum::EN());
        $this->insertTranslationLanguage($teamId, LanguageEnum::JA());

        $queryResult = $TeamTranslationLanguage->getLanguagesByTeam(1);
        $this->assertCount(2, $queryResult);

        foreach ($queryResult as $teamLang) {
            $this->assertNotEmpty($teamLang['language']);
        }
    }

    public function test_getAllTeams_success()
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $this->insertTranslationLanguage(1, LanguageEnum::EN());
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(1, $queryResult);
        $this->assertEquals([1], $queryResult);

        $this->insertTranslationLanguage(1, LanguageEnum::JA());
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(1, $queryResult);
        $this->assertEquals([1], $queryResult);

        $this->insertTranslationLanguage(2, LanguageEnum::DE());
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(2, $queryResult);
        $this->assertEquals([1, 2], $queryResult);
    }

    public function test_supportTranslationLanguage_success()
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $teamId = 1;
        $language1 = LanguageEnum::DE();
        $language2 = LanguageEnum::TH();

        $this->assertFalse($TeamTranslationLanguage->supportTranslationLanguage($teamId, $language1->getValue()));
        $this->assertFalse($TeamTranslationLanguage->supportTranslationLanguage($teamId, $language2->getValue()));

        $this->insertTranslationLanguage($teamId, $language1);

        $this->assertTrue($TeamTranslationLanguage->supportTranslationLanguage($teamId, $language1->getValue()));
        $this->assertFalse($TeamTranslationLanguage->supportTranslationLanguage($teamId, $language2->getValue()));
    }
}