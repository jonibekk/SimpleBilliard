<?php
App::uses('TranscodeProgressData', 'Model/Video/Stream');

use Goalous\Enum as Enum;

/**
 * Handling about notification json from AWS SNS
 *
 * Class TranscodeNotificationAwsSns
 */
class TranscodeNotificationAwsSns implements TranscodeProgressData
{
    /**
     * parsed data of AWS SNS JSON request body
     * @see https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html
     * @var array
     */
    protected $data = [];

    /**
     * parsed data of "Message" value in AWS SNS JSON request body
     * @see https://docs.aws.amazon.com/elastictranscoder/latest/developerguide/notifications.html
     * @var array
     */
    protected $messageData = [];

    /**
     * Creating self instance parsing from Json string
     * posted from AWS SNS
     * @see https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html
     *
     * @param string $json
     *
     * @return TranscodeNotificationAwsSns
     */
    public static function parseJsonString(string $json): self
    {
        $jsonData = json_decode($json, true);
        if (is_null($jsonData)) {
            throw new InvalidArgumentException('failed to parse json string');
        }
        return self::createFromArray($jsonData);
    }

    /**
     * Creating self instance from parsed Json string
     * posted from AWS SNS
     * @see https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html
     *
     * @param array $data
     *
     * @return TranscodeNotificationAwsSns
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * TranscodeNotificationAwsSns constructor.
     *
     * @param $data
     */
    private function __construct($data)
    {
        $this->data = $data;
        $this->parseMessage();
    }

    /**
     * Parsing "Message" string in AWS SNS JSON request body
     *
     * @see https://docs.aws.amazon.com/elastictranscoder/latest/developerguide/notifications.html
     */
    private function parseMessage()
    {
        if (!isset($this->data['Message'])) {
            throw new InvalidArgumentException('failed to parse SNS notification Message column not exists');
        }
        $messageData = json_decode($this->data['Message'], true);
        if (is_null($messageData)) {
            throw new InvalidArgumentException('failed to parse SNS notification format json string');
        }
        $this->messageData = $messageData;
    }

    /**
     * Return Enum defined transcoding progress state
     *
     * @return Enum\Video\VideoTranscodeProgress
     */
    public function getProgressState(): Enum\Video\VideoTranscodeProgress
    {
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

    /**
     * Throw RuntimeException if 0 key of outputs is not exists
     *
     * @throws RuntimeException
     */
    private function assertFirstOutputsExists()
    {
        if (!isset($this->messageData['outputs'][0])) {
            throw new RuntimeException('outputs[0] is not exists');
        }
    }

    /**
     * Return jobId string
     *
     * @return string
     */
    public function getJobId(): string
    {
        return $this->messageData['jobId'];
    }

    /**
     * Return output key of resource path
     *
     * @return string
     */
    public function getOutputKeyPrefix(): string
    {
        return $this->messageData['outputKeyPrefix'];
    }

    /**
     * Return transcoded video output aspect ratio by float
     *
     * @return float
     */
    public function getAspectRatio(): float
    {
        $this->assertFirstOutputsExists();
        if (!isset($this->messageData['outputs'][0]['height'])
            || !isset($this->messageData['outputs'][0]['width'])) {
            throw new RuntimeException('outputs height/width not exists');
        }
        $height = floatval($this->messageData['outputs'][0]['height']);
        $width  = floatval($this->messageData['outputs'][0]['width']);
        if (0 === $height) {
            throw new RuntimeException('height is 0');
        }
        return $width / $height;
    }

    /**
     * Return output video duration in seconds by int
     *
     * @return int
     */
    public function getDuration(): int
    {
        $this->assertFirstOutputsExists();
        return intval($this->messageData['outputs'][0]['duration']);
    }

    /**
     * Return master playlist path in the storage
     *
     * @return string
     */
    public function getPlaylistPath(): string
    {
        return $this->getOutputKeyPrefix() . 'playlist.m3u8';
    }

    /**
     * Get the meta-data set ti the job
     *
     * @param string $key
     * @param null   $default
     *
     * @return null
     */
    public function getMetaData(string $key, $default = null)
    {
        if (isset($this->messageData['userMetadata'][$key])) {
            return $this->messageData['userMetadata'][$key];
        }
        return $default;
    }

    /**
     * Return true if has error
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->getProgressState()->equals(Enum\Video\VideoTranscodeProgress::ERROR());
    }

    /**
     * Return warning message string
     * @see https://confluence.goalous.com/display/GOAL/AWS+SNS+video+transcode+notification
     *
     * @return string
     */
    public function getWarning(): string
    {
        if (isset($this->messageData['outputs'][0])
            && isset($this->messageData['outputs'][0]['statusDetail'])) {
            // if statusDetail is in outputs, return that
            return $this->messageData['outputs'][0]['statusDetail'];
        }
        return $this->createErrorString();
    }

    /**
     * Return error message string
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->createErrorString();
    }

    /**
     * Return error string of job or outputs
     * @see https://confluence.goalous.com/display/GOAL/AWS+SNS+video+transcode+notification
     *
     * @return string
     */
    private function createErrorString(): string
    {
        // First, check the job error
        if (isset($this->messageData['errorCode']) || isset($this->messageData['messageDetails'])) {
            $errorCode = empty($this->messageData['errorCode']) ? '' : sprintf('[%s] ', $this->messageData['errorCode']);
            $errorMessage = empty($this->messageData['messageDetails']) ? '' : $this->messageData['messageDetails'];
            return $errorCode . $errorMessage;
        }

        // Check output and playlist error
        $outputs = am($this->messageData['outputs'], $this->messageData['playlists']);
        foreach ($outputs as $output) {
            if (isset($output['statusDetail'])) {
                $errorCode = empty($output['errorCode']) ? '' : sprintf('[%s] ', $output['errorCode']);
                $errorMessage = empty($output['statusDetail']) ? '' : $output['statusDetail'];
                return $errorCode . $errorMessage;
            }
        }

        return '';
    }
}
