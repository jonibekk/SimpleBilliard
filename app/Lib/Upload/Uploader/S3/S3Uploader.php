<?php
App::import('Lib/Upload/Uploader', 'BaseUploader');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/30
 * Time: 16:11
 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
 */

use Aws\S3\Exception\S3Exception;

class S3Uploader extends BaseUploader
{
    private $s3Instance;

    public function __construct(int $teamId, int $userId, string $webroot)
    {
        parent::__construct($teamId, $userId, $webroot);

        if (empty($this->s3Instance)) {
            // Initiate s3 client instance
            $this->s3Instance = AwsClientFactory::createS3ClientForFileStorage();
        }
    }

    /**
     * Upload the file to a temp S3 bucket
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function buffer(UploadedFile $file): string
    {
        $key = $this->createKey($file->getUUID());

        try {
            $this->upload(S3_UPLOAD_BUCKET, $key, $this->package($file), "application/json");
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed saving to S3. Team: $this->teamId, User: $this->userId, File:" . $file->getFileName(),
                $exception->getTrace());
            throw new RuntimeException("Failed saving to S3");
        }

        return $file->getUUID();
    }

    /**
     * Get buffered file from S3
     *
     * @param string $uuid
     *
     * @return UploadedFile
     */
    public function getBuffer(string $uuid): UploadedFile
    {
        // TODO GL-7171
    }

    /**
     * Save a file to permanent bucket in S3
     *
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function save(string $modelName, int $modelId, UploadedFile $file): bool
    {
        // TODO GL-7171
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
        //If start with '/', remove it
        if (strpos($key, "/") === 0) {
            $key = substr($key, 1);
        }

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

    public function delete(string $modelName, int $modelId, string $uuid): bool
    {
        // TODO GL-7171
    }

    /**
     * On S3, buffered file will be deleted automatically
     *
     * @param string $uuid
     *
     * @return bool|mixed
     */
    public function deleteBuffer(string $uuid): bool
    {
        return true;
    }
}