<?php

App::import('Lib/Translation', 'TranslationResult');

use Goalous\Enum\Language as LangEnum;

interface TranslatorClientInterface
{
    /**
     * Translate a single string using external API
     *
     * @param string $body
     * @param LangEnum $targetLanguage
     *
     * @return TranslationResult
     */
    public function translate(string $body, LangEnum $targetLanguage): TranslationResult;

    /**
     * Translate multiple strings using external API
     *
     * @param array  $body
     * @param LangEnum $targetLanguage
     *
     * @return TranslationResult[]
     */
    public function translateBatch(array $body, LangEnum $targetLanguage): array;
}