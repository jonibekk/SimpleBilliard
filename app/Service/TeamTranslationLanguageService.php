<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TranslationLanguage', 'Model');

use Goalous\Exception as GlException;

class TeamTranslationLanguageService extends AppService
{
    /**
     * Get all translation languages supported by the team
     *
     * @param int $teamId
     *
     * @return array
     *              [ ["language" => "en", "language_name" => "English"],[...] ]
     */
    public function getAllLanguages(int $teamId): array
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $queryResult = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

        if (empty($queryResult)) {
            throw new GlException\GoalousNotFoundException("Team does not have translation languages");
        }

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $returnArray = [];

        foreach ($queryResult as $singleData) {

            $languageInfo = $TranslationLanguage->getLanguageByCode($singleData['language']);

            $returnArray[] = [
                "language"      => $singleData['language'],
                'language_name' => __($languageInfo['intl_name'])
            ];
        };

        return $returnArray;
    }

    /**
     * Get default translation language of a team
     *
     * @param int $teamId
     *
     * @return array
     * @throws Exception
     */
    public function getDefaultLanguage(int $teamId): array
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasTranslationLanguage($teamId)) {
            throw new GlException\GoalousNotFoundException("Team does not have translation languages");
        }

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $defaultLanguage = $Team->getDefaultTranslationLanguage($teamId);

        if (empty($defaultLanguage)) {
            $defaultLanguage = $this->calculateDefaultLanguage($teamId);
            $this->setDefaultTranslationLanguage($teamId, $defaultLanguage);
        }

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $languageInfo = $TranslationLanguage->getLanguageByCode($defaultLanguage);

        return [
            "language"      => $languageInfo['language'],
            'language_name' => __($languageInfo['intl_name'])
        ];
    }

    /**
     * Calculate a default translation language for the team
     *
     * @param int $teamId
     *
     * @return string ISO-639-1 Language code
     */
    public function calculateDefaultLanguage(int $teamId): string
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasTranslationLanguage($teamId)) {
            throw new GlException\GoalousNotFoundException("Team does not have translation languages");
        }

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $languages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

        $languageArray = [];

        foreach ($languages as $languageEntity) {
            $languageArray[] = $languageEntity['language'];
        }

        $mostImportantLanguage = $TranslationLanguage->getMostImportantLanguage($languageArray);

        return $mostImportantLanguage['language'];
    }

    /**
     * Set the default translation language in a team
     *
     * @param int    $teamId
     * @param string $language ISO-639-1 Language code
     *
     * @throws Exception
     */
    private function setDefaultTranslationLanguage(int $teamId, string $language)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        try {
            $this->TransactionManager->begin();
            $Team->setDefaultTranslationLanguage($teamId, $language);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error($e->getMessage(), $e->getTrace());
            throw $e;
        }
    }


}