<?php
App::import('Lib/Storage/Client', 'StorageClient');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/02
 * Time: 15:30
 */

/**
 * Storage Client for AWS S3
 *
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
 * Class BaseStorageClient
 */
abstract class BaseStorageClient implements StorageClient
{
    protected $s3Instance;

    public function __construct()
    {
        if (empty($this->s3Instance)) {
            // Initiate s3 client instance
            $this->s3Instance = AwsClientFactory::createS3ClientForFileStorage();
        }
    }

    /**
     * Create key for uploading
     *
     * @param string $base Base string to make key out of
     *
     * @return string
     */
    abstract protected function createFileKey(string $base): string;

    /**
     * Package file into JSON format
     *
     * @param UploadedFile $file
     *
     * @return string JSON encoded array
     */
    protected final function package(UploadedFile $file): string
    {
        if (empty ($file->getFileName()) || empty ($file->getEncodedFile())) {
            throw new InvalidArgumentException();
        }

        $array['file_name'] = $file->getFileName();
        $array['file_data'] = $file->getEncodedFile();

        $json = json_encode($array);

        if (empty($json)) {
            throw new RuntimeException();
        }
        return $json;
    }

    /**
     * Unpackage JSON into UploadedFile
     *
     * @param string $jsonEncoded
     *
     * @return UploadedFile
     */
    protected final function unpackage(string $jsonEncoded): UploadedFile
    {
        if (empty($jsonEncoded)) {
            throw new InvalidArgumentException();
        }

        $array = json_decode($jsonEncoded, true);

        if (empty($array['file_data']) || empty ($array['file_name'])) {
            throw new RuntimeException();
        }
        return new UploadedFile($array['file_data'], $array['file_name']);
    }

    /**
     * Upload a file to S3
     *
     * @param string $bucket
     * @param string $key
     * @param string $body
     * @param string $type
     *
     * @return mixed
     */
    protected function upload(string $bucket, string $key, string $body, string $type): bool
    {
        /**
         * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
         */
        $response = $this->s3Instance->putObject([
            'Bucket'               => $bucket,
            'Key'                  => $key,
            'Body'                 => $body,
            'ContentType'          => $type,
            'StorageClass'         => 'STANDARD',
            'ServerSideEncryption' => 'AES256',
            'ACL'                  => 'authenticated-read',
        ]);
        return !empty($response);
    }

    /**
     * Get special prefix when uploading from local
     *
     * @return string
     */
    protected final function getLocalPrefix(): string
    {
        if (ENV_NAME == 'local') {
            if (empty(AWS_S3_BUCKET_USERNAME)) {
                throw new RuntimeException("Please define AWS_S3_BUCKET_USERNAME");
            }
            return '/' . AWS_S3_BUCKET_USERNAME;
        }
        return '';
    }

    /**
     * If exists, remove heading '/' from key.
     * Processed recursively to remove multiple occurrences
     *
     * @param $key
     *
     * @return string
     */
    protected final function sanitize($key): string
    {
        //If start with '/', remove it
        if (strpos($key, "/") === 0) {
            $key = substr($key, 1);
            return $this->sanitize($key);
        } else {
            return $key;
        }
    }
}