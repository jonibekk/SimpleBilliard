<?php
App::uses('AwsEtsForVideoSourceTrait', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Enum as Enum;

class AwsEtsTranscodeOutput
{
    use AwsEtsForVideoSourceTrait;

    /**
     * @var Enum\Model\Video\VideoSourceType
     */
    private $videoSourceType;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $thumbnailPattern;

    /**
     * @var string
     */
    private $presetId;

    /**
     * @var int
     */
    private $segmentDuration;

    /**
     * @var array
     */
    private $watermarks = [];

    public function __construct(Enum\Model\Video\VideoSourceType $videoSourceType, string $key, string $thumbnailPattern, string $presetId)
    {
        $this->videoSourceType = $videoSourceType;
        $this->key = $key;
        $this->thumbnailPattern = $thumbnailPattern;
        $this->presetId = $presetId;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setSegmentDuration(int $segmentDuration)
    {
        $this->segmentDuration = $segmentDuration;
    }

    public function addWaterMark(string $inputKey, string $presetWatermarkId)
    {
        array_push($this->watermarks, [
            'InputKey' => $inputKey,
            'PresetWatermarkId' => $presetWatermarkId,
        ]);
    }

    public function getOutputArray(bool $putWaterMark): array
    {
        $output = [
            'Key' => $this->key,
            'ThumbnailPattern' => $this->thumbnailPattern,
            'PresetId' => $this->presetId,
            'Rotate' => 'auto',
            'Watermarks' => $putWaterMark ? $this->watermarks : [],
        ];
        if (!is_null($this->segmentDuration)) {
            $output['SegmentDuration'] = strval($this->segmentDuration);
        }
        return $output;
    }

    /**
     * @param $baseUrl string|null
     *
     * @return VideoSource
     */
    public function getVideoSource($baseUrl = null): VideoSource
    {
        $url = AwsEtsTranscodeOutputFileNameDefinition::getSourceBaseName($this->videoSourceType, $this->key);
        $videoSource = new VideoSource($this->videoSourceType, $url);
        $videoSource->setBaseUrl($baseUrl);
        return $videoSource;
    }
}
