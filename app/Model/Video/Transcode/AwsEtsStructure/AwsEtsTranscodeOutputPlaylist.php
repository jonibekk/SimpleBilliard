<?php
App::uses('AwsEtsForVideoSourceTrait', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Enum as Enum;

class AwsEtsTranscodeOutputPlaylist
{
    use AwsEtsForVideoSourceTrait;

    /**
     * @var Enum\Model\Video\VideoSourceType
     */
    private $videoSourceType;

    private $format;
    private $name;
    private $outputKeys = [];

    private $enableHlsContentProtection = false;

    /**
     * AwsEtsTranscodeOutputPlaylist constructor.
     *
     * @param Enum\Model\Video\VideoSourceType $videoSourceType
     * @param string                     $format
     * @param string                     $name
     * @param AwsEtsTranscodeOutput[]             $outputKeys
     */
    public function __construct(Enum\Model\Video\VideoSourceType $videoSourceType, string $format, string $name, array $outputKeys)
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
            'OutputKeys' => array_reduce($this->outputKeys, function($r, $outputKey/** @var AwsEtsTranscodeOutput $outputKey */) {
                array_push($r, $outputKey->getKey());
                return $r;
            }, []),
        ];
    }

    /**
     * @param string|null $baseUrl
     *
     * @return VideoSource
     */
    public function getVideoSource($baseUrl = null): VideoSource
    {
        $url = AwsEtsTranscodeOutputFileNameDefinition::getSourceBaseName($this->videoSourceType, $this->name);
        $videoSource = new VideoSource($this->videoSourceType, $url);
        $videoSource->setBaseUrl($baseUrl);
        return $videoSource;
    }

    /**
     * @param bool $enableHlsContentProtection
     *
     * if enabling this option
     * AWS ETS Pipeline need configuring encryption key of AWS KMS Key ARN
     */
    public function setEnableHlsContentProtection(bool $enableHlsContentProtection)
    {
        $this->enableHlsContentProtection = $enableHlsContentProtection;
    }

    public function getOutputArray(): array
    {
        $output = [
            'Format' => $this->format,
            'Name' => $this->name,
            'OutputKeys' => array_reduce($this->outputKeys, function($keys, $outputKey) {
                /** @var AwsEtsTranscodeOutput $outputKey */
                array_push($keys, $outputKey->getKey());
                return $keys;
            }, []),
        ];
        if ($this->enableHlsContentProtection) {
            $output = am($output, [
                'HlsContentProtection' => [
                    'Method'           => 'aes-128',
                    'KeyStoragePolicy' => 'WithVariantPlaylists',
                ],
            ]);
        }
        return $output;
    }
}
