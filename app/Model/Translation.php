<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TranslationEntity');

use Goalous\Enum\DataType\DataType as DataType;
use Goalous\Enum\Language as LanguageEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\Status as TranslationStatus;
use Goalous\Exception as GlException;

class Translation extends AppModel
{
    public $modelConversionTable = [
        'content_type' => DataType::INT,
        'content_id'   => DataType::INT,
        'status'       => DataType::INT
    ];

    /**
     * Check if translation exists
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $language
     *
     * @return bool
     */
    public function hasTranslation(TranslationContentType $contentType, int $contentId, string $language): bool
    {
        $option = [
            'conditions' => [
                'content_type' => $contentType->getValue(),
                'content_id'   => $contentId,
                'language'     => $language
            ]
        ];

        return $this->find('count', $option) > 0;
    }

    /**
     * Create new entry for new translation
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $language
     *
     * @throws Exception
     */
    public function createEntry(TranslationContentType $contentType, int $contentId, string $language)
    {
        if (!LanguageEnum::isValid($language)) {
            throw new InvalidArgumentException("Invalid language code: $language");
        }

        $newData = [
            'content_type' => $contentType->getValue(),
            'content_id'   => $contentId,
            'body'         => '',
            'language'     => $language,
            'status'       => TranslationStatus::PROCESSING,
            'created'      => GoalousDateTime::now()->getTimestamp()
        ];

        $this->create();
        $this->save($newData, false);
    }

    /**
     * Insert translation result
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $language
     * @param string                 $translationBody
     *
     * @throws Exception
     */
    public function updateTranslationBody(TranslationContentType $contentType, int $contentId, string $language, string $translationBody)
    {
        if (!LanguageEnum::isValid($language)) {
            throw new InvalidArgumentException("Invalid language code: $language");
        }

        $translationEntry = $this->getTranslation($contentType, $contentId, $language);

        if (empty($translationEntry)) {
            throw new GlException\GoalousNotFoundException('Translation entry not found');
        }

        $newData = [
            'id'     => $translationEntry['id'],
            'body'   => $translationBody,
            'status' => TranslationStatus::DONE
        ];

        $this->create();
        $this->save($newData, false);
    }

    /**
     * Get translation data
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $language
     *
     * @return TranslationEntity | null
     */
    public function getTranslation(TranslationContentType $contentType, int $contentId, string $language)
    {
        $option = [
            'conditions' => [
                'content_type' => $contentType->getValue(),
                'content_id'   => $contentId,
                'language'     => $language
            ],
        ];

        /** @var TranslationEntity $queryResult */
        $queryResult = $this->useType()->useEntity()->find('first', $option);

        return $queryResult;
    }

    /**
     * Hard delete all translations for a particular content
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     */
    public function eraseAllTranslations(TranslationContentType $contentType, int $contentId)
    {
        $conditions = [
            'content_type' => $contentType->getValue(),
            'content_id'   => $contentId,
        ];

        $this->deleteAll($conditions);
    }

    /**
     * Hard delete translation for a particular content
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $language
     */
    public function eraseTranslation(TranslationContentType $contentType, int $contentId, string $language)
    {
        $conditions = [
            'content_type' => $contentType->getValue(),
            'content_id'   => $contentId,
            'language'     => $language
        ];

        $this->deleteAll($conditions);
    }
}