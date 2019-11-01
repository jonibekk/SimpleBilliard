<?php
App::import('Lib/Translation', 'TranslatorClientInterface');
App::import('Lib/Translation', 'TranslationResult');
App::import('Lib/Util', 'StringUtil');

abstract class BaseTranslatorClient implements TranslatorClientInterface
{
    /**
     * Translation APIs have limitation to the maximum length of source string.
     * This value is used to segment long string
     */
    const MAX_SEGMENT_CHAR_LENGTH = 2000;

    /**
     * Translation APIs have limitation to the maximum size of string array in batch translation.
     * This value is used to translate large array in chunks
     */
    const MAX_BATCH_ARRAY_SIZE = 100;

    /**
     * Translate a single string using external API.
     *
     * @param string $body
     * @param string $targetLanguage
     *
     * @return TranslationResult
     */
    abstract public function translate(string $body, string $targetLanguage): TranslationResult;

    /**
     * Request translation from external API
     *
     * @param string[] $segmentedString
     * @param string   $targetLanguage
     *
     * @return TranslationResult[]
     */
    abstract protected function requestTranslation(array $segmentedString, string $targetLanguage): array;

    /**
     * Merge multiple translation results into a single one
     *
     * @param TranslationResult[] $translationResults
     * @param string              $glue String used to merge 2 strings
     *
     * @return TranslationResult
     */
    protected function mergeTranslationResults(array $translationResults, string $glue = "\n"): TranslationResult
    {
        $langCount = [];
        $mergedString = '';

        /** @var TranslationResult $singleResult */
        foreach ($translationResults as $singleResult) {

            if (empty($mergedString)) {
                $mergedString = $singleResult->getTranslation();
            } else {
                $mergedString .= $glue . $singleResult->getTranslation();
            }

            if (empty($langCount[$singleResult->getSourceLanguage()])) {
                $langCount[$singleResult->getSourceLanguage()] = 0;
            }

            $langCount[$singleResult->getSourceLanguage()] += 1;
        }

        // Get most frequent source language
        $sourceLanguage = array_keys($langCount, max($langCount))[0];

        return new TranslationResult($sourceLanguage, $mergedString, $translationResults[0]->getTargetLanguage());
    }
}
