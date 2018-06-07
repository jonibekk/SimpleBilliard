<?php

App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');

use Goalous\Enum as Enum;

class AwsVideoTranscodeJobRequest
{
    /**
     * @var TranscodeInputAwsEts[]
     */
    private $inputVideos     = [];

    /**
     * @var string
     */
    private $outputKeyPrefix;

    /**
     * @return string
     */
    public function getOutputKeyPrefix(): string
    {
        return $this->outputKeyPrefix;
    }

    /**
     * @param string $outputKeyPrefix
     */
    public function setOutputKeyPrefix(string $outputKeyPrefix)
    {
        $this->outputKeyPrefix = $outputKeyPrefix;
    }

    /**
     * @var string
     */
    protected $awsEtsPipeLineId;

    /**
     * @var Enum\Video\TranscodeOutputVersion
     */
    protected $transcodeOutputVersion;

    /**
     * @var array
     */
    protected $userMetaData = [];

    /**
     * @var bool
     */
    protected $putWaterMark = false;


    public function addInputVideo(AwsEtsTranscodeInput $transcodeInputAwsEts)
    {
        array_push($this->inputVideos, $transcodeInputAwsEts);
    }

    /**
     * @return AwsEtsTranscodeInput[]
     */
    public function getInputVideos(): array
    {
        return $this->inputVideos;
    }

    /**
     * @return bool
     */
    public function isPutWaterMark(): bool
    {
        return $this->putWaterMark;
    }

    /**
     * @param bool $putWaterMark
     */
    public function setPutWaterMark(bool $putWaterMark)
    {
        $this->putWaterMark = $putWaterMark;
    }

    /**
     * @return array
     */
    public function getUserMetaData(): array
    {
        return $this->userMetaData;
    }

    /**
     * @param array $userMetaData
     */
    public function setUserMetaData(array $userMetaData)
    {
        $this->userMetaData = $userMetaData;
    }

    public function __construct(string $outputKeyPrefix, string $awsEtsPipeLineId, Enum\Video\TranscodeOutputVersion $transcodeOutputVersion)
    {
        $this->outputKeyPrefix = $outputKeyPrefix;
        $this->awsEtsPipeLineId = $awsEtsPipeLineId;
        $this->transcodeOutputVersion = $transcodeOutputVersion;
    }

    /**
     * @return string
     */
    public function getAwsEtsPipeLineId(): string
    {
        return $this->awsEtsPipeLineId;
    }

    /**
     * @return Enum\Video\TranscodeOutputVersion
     */
    public function getTranscodeOutputVersion(): Enum\Video\TranscodeOutputVersion
    {
        return $this->transcodeOutputVersion;
    }

    /**
     * returning transcoder type
     * @return Enum\Video\Transcoder
     */
    public function getTranscoder(): Enum\Video\Transcoder
    {
        return Enum\Video\Transcoder::AWS_ETS();
    }
}
