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
}