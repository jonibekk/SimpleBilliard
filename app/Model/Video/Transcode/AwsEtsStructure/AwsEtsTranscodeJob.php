<?php
App::uses('TranscodeOutput', 'Model/Video/Transcode');
App::uses('VideoSource', 'Model/Video/Transcode');
App::uses('AwsEtsForVideoSourceTrait', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeInput', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeJob', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeOutput', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeOutputFileNameDefinition', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeOutputPlaylist', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Enum as Enum;

class AwsEtsTranscodeJob implements TranscodeOutput
{
    /**
     * @var Enum\Video\TranscodeOutputVersion
     */
    private $transcodeOutputVersion;

    /**
     * @var Enum\Video\Transcoder
     */
    private $transcoder;

    /**
     * @var AwsEtsTranscodeInput[]
     */
    private $inputVideos     = [];

    /**
     * @var AwsEtsTranscodeOutput[]
     */
    private $outputVideos    = [];

    /**
     * @var AwsEtsTranscodeOutputPlaylist[]
     */
    private $outputPlaylists = [];


    public function __construct(Enum\Video\TranscodeOutputVersion $transcodeOutputVersion, Enum\Video\Transcoder $transcoder)
    {
        $this->transcodeOutputVersion = $transcodeOutputVersion;
        $this->transcoder = $transcoder;
    }

    public function addInputVideo(AwsEtsTranscodeInput $AwsEtsTranscodeInput)
    {
        array_push($this->inputVideos, $AwsEtsTranscodeInput);
    }

    public function addOutputVideo(AwsEtsTranscodeOutput $output)
    {
        array_push($this->outputVideos, $output);
    }

    public function addOutputPlaylist(AwsEtsTranscodeOutputPlaylist $outputPlaylist)
    {
        array_push($this->outputPlaylists, $outputPlaylist);
    }

    public function getTranscoder(): Enum\Video\Transcoder
    {
        return $this->transcoder;
    }

    /**
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-elastictranscoder-2012-09-25.html#createjob
     *
     * @param string $pipelineId
     * @param string $outputKeyPrefix
     * @param array  $userMetaData
     * @param bool   $putWaterMark
     *
     * @return array
     */
    public function getCreateJobArray(string $pipelineId, string $outputKeyPrefix, array $userMetaData, bool $putWaterMark): array
    {
        return [
            'PipelineId'      => $pipelineId,
            'OutputKeyPrefix' => $outputKeyPrefix,
            'Inputs'          => array_reduce($this->inputVideos, function ($inputs, $inputVideo) {
                /** @var AwsEtsTranscodeInput $inputVideo */
                array_push($inputs, $inputVideo->getOutputArray());
                return $inputs;
            }, []),
            'Outputs'         => array_reduce($this->outputVideos, function ($outputs, $outputVideo) use ($putWaterMark) {
                /** @var AwsEtsTranscodeOutput $outputVideo */
                array_push($outputs, $outputVideo->getOutputArray($putWaterMark));
                return $outputs;
            }, []),
            'Playlists'       => array_reduce($this->outputPlaylists, function ($outputs, $outputPlaylist) {
                /** @var AwsEtsTranscodeOutputPlaylist $outputPlaylist */
                array_push($outputs, $outputPlaylist->getOutputArray());
                return $outputs;
            }, []),
            'UserMetadata'    => am(
            // changing values to string
            // https://github.com/aws/aws-sdk-php-laravel/issues/104
                array_map('strval', $userMetaData),
                [
                    'transcode_output_version' => strval($this->transcodeOutputVersion->getValue()),
                ]
            ),
        ];
    }

    /**
     * @param string|null $baseUrl
     *
     * @return VideoSource[]
     */
    public function getVideoSources($baseUrl = null) :array
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

    public function getThumbnailUrl(string $baseUrl): string
    {
        return $baseUrl . 'thumbs-00001.png';
    }
}
