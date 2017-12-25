<?php

App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');

use Goalous\Model\Enum as Enum;

class AwsVideoTranscodeJobRequest
{
    /**
     * @var string
     */
    protected $inputS3FileKey;

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

    public function __construct(string $inputS3FileKey, string $awsEtsPipeLineId, Enum\Video\TranscodeOutputVersion $transcodeOutputVersion)
    {
        $this->inputS3FileKey = $inputS3FileKey;
        $this->awsEtsPipeLineId = $awsEtsPipeLineId;
        $this->transcodeOutputVersion = $transcodeOutputVersion;
    }

    /**
     * @return string
     */
    public function getInputS3FileKey(): string
    {
        return $this->inputS3FileKey;
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
     * return video output path
     * @see https://confluence.goalous.com/display/GOAL/Video+storage+structure
     *
     * @return string
     */
    public function getOutputKeyPrefix(): string
    {
        // e.g.
        // $this->inputS3FileKey() = uploads/111/222/abcdef1234567890/original
        // return 'streams/111/222/abcdef1234567890/'

        $urlSplits = array_slice(explode('/', trim($this->getInputS3FileKey(), '/')), 1, -1);
        return sprintf('streams/%s/', implode($urlSplits, '/'));
    }
}
