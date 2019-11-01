<?php
App::import('Lib/Translation', 'BaseTranslatorClient');
App::import('Lib/Translation', 'TranslationResult');

use Google\Cloud\Translate\TranslateClient;
use Goalous\Enum\Language as LangEnum;

/**
 * Client for translating with Google Cloud Translation API.
 * Dashboard: https://console.cloud.google.com/apis/api/translate.googleapis.com/overview?project=goalous-60207
 * Class GoogleTranslatorClient
 */
class GoogleTranslatorClient extends BaseTranslatorClient
{
    /**
     * Recommended maximum of 2k code points
     * https://cloud.google.com/translate/quotas
     */
    const MAX_SEGMENT_CHAR_LENGTH = 2000;
    /**
     * Hard limit on array length in batch translation.
     */
    const MAX_BATCH_ARRAY_SIZE = 128;
    /**
     * Translation API will remove all new lines (\n)
     * This string will replace \n before translation.
     */
    const TRANSLATION_BREAKPOINT = "<br glstrl-9gAl>";

    /**
     * Translate a string using Google Cloud Translation API
     * Source string will be split to segments to conform with limitation with respective translation API.
     *
     * @param string $body
     * @param string $targetLanguage
     *
     * @return TranslationResult
     */
    public function translate(string $body, string $targetLanguage): TranslationResult
    {
        // Segment long string to conform with translator API limitation
        $segmentedBody = StringUtil::splitStringToSegments($body, static::MAX_SEGMENT_CHAR_LENGTH, "\n",
            self::TRANSLATION_BREAKPOINT, true);

        // Check for array length
        if (count($segmentedBody) > static::MAX_BATCH_ARRAY_SIZE) {
            $chunkedBody = array_chunk($segmentedBody, static::MAX_BATCH_ARRAY_SIZE);

            $translatedResult = [];

            foreach ($chunkedBody as $chunk) {
                $translatedResult = array_merge($translatedResult, $this->requestTranslation($chunk, $targetLanguage));
            }
        } else {
            $translatedResult = $this->requestTranslation($segmentedBody, $targetLanguage);
        }
        $mergedTranslationResult = $this->mergeTranslationResults($translatedResult);

        // Replace special breakpoint back with new line (\n)
        $fullTranslatedText = $mergedTranslationResult->getTranslation();
        $fullTranslatedText = preg_replace('/' . self::TRANSLATION_BREAKPOINT . '/m',
            "\n", $fullTranslatedText);

        return new TranslationResult($mergedTranslationResult->getSourceLanguage(), $fullTranslatedText,
            $mergedTranslationResult->getTargetLanguage());
    }

    protected function requestTranslation(array $segmentedString, string $targetLanguage): array
    {
        if (count($segmentedString) > static::MAX_BATCH_ARRAY_SIZE) {
            throw new InvalidArgumentException("Batch size is too big.");
        }

        $translate = new TranslateClient([
            'key' => GCP_API_KEY
        ]);

        $translationResults = $translate->translateBatch($segmentedString, [
            'target' => $targetLanguage
        ]);

        $translationResultArray = [];

        foreach ($translationResults as $translationResult) {
            // Used to convert NCRs to symbols
            $decodedString = html_entity_decode($translationResult['text'], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $translationResultArray[] = new TranslationResult($translationResult['source'], $decodedString,
                $targetLanguage);
        }

        return $translationResultArray;
    }

}
