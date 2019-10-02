<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TranslationLanguage', 'Model');

use Goalous\Exception as GlException;
use Goalous\Enum\Language as LanguageEnum;

class TeamTranslationLanguageService extends AppService
{
    /**
     * Get all translation languages supported by the team asn associative array
     *
     * @param int $teamId
     *
     * @return array
     *              [ ["en" => "English"], [...] ]
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

            $returnArray[$singleData['language']] = __($languageInfo['intl_name']);
        };

        return $returnArray;
    }

    /**
     * Get default translation language of a team
     *
     * @param int $teamId
     *
     * @return array
     *              ["en" => "English"]
     * @throws Exception
     */
    public function getDefaultTranslationLanguage(int $teamId): array
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasLanguage($teamId)) {
            throw new GlException\GoalousNotFoundException("Team does not have translation languages");
        }

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $defaultLanguage = $Team->getDefaultTranslationLanguage($teamId);

        if (empty($defaultLanguage) || !$TeamTranslationLanguage->isLanguageSupported($teamId, $defaultLanguage)) {
            $defaultLanguage = $this->calculateDefaultTranslationLanguage($teamId);
            $this->setDefaultTranslationLanguage($teamId, $defaultLanguage);
        }

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        $languageInfo = $TranslationLanguage->getLanguageByCode($defaultLanguage);

        return [
            $languageInfo['language'] => __($languageInfo['intl_name'])
        ];
    }

    /**
     * Get language code of default translation language of a team
     *
     * @param int $teamId
     *
     * @return string ISO 639-1 Language Code
     *
     * @throws Exception
     */
    public function getDefaultTranslationLanguageCode(int $teamId): string
    {
        return array_keys($this->getDefaultTranslationLanguage($teamId))[0] ?: "";
    }

    /**
     * Calculate a default translation language for the team
     *
     * @param int $teamId
     *
     * @return string ISO-639-1 Language code
     */
    public function calculateDefaultTranslationLanguage(int $teamId): string
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasLanguage($teamId)) {
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

    /**
     * From given array of language codes, get the first language that the team support
     *
     * @param int   $teamId
     * @param array $languageCodes
     *
     * @return string
     */
    public function selectFirstSupportedLanguage(int $teamId, array $languageCodes): string
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $queryResult = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

        foreach ($languageCodes as $language) {
            $cleanedLanguage = LanguageEnum::cleanLanguage($language);
            if (empty($cleanedLanguage)) {
                continue;
            }
            foreach ($queryResult as $teamTranslationLanguageEntity) {
                if ($cleanedLanguage === $teamTranslationLanguageEntity['language']) {
                    return $cleanedLanguage;
                }
            }
        }

        return "";
    }
}