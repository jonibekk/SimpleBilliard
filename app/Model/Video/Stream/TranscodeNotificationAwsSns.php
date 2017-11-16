<?php
App::uses('TranscodeProgressData', 'Model/Video/Stream');

use Goalous\Model\Enum as Enum;

class TranscodeNotificationAwsSns implements TranscodeProgressData
{
    protected $data = [];
    protected $messageData = [];

    public static function parseJsonString(string $json)
    {
        $jsonData = json_decode($json, true);
        if (is_null($jsonData)) {
            throw new RuntimeException('failed to parse json string');
        }
        return new self($jsonData);
    }

    private function __construct($data)
    {
        $this->data = $data;
        $this->parseMessage();
    }

    private function parseMessage()
    {
        $messageData = json_decode($this->data['Message'], true);
        if (is_null($messageData)) {
            throw new RuntimeException('failed to parse json message string');
        }
        $this->messageData = $messageData;
        //CakeLog::info(sprintf('message data: %s', AppUtil::jsonOneLine($messageData)));
    }

    public function getProgressState(): Enum\Video\VideoTranscodeProgress {
        if (!isset($this->messageData['state'])) {
            throw new RuntimeException('message data: state is not set.');
        }
        switch ($this->messageData['state']) {
            case 'PROGRESSING':
                return Enum\Video\VideoTranscodeProgress::PROGRESS();
            case 'COMPLETED':
                return Enum\Video\VideoTranscodeProgress::COMPLETE();
            case 'ERROR':
                return Enum\Video\VideoTranscodeProgress::ERROR();
            case 'WARNING':
                return Enum\Video\VideoTranscodeProgress::WARNING();
        }
        throw new RuntimeException('unknown notification state: ' . $this->messageData['state']);
    }

    public function getOutputKeyPrefix(): string
    {
        return $this->messageData['outputKeyPrefix'];
    }

    public function getAspectRatio(): float
    {
        // TODO: fix not use magic number 0
        $height = floatval($this->messageData['outputs'][0]['height']);
        $width  = floatval($this->messageData['outputs'][0]['width']);
        return $width/$height;
    }

    public function getDuration(): int
    {
        // TODO: fix not use magic number 0
        return intval($this->messageData['outputs'][0]['duration']);
    }

    public function getPlaylistPath(): string
    {
        // TODO: define somewhere m3u8
        return $this->getOutputKeyPrefix() . 'playlist.m3u8';
    }

    public function getMetaData(string $key, $default = null)
    {
        if (isset($this->messageData['userMetadata'][$key])) {
            return $this->messageData['userMetadata'][$key];
        }
        return $default;
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
