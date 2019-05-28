<?php
App::import('Lib/Translation', 'TranslatorClientInterface');

use Google\Cloud\Translate\TranslateClient;
use Goalous\Enum\Language as LangEnum;

class GoogleTranslatorClient implements TranslatorClientInterface
{
    public function translate(string $body, LangEnum $targetLanguage): TranslationResult
    {
        return $this->translateMany([$body], $targetLanguage)[0];
    }

    public function translateBatch(array $body, LangEnum $targetLanguage): array
    {
        $translate = new TranslateClient([
            'key' => GCP_API_KEY
        ]);

        $translationResults = $translate->translateBatch($body, [
            'target' => $targetLanguage->getValue()
        ]);

        $result = [];

        foreach ($translationResults as $translationResult) {
            $result[] = new TranslationResult($translationResult['source'], $translationResult['text']);
        }

        return $result;
    }

}