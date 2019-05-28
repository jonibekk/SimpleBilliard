<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/05/22
 * Time: 17:51
 */

class TranslationResult
{
    /** @var string */
    private $sourceLanguage;

    /** @var string */
    private $translation;

    /**
     * TranslationResult constructor.
     *
     * @param string $sourceLanguage
     * @param string $translation
     */
    public function __construct(string $sourceLanguage, string $translation)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->translation = $translation;
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
}