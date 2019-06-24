<?php
App::import('Lib/Translation', 'BaseTranslatorClient');
App::import('Lib/Translation', 'TranslationResult');

use Google\Cloud\Translate\TranslateClient;
use Goalous\Enum\Language as LangEnum;

class GoogleTranslatorClient extends BaseTranslatorClient
{
    const MAX_CHAR_LENGTH = 2000;
    const MAX_BATCH_ARRAY_LENGTH = 128;

    protected function requestTranslation(array $segmentedString, string $targetLanguage): array
    {
        if (!LangEnum::isValid($targetLanguage)) {
            throw new InvalidArgumentException('Invalid language code');
        }

        if (count($segmentedString) > static::MAX_BATCH_ARRAY_LENGTH) {
            throw new InvalidArgumentException('Array is too long');
        }

        $translate = new TranslateClient([
            'key' => GCP_API_KEY
        ]);

        $translationResults = $translate->translateBatch($segmentedString, [
            'target' => $targetLanguage
        ]);

        $returnArray = [];

        foreach ($translationResults as $translationResult) {
            $returnArray[] = new TranslationResult($translationResult['source'], $translationResult['text'], $targetLanguage);
        }

        return $returnArray;
    }

}