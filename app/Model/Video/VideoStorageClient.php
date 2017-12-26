<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoUploadResultAwsS3', 'Model/Video/Results');

use Aws\Exception\AwsException;

class VideoStorageClient
{
    public static function upload(VideoUploadRequest $uploadRequest): VideoUploadResult
    {
        $s3Client = self::createS3Client();

        try {
            $awsResult = $s3Client->putObject($uploadRequest->getObjectArray());
        } catch (AwsException $exception) {
            return VideoUploadResultAwsS3::createFromAwsException($exception);
        }
        return VideoUploadResultAwsS3::createFromAwsResult($awsResult)->withResourcePath($uploadRequest->getResourcePath());
    }

    /**
     * TODO: move this function to
     * create s3 client
     * @return \Aws\S3\S3Client
     */
    public static function createS3Client(): \Aws\S3\S3Client
    {
        return new \Aws\S3\S3Client([
            // TODO: move configurations to config files
            'region'      => 'ap-northeast-1',
            'version'     => 'latest',
            'credentials' => [
                'key'    => "AKIAJWRB3ISRYGDYHV5A",
                'secret' => "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",
            ],
        ]);
    }
}
