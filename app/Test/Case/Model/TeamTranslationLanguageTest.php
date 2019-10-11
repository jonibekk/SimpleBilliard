<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamTranslationLanguage', 'Model');

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

    public function test_hasLanguage_success()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $queryResult = $TeamTranslationLanguage->hasLanguage(1);
        $this->assertFalse($queryResult);

        $this->insertTranslationLanguage($teamId, "en");

        $queryResult = $TeamTranslationLanguage->hasLanguage(1);
        $this->assertTrue($queryResult);
    }

    public function test_getLanguagesByTeam_success()
    {
        $teamId = 1;

        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");

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

        $this->insertTranslationLanguage(1, "en");
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(1, $queryResult);
        $this->assertEquals([1], $queryResult);

        $this->insertTranslationLanguage(1, "ja");
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(1, $queryResult);
        $this->assertEquals([1], $queryResult);

        $this->insertTranslationLanguage(2, "de");
        $queryResult = $TeamTranslationLanguage->getAllTeamIds();
        $this->assertCount(2, $queryResult);
        $this->assertEquals([1, 2], $queryResult);
    }

    public function test_isLanguageSupported_success()
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $teamId = 1;
        $language1 = "de";
        $language2 = "th";

        $this->assertFalse($TeamTranslationLanguage->isLanguageSupported($teamId, $language1));
        $this->assertFalse($TeamTranslationLanguage->isLanguageSupported($teamId, $language2));

        $this->insertTranslationLanguage($teamId, $language1);

        $this->assertTrue($TeamTranslationLanguage->isLanguageSupported($teamId, $language1));
        $this->assertFalse($TeamTranslationLanguage->isLanguageSupported($teamId, $language2));
    }
}
