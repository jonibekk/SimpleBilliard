<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoUploadResultAwsS3', 'Model/Video/Results');
App::import('Model/Video/Transcode', 'TranscodeOutputVersionDefinition');

use Goalous\Model\Enum as Enum;

class AwsTranscodeJobClient
{
    public static function createJob(string $inputKey, string $pipeLineId, string $outputKeyPrefix, array $userMetaData, bool $putWaterMark)
    {
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion(Enum\Video\TranscodeOutputVersion::V1());
        try {
            $result = self::createAwsEtsClient()->createJob(
                $transcodeOutput->getCreateJobArray($inputKey, $pipeLineId, $outputKeyPrefix, $userMetaData, $putWaterMark)
            );
            GoalousLog::info('transcode job create result', $result->toArray());
        } catch (\Aws\Common\Exception\ServiceResponseException $exception) {
            GoalousLog::info('transcode job create result failed', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
            return;// :TODO
        }
        return;// :TODO
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
