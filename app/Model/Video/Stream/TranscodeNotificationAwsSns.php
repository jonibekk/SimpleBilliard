<?php
App::uses('VideoUploadRequestOnPost', 'Model/Video/Stream');

use Goalous\Model\Enum as Enum;

class TranscodeNotificationAwsSns implements TranscodeProgressData
{
    public static function parseJsonString(string $json)
    {
        return new self();
    }

    private function __construct()
    {
    }

    public function getProgress(): Enum\Video\VideoTranscodeProgress {
        return Enum\Video\VideoTranscodeProgress::COMPLETE();
    }

    public function isError(): bool
    {
        return false;
    }

    public function getError(): string
    {
        return '';
    }
}
