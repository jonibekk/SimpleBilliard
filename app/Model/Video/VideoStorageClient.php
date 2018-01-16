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
     * Create pre-signed url in transcoded bucket
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/guide/service/s3-presigned-url.html
     * @param string   $key
     * @param DateTime $expireAt
     *
     * @return string
     */
    public static function createPreSignedUriFromTranscoded(string $key, \DateTime $expireAt): string
    {
        $s3Client = self::createS3Client();
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => AWS_S3_BUCKET_VIDEO_TRANSCODED,
            'Key'    => $key,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, $expireAt);
        return (string) $request->getUri();
    }

    /**
     * TODO: move this function to
     * create s3 client
     * @return \Aws\S3\S3Client
     */
    public static function createS3Client(): \Aws\S3\S3Client
    {
        return new \Aws\S3\S3Client([
            'region'      => 'ap-northeast-1',
            'version'     => 'latest',
            'credentials' => [
                'key'    => AWS_ELASTIC_TRANSCODER_KEY,
                'secret' => AWS_ELASTIC_TRANSCODER_SECRET_KEY,
            ],
        ]);
    }
}

