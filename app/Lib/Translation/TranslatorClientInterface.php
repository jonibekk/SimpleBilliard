<?php

App::import('Lib/Translation', 'TranslationResult');

interface TranslatorClientInterface
{
    /**
     * Translate a single string using external API
     *
     * @param string $body
     * @param string $targetLanguage
     *
     * @return TranslationResult
     */
    public function translate(string $body, string $targetLanguage): TranslationResult;

    /**
     * Translate multiple strings using external API
     *
     * @param array  $body
     * @param string $targetLanguage
     *
     * @return TranslationResult[]
     */
    public function translateMany(array $body, string $targetLanguage): array;
}