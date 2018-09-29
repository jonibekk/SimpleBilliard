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
     * @var Enum\Model\Video\TranscodeOutputVersion
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

    public function __construct(string $outputKeyPrefix, string $awsEtsPipeLineId, Enum\Model\Video\TranscodeOutputVersion $transcodeOutputVersion)
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
     * @return Enum\Model\Video\TranscodeOutputVersion
     */
    public function getTranscodeOutputVersion(): Enum\Model\Video\TranscodeOutputVersion
    {
        return $this->transcodeOutputVersion;
    }

    /**
     * returning transcoder type
     * @return Enum\Model\Video\Transcoder
     */
    public function getTranscoder(): Enum\Model\Video\Transcoder
    {
        return Enum\Model\Video\Transcoder::AWS_ETS();
    }
}
