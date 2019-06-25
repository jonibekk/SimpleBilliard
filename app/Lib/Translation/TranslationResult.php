<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/05/22
 * Time: 17:51
 */

App::uses('StringUtil', 'Util');

class TranslationResult
{
    /** @var string */
    private $sourceLanguage;

    /** @var string */
    private $targetLanguage;

    /** @var string */
    private $translation;

    /** @var int */
    private $translationCharLength;

    /**
     * TranslationResult constructor.
     *
     * @param string $sourceLanguage
     * @param string $translation
     * @param string $targetLanguage
     */
    public function __construct(string $sourceLanguage, string $translation, string $targetLanguage)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->translation = $translation;
        $this->translationCharLength = StringUtil::mbStrLength($translation);
        $this->targetLanguage = $targetLanguage;
    }

    /**
     * @return string
     */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /**
     * @return string
     */
    public function getTranslation(): string
    {
        return $this->translation;
    }

    /**
     * @return int
     */
    public function getTranslationCharLength(): int
    {
        return $this->translationCharLength;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'source_language' => $this->sourceLanguage,
            'language'        => $this->targetLanguage,
            'translation'     => $this->translation
        ];
    }

    /**
     * @return string
     */
    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }


}