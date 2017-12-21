<?php

use Goalous\Model\Enum as Enum;

class TranscodeOutputVersionDefinition
{
    public static function getVersion(Enum\Video\TranscodeOutputVersion $transcodeOutputVersion)
    {
        switch ($transcodeOutputVersion->getValue()) {
            case Enum\Video\TranscodeOutputVersion::V1:
                $outputVp9  = new AwsEtsOutput(Enum\Video\VideoSourceType::VIDEO_WEBM(), 'webm_500k/video.webm', 'thumbs-{count}', '1513327166916-ghbctw');
                $outputVp9->setForVideoSource(true);
                $outputH264 = new AwsEtsOutput(Enum\Video\VideoSourceType::NOT_RECOMMENDED(), 'ts_500k/video', 'thumbs-{count}', '1513234427744-pkctj7');
                $outputPlaylistHls = new AwsEtsOutputPlaylist(Enum\Video\VideoSourceType::PLAYLIST_M3U8_HLS(), 'HLSv3', 'playlist', [
                    $outputH264,
                ]);
                $outputPlaylistHls->setForVideoSource(true);
                $transcodeOutput = new TranscodeOutputAwsEts(
                    $transcodeOutputVersion, Enum\Video\Transcoder::AWS_ETS()
                );
                $transcodeOutput->addOutputVideo($outputVp9);
                $transcodeOutput->addOutputVideo($outputH264);
                $transcodeOutput->addOutputPlaylist($outputPlaylistHls);
                return $transcodeOutput;
        }
    }
}

class TranscodeOutputAwsEts implements TranscodeOutput
{
    /**
     * @var AwsEtsOutput[]
     */
    private $outputVideos    = [];

    /**
     * @var AwsEtsOutputPlaylist[]
     */
    private $outputPlaylists = [];

    public function __construct(Enum\Video\TranscodeOutputVersion $transcodeOutputVersion, Enum\Video\Transcoder $transcoder)
    {

    }

    public function addOutputVideo(AwsEtsOutput $output)
    {
        array_push($this->outputVideos, $output);
    }

    public function addOutputPlaylist(AwsEtsOutputPlaylist $output)
    {
        array_push($this->outputPlaylists, $output);
    }

    /**
     * @param string $baseUrl
     *
     * @return VideoSource[]
     */
    public function getVideoSources(string $baseUrl) :array
    {
        $sources = [];

        foreach ($this->outputVideos as $outputVideo) {
            if ($outputVideo->isForVideoSource()) {
                array_push($sources, $outputVideo->getVideoSource($baseUrl));
            }
        }

        foreach ($this->outputPlaylists as $outputPlaylist) {
            if ($outputPlaylist->isForVideoSource()) {
                array_push($sources, $outputPlaylist->getVideoSource($baseUrl));
            }
        }

        return $sources;
    }
}

trait AwsEtsForVideoSourceTrait
{
    /**
     * @var bool
     */
    private $isForVideoSource = false;

    public function setForVideoSource(bool $bool)
    {
        $this->isForVideoSource = $bool;
    }

    public function isForVideoSource(): bool
    {
        return $this->isForVideoSource;
    }
}

class AwsEtsOutput
{
    use AwsEtsForVideoSourceTrait;

    /**
     * @var Enum\Video\VideoSourceType
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

    public function __construct(Enum\Video\VideoSourceType $videoSourceType, string $key, string $thumbnailPattern, string $presetId)
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

    public function getOutputArray(): array
    {
        $output = [
            'Key' => $this->key,
            'ThumbnailPattern' => $this->thumbnailPattern,
            'PresetId' => $this->presetId,
            'Rotate' => 'auto',
            'Watermarks' => $this->watermarks,
        ];
        if (!is_null($this->segmentDuration)) {
            $output['SegmentDuration'] = $this->segmentDuration;
        }
        return $output;
    }

    public function getVideoSource(string $baseUrl): VideoSource
    {
        return new VideoSource($this->videoSourceType, $baseUrl);
    }
}

class AwsEtsOutputPlaylist
{
    use AwsEtsForVideoSourceTrait;

    /**
     * @var Enum\Video\VideoSourceType
     */
    private $videoSourceType;

    private $format;
    private $name;
    private $outputKeys = [];

    /**
     * AwsEtsOutputPlaylist constructor.
     *
     * @param Enum\Video\VideoSourceType $videoSourceType
     * @param string                     $format
     * @param string                     $name
     * @param AwsEtsOutput[]             $outputKeys
     */
    public function __construct(Enum\Video\VideoSourceType $videoSourceType, string $format, string $name, array $outputKeys)
    {
        $this->videoSourceType = $videoSourceType;
        $this->format = $format;
        $this->name = $name;
        $this->outputKeys = $outputKeys;
    }

    /**
     * @return array
     */
    public function getPlaylistArray(): array
    {
        return [
            'Format' => $this->format,
            'Name' => $this->name,
            'OutputKeys' => array_reduce($this->outputKeys, function($r, $outputKey/** @var AwsEtsOutput $outputKey */) {
                array_push($r, $outputKey->getKey());
                return $r;
            }, []),
        ];
    }

    public function getVideoSource(string $baseUrl): VideoSource
    {
        return new VideoSource($this->videoSourceType, $baseUrl);
    }
}

/**
 * transcoderの出力内容
 *
 * Class TranscodeOutput
 */
interface TranscodeOutput
{
    /**
     * @param string $baseUrl
     *
     * @return VideoSource[]
     */
    public function getVideoSources(string $baseUrl) :array;
}

/**
 * <source>タグ内に利用する値の出力
 * @see https://developer.mozilla.org/ja/docs/Web/HTML/Element/source
 *
 * Class VideoSource
 */
class VideoSource
{
    /**
     * @var Enum\Video\VideoSourceType
     */
    private $sourceType;

    /**
     * @var string
     */
    private $fullPathUrl;

    public function __construct(Enum\Video\VideoSourceType $type, string $fullPathUrl)
    {
        $this->sourceType = $type;
        $this->fullPathUrl = $fullPathUrl;
    }

    public function getType(): string
    {
        return $this->sourceType->getValue();
    }

    public function getSource(): string
    {
        return $this->fullPathUrl;
    }
}