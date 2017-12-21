<?php

App::uses('AwsVideoTranscodeRequest', 'Model/Video/Requests');

use Goalous\Model\Enum as Enum;

class AwsVideoTranscodeRequestNewUpload implements AwsVideoTranscodeRequest
{
    /**
     * @var string
     */
    protected $s3FileKey;

    /**
     * @var string
     */
    protected $awsEtsPipeLine;

    /**
     * @var Enum\Video\TranscodeOutputVersion
     */
    protected $transcodeOutputVersion;

    public function __construct(string $s3FileKey, string $awsEtsPipeLine, Enum\Video\TranscodeOutputVersion $transcodeOutputVersion)
    {
        $this->s3FileKey = $s3FileKey;
        $this->awsEtsPipeLine = $awsEtsPipeLine;
        $this->transcodeOutputVersion = $transcodeOutputVersion;
    }


}
