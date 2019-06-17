<?php

App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TranslationLanguageEntity');

use Goalous\Enum as Enum;
use Goalous\Enum\DataType\DataType as DataType;
use Goalous\Enum\Language as LanguageEnum;

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
     * Get information of a specified language
     *
     * @param Enum\Language $language
     *
     * @return TranslationLanguageEntity
     */
    public function getLanguage(Enum\Language $language): TranslationLanguageEntity
    {
        $option = [
            'conditions' => [
                'language' => $language->getValue()
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
        if (!LanguageEnum::isValid($language)) {
            throw new InvalidArgumentException("Unknown language code");
        }

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
                'id'         => 'DESC'
            ]
        ];

        /** @var TranslationLanguageEntity $return */
        $return = $this->useType()->useEntity()->find('first', $option);

        return $return;
    }
}