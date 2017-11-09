<?php

class VideoStorageClient
{
    public static function upload(VideoUploadRequest $uploadRequest): VideoUploadResult
    {
        $s3Client = self::createS3Client();

        try {
            $awsResult = $s3Client->putObject($uploadRequest->getObjectArray());
        } catch (\Aws\Common\Exception\ServiceResponseException $exception) {
            return VideoUploadResultAwsS3::createFromAwsException($exception);
        }
        return VideoUploadResultAwsS3::createFromGuzzleModel($awsResult);
    }

    /**
     * TODO: move this function to
     * create s3 client
     * @return \Aws\S3\S3Client
     */
    public static function createS3Client(): \Aws\S3\S3Client
    {
        return \Aws\S3\S3Client::factory([
            // TODO: move configurations to config files
            'region'   => 'ap-northeast-1',
            'credentials' => [
                'key'    => "a",
                'secret' => "a",
            ],
        ]);
    }
}
