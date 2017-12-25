<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoUploadResultAwsS3', 'Model/Video/Results');
App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');
App::uses('AwsVideoTranscodeJobResult', 'Model/Video/Results');
App::import('Model/Video/Transcode', 'TranscodeOutputVersionDefinition');

use Goalous\Model\Enum as Enum;

class AwsTranscodeJobClient
{
    public static function createJob(AwsVideoTranscodeJobRequest $awsVideoTranscodeRequest): AwsVideoTranscodeJobResult
    {
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion(Enum\Video\TranscodeOutputVersion::V1());
        try {
            $result = self::createAwsEtsClient()->createJob(
                $transcodeOutput->getCreateJobArray(
                    $awsVideoTranscodeRequest->getInputS3FileKey(),
                    $awsVideoTranscodeRequest->getAwsEtsPipeLineId(),
                    $awsVideoTranscodeRequest->getOutputKeyPrefix(),
                    $awsVideoTranscodeRequest->getUserMetaData(),
                    $awsVideoTranscodeRequest->isPutWaterMark()
                )
            );
            return new AwsVideoTranscodeJobResult($result->toArray());
        } catch (\Aws\Common\Exception\ServiceResponseException $exception) {
            return (new AwsVideoTranscodeJobResult([]))
                ->withErrorCodeAws($exception->getAwsErrorCode())
                ->withErrorMessage($exception->getMessage());
        } catch (\Throwable $throwable) {
            return (new AwsVideoTranscodeJobResult([]))
                ->withErrorCodeAws(0)
                ->withErrorMessage($throwable->getMessage());
        }
    }

    private function createAwsEtsClient(): \Aws\ElasticTranscoder\ElasticTranscoderClient
    {
        return \Aws\ElasticTranscoder\ElasticTranscoderClient::factory([
            // TODO: move configurations to config files
            'region'   => 'ap-northeast-1',
            'credentials' => [
                'key'    => "AKIAJWRB3ISRYGDYHV5A",
                'secret' => "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",
            ],
        ]);
    }
}
