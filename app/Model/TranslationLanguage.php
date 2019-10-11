<?php

App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TranslationLanguageEntity');

use Goalous\Enum as Enum;
use Goalous\Enum\DataType\DataType as DataType;

class TranslationLanguage extends AppModel
{
    /**
     * Manual setting of table name
     *
     * @var string
     */
    public $useTable = "mst_translation_languages";

    public $modelConversionTable = [
        'importance' => DataType::INT
    ];

    /**
     * Get all translation languages
     *
     * @return TranslationLanguageEntity[]
     */
    public function getAllLanguages(): array
    {
        $option = [
            'order' => [
                'importance' => 'DESC',
                'id'         => 'DESC'
            ]
        ];

        return $this->useType()->useEntity()->find('all', $option);
    }

    /**
     * Get all translation language codes
     *
     * @return string[]
     */
    public function getAllLanguagesAsBasicArray(): array
    {
        $languageEntities = $this->getAllLanguages();

        $languages = [];

        foreach ($languageEntities as $entity) {
            $languages[] = $entity['language'];
        }

        return $languages;
    }

    /**
     * Check whether given language code is valid
     *
     * @param string $language
     *
     * @return bool
     */
    public function isValidLanguage(string $language): bool
    {
        $option = [
            'conditions' => [
                'language' => $language
            ]
        ];

        return $this->find('count', $option) > 0;
    }

    /**
     * Get information of a specified language
     *
     * @param string $language ISO 639-1 Language code
     *
     * @return TranslationLanguageEntity
     */
    public function getLanguage(string $language): TranslationLanguageEntity
    {
        $option = [
            'conditions' => [
                'language' => $language
            ]
        ];

        /** @var TranslationLanguageEntity $return */
        $return = $this->useType()->useEntity()->find('first', $option);

        return $return;
    }

    /**
     * Get information of a specified language code. E.g. en, ja, etc.
     *
     * @param string $language
     *
     * @return TranslationLanguageEntity
     */
    public function getLanguageByCode(string $language): TranslationLanguageEntity
    {
        $option = [
            'conditions' => [
                'language' => $language
            ]
        ];

        /** @var TranslationLanguageEntity $return */
        $return = $this->useType()->useEntity()->find('first', $option);

        if (empty($return)) {
            throw new InvalidArgumentException("Unknown language code");
        }

        return $return;
    }

    /**
     * Remove country code from a locale if not supported.
     *          e.g. en-US -> en, zh-TW -> zh-TW
     *
     * @param string $localizedLanguage
     *
     * @return string
     */
    public function cleanLanguage(string $localizedLanguage): string
    {
        if ($this->isValidLanguage($localizedLanguage)) {
            return $localizedLanguage;
        }

        $languageCode = locale_get_primary_language($localizedLanguage);

        return $languageCode ?: "";
    }

    /**
     * Get most important language from inserted ones
     *
     * @param array $languages Array of ISO-639-1 language codes.
     *                         ["en", "ja", ...]
     *
     * @return TranslationLanguageEntity
     */
    public function getMostImportantLanguage(array $languages): TranslationLanguageEntity
    {
        $option = [
            'conditions' => [
                'language' => $languages
            ],
            'order'      => [
                'importance' => 'DESC',
                'id'         => 'ASC'
            ]
        ];

        /** @var TranslationLanguageEntity $return */
        $return = $this->useType()->useEntity()->find('first', $option);

        return $return;
    }
}
