<?php

use Goalous\Model\Enum as Enum;

/**
 * Class TranscodeInfo
 *
 * this class helps managing key-values of
 * video_streams.transcode_info
 *
 * this class helps two things
 *
 * 1. input json string
 *      $transcodeInfo = TranscodeInfo::createFromJson("{}");
 *
 *      // set values
 *      $transcodeInfo->setTranscodeJobId("1234567890");
 *      $transcodeInfo->set...(something);
 * 2. output json string
 *      $transcodeInfoJsonString = $transcodeInfo->toJson();
 *
 * # json data structure
 * @see https://confluence.goalous.com/display/GOAL/Video+Upload+DB+Design#VideoUploadDBDesign-video_streams.transcode_info
 */
class TranscodeInfo
{
    const HASH_TRANSCODER       = 'transcoder';
    const HASH_TRANSCODE_JOB_ID = 'job_id';
    const HASH_TRANSCODE_NO_PROGRESS = 'transcode.no_progress';
    const HASH_TRANSCODE_ERRORS = 'transcode.errors';
    const HASH_TRANSCODE_WARNINGS = 'transcode.warnings';

    /**
     * @var array
     */
    private $transcodeInfo = [];

    /**
     * set transcoder type
     *
     * @param Enum\Video\Transcoder $transcoder
     */
    public function setTranscoderType(Enum\Video\Transcoder $transcoder)
    {
        $this->transcodeInfo = Hash::insert($this->transcodeInfo, self::HASH_TRANSCODER, $transcoder->getValue());
    }

    /**
     * set transcode output version
     *
     * @param string $jobId
     */
    public function setTranscodeJobId(string $jobId)
    {
        $this->transcodeInfo = Hash::insert($this->transcodeInfo, self::HASH_TRANSCODE_JOB_ID, $jobId);
    }

    /**
     * set true if transcode has no progress
     *
     * @param bool $isNoProgress
     */
    public function setTranscodeNoProgress(bool $isNoProgress)
    {
        $this->transcodeInfo = Hash::insert($this->transcodeInfo, self::HASH_TRANSCODE_NO_PROGRESS, $isNoProgress);
    }



    /**
     * add transcode warning message
     *
     * @param string $warningMessage
     */
    public function addTranscodeWarning(string $warningMessage)
    {
        $warnings = Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_WARNINGS, []);
        array_push($warnings, $warningMessage);
        $this->transcodeInfo = Hash::insert($this->transcodeInfo, self::HASH_TRANSCODE_WARNINGS, $warnings);
    }

    /**
     * get transcode warnings
     *
     * @return array
     */
    public function getTranscodeWarnings(): array
    {
        return Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_WARNINGS, []);
    }

    /**
     * add transcode error message
     *
     * @param string $errorMessage
     */
    public function addTranscodeError(string $errorMessage)
    {
        $errors = Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_ERRORS, []);
        array_push($errors, $errorMessage);
        $this->transcodeInfo = Hash::insert($this->transcodeInfo, self::HASH_TRANSCODE_ERRORS, $errors);
    }

    /**
     * get transcode errors
     *
     * @return array
     */
    public function getTranscodeErrors(): array
    {
        return Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_ERRORS, []);
    }

    /**
     * @return Enum\Video\Transcoder|null
     */
    public function getTranscoderType()//: ?Enum\Video\Transcoder
    {
        $transcoder = Hash::get($this->transcodeInfo, self::HASH_TRANSCODER);
        if (is_null($transcoder)) {
            return null;
        }
        return new Enum\Video\Transcoder($transcoder);
    }

    /**
     * @return string|null
     */
    public function getTranscodeJobId()//: ?string
    {
        return Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_JOB_ID);
    }

    /**
     * @return bool
     */
    public function isTranscodeNoProgress(): bool
    {
        return Hash::get($this->transcodeInfo, self::HASH_TRANSCODE_NO_PROGRESS, false);
    }

    private function __construct(array $transcodeInfo)
    {
        $this->transcodeInfo = $transcodeInfo;
    }

    /**
     * create enw instance from json string
     * @param string $jsonTranscodeInfo
     *
     * @return TranscodeInfo
     */
    public static function createFromJson(string $jsonTranscodeInfo): self
    {
        $transcodeInfo = json_decode($jsonTranscodeInfo, true);
        if (is_null($transcodeInfo)) {
            throw new InvalidArgumentException("fail to parse from json");
        }
        return new self($transcodeInfo);
    }

    /**
     * create an empty instance
     *
     * @return TranscodeInfo
     */
    public static function createNew(): self
    {
        return new self([]);
    }

    /**
     * call this function when saving to video_streams.transcode_info
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->transcodeInfo);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
