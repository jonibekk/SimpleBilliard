<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoUploadResultAwsS3', 'Model/Video/Results');
App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');
App::uses('AwsVideoTranscodeJobResult', 'Model/Video/Results');
App::import('Model/Video/Transcode', 'TranscodeOutputVersionDefinition');

use Aws\Exception\AwsException;
use Goalous\Model\Enum as Enum;

class AwsTranscodeJobClient
{
    public static function createJob(AwsVideoTranscodeJobRequest $awsVideoTranscodeRequest): AwsVideoTranscodeJobResult
    {
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion($awsVideoTranscodeRequest->getTranscodeOutputVersion());
        foreach ($awsVideoTranscodeRequest->getInputVideos() as $inputVideo) {
            $transcodeOutput->addInputVideo($inputVideo);
        }
        try {
            $result = self::createAwsEtsClient()->createJob(
                $transcodeOutput->getCreateJobArray(
                    $awsVideoTranscodeRequest->getAwsEtsPipeLineId(),
                    $awsVideoTranscodeRequest->getOutputKeyPrefix(),
                    $awsVideoTranscodeRequest->getUserMetaData(),
                    $awsVideoTranscodeRequest->isPutWaterMark()
                )
            );
            return new AwsVideoTranscodeJobResult($result->toArray());
        } catch (AwsException $exception) {
            return (new AwsVideoTranscodeJobResult([]))
                ->withErrorCodeAws($exception->getAwsErrorCode())
                ->withErrorMessage($exception->getMessage());
        }
    }

    private function createAwsEtsClient(): \Aws\ElasticTranscoder\ElasticTranscoderClient
    {
        return new \Aws\ElasticTranscoder\ElasticTranscoderClient([
            'region'   => 'ap-northeast-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_ELASTIC_TRANSCODER_KEY,
                'secret' => AWS_ELASTIC_TRANSCODER_SECRET_KEY,
            ],
        ]);
    }
}
