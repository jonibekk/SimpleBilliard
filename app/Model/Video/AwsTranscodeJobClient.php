<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoUploadResultAwsS3', 'Model/Video/Results');

class AwsTranscodeJobClient
{
    public static function createJob(): VideoUploadResult
    {
        // TODO:
        $inputKey = "";
        $outputKeyPrefix = "";
        $videoId = 1;
        $videoStreamId = 1;
        try {
            self::createAwsEtsClient()->createJob([
                'PipelineId' => '1510729662392-4lzb0n',
                'OutputKeyPrefix' => $outputKeyPrefix,
                'Input' => [
                    'Key' => $inputKey,
                    'FrameRate' => 'auto',
                    'Resolution' => 'auto',
                    'AspectRatio' => 'auto',
                    'Interlaced' => 'auto',
                    'Container' => 'auto',
                ],
                'Outputs' => [
                    [
                        'Key' => "ts_500k/video",
                        'ThumbnailPattern' => "thumbs-{count}",
                        'PresetId' => "1513234427744-pkctj7",
                        'Rotate' => "auto",
                        'SegmentDuration' => "10",
                        'Watermarks' => [
                            'InputKey' => "images/watermark_h264.png",
                            'PresetWatermarkId' => "TopLeft",
                        ],
                    ],
                    [
                        'Key' => "webm_500k/video.webm",
                        'ThumbnailPattern' => "thumbs-{count}",
                        'PresetId' => "1513327166916-ghbctw",
                        'Rotate' => "auto",
                        'Watermarks' => [
                            'InputKey' => "images/watermark_vp9.png",
                            'PresetWatermarkId' => "TopLeft",
                        ],
                    ],
                ],
                'Playlists' => [
                    'Format' => 'HLSv3',
                    'Name' => 'playlist',
                    'OutputKeys' => [
                        'ts_500k/video',
                    ]
                ],
                'UserMetadata' => [
                    'video.id' => $videoId,
                    'video_streams.id' => $videoStreamId,
                ],
            ]);
        } catch (\Aws\Common\Exception\ServiceResponseException $exception) {
            return VideoUploadResultAwsS3::createFromAwsException($exception);
        }
        return VideoUploadResultAwsS3::createFromGuzzleModel($awsResult)->withResourcePath($uploadRequest->getResourcePath());
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
