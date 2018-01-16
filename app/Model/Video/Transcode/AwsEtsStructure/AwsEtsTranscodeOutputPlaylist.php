<?php
App::uses('AwsEtsForVideoSourceTrait', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Model\Enum as Enum;

class AwsEtsTranscodeOutputPlaylist
{
    use AwsEtsForVideoSourceTrait;

    /**
     * @var Enum\Video\VideoSourceType
     */
    private $videoSourceType;

    private $format;
    private $name;
    private $outputKeys = [];

    private $enableHlsContentProtection = false;

    /**
     * AwsEtsTranscodeOutputPlaylist constructor.
     *
     * @param Enum\Video\VideoSourceType $videoSourceType
     * @param string                     $format
     * @param string                     $name
     * @param AwsEtsTranscodeOutput[]             $outputKeys
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
            'OutputKeys' => array_reduce($this->outputKeys, function($r, $outputKey/** @var AwsEtsTranscodeOutput $outputKey */) {
                array_push($r, $outputKey->getKey());
                return $r;
            }, []),
        ];
    }

    public function getVideoSource(string $baseUrl): VideoSource
    {
        $url = $baseUrl . AwsEtsTranscodeOutputFileNameDefinition::getSourceBaseName($this->videoSourceType, $this->name);
        return new VideoSource($this->videoSourceType, $url);
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
