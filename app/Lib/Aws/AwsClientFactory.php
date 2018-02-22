<?php

/**
 * Create and return each AWS service client
 *
 * Class AwsClientFactory
 */
class AwsClientFactory
{
    /**
     * Create attached / image files s3 storage client
     *
     * @return \Aws\S3\S3Client
     */
    public static function createS3ClientForFileStorage(): \Aws\S3\S3Client
    {
        return new \Aws\S3\S3Client([
            'region'      => 'ap-northeast-1',
            // @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
            'version'     => '2006-03-01',
            'credentials' => [
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            ],
        ]);
    }

    /**
     * Create original video storage client
     *
     * @return \Aws\S3\S3Client
     */
    public static function createS3ClientForOriginalVideoStorage(): \Aws\S3\S3Client
    {
        return new \Aws\S3\S3Client([
            'region'      => 'ap-northeast-1',
            // @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
            'version'     => '2006-03-01',
            'credentials' => [
                'key'    => AWS_ELASTIC_TRANSCODER_KEY,
                'secret' => AWS_ELASTIC_TRANSCODER_SECRET_KEY,
            ],
        ]);
    }

    /**
     * Create Elastic transcoder client
     *
     * @return \Aws\ElasticTranscoder\ElasticTranscoderClient
     */
    public static function createElasticTranscoderClient(): \Aws\ElasticTranscoder\ElasticTranscoderClient
    {
        return new \Aws\ElasticTranscoder\ElasticTranscoderClient([
            'region'   => 'ap-northeast-1',
            // @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-elastictranscoder-2012-09-25.html
            'version' => '2012-09-25',
            'credentials' => [
                'key'    => AWS_ELASTIC_TRANSCODER_KEY,
                'secret' => AWS_ELASTIC_TRANSCODER_SECRET_KEY,
            ],
        ]);
    }
}