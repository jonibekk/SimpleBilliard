<?php
App::import('Lib/Translation', 'TranslatorClientInterface');

use Google\Cloud\Translate\TranslateClient;

class GoogleTranslatorClient implements TranslatorClientInterface
{
    public function translate(string $body, string $targetLanguage): TranslationResult
    {
        return $this->translateMany([$body], $targetLanguage)[0];
    }

    public function translateMany(array $body, string $targetLanguage): array
    {
        $translate = new TranslateClient([
            'key' => GCP_API_KEY
        ]);

        $translationResults = $translate->translateBatch($body, [
            'target' => $targetLanguage
        ]);

        $result = [];

        foreach ($translationResults as $translationResult) {
            $result[] = new TranslationResult($translationResult['source'], $translationResult['text']);
        }

        return $result;
    }

}