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
use Goalous\Exception as GlException;

class S3Uploader extends BaseUploader
{
    private $s3Instance;

    public function __construct(int $userId, int $teamId)
    {
        parent::__construct($userId, $teamId);

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
        $key = $this->createBufferKey($file->getUUID());

        try {
            $this->upload(AWS_S3_BUCKET_TMP, $key, $this->package($file), "application/json");
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed saving to S3. Team: $this->teamId, User: $this->userId, File:" . $file->getFileName(),
                $exception->getTrace());
            throw new RuntimeException("Failed saving to S3");
        }

        return $file->getUUID();
    }

    /**
     * Get buffered file from S3
     * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#getobject
     *
     * @param string $uuid
     *
     * @return UploadedFile
     */
    public function getBuffer(string $uuid): UploadedFile
    {
        $key = $this->createBufferKey($uuid);

        try {
            $response = $this->s3Instance->getObject([
                'Bucket' => AWS_S3_BUCKET_TMP,
                'Key'    => $key,
            ]);
        } catch (S3Exception $exception) {
            throw new RuntimeException();
        }

        if (empty($response['Body'])) {
            throw new GlException\GoalousNotFoundException();
        }
        /** @var GuzzleHttp\Psr7\Stream $data */
        $data = $response['Body'];

        $dataArray = json_decode($data->getContents(), true);

        if (empty($dataArray)) {
            throw new RuntimeException();
        }

        $file = (new UploadedFile($dataArray['file_data'], $dataArray['file_name']))->withUUID($uuid);

        return $file;
    }

    /**
     * Save a file to permanent bucket in S3
     *
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     * @param string       $suffix
     *
     * @return bool
     */
    public function save(string $modelName, int $modelId, UploadedFile $file, string $suffix = ""): bool
    {
        $key = $this->createUploadKey($modelName, $modelId, $file->getFileName(true), $suffix, $file->getFileExt());
        try {
            $this->upload(S3_ASSETS_BUCKET, $key, $file->getBinaryFile(), $file->getMIME());
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed saving to S3. Team: $this->teamId, User: $this->userId, File:" . $file->getFileName(),
                $exception->getTrace());
            throw new RuntimeException("Failed saving to S3");
        }
        return true;
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

    /**
     * Remove an uploaded file from permanent storage
     *
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobject
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(string $modelName, int $modelId, string $fileName = ""): bool
    {
        $key = $this->createDeleteKey($modelName, $modelId, $fileName);

        $this->s3Instance->deleteObject([
            'Bucket' => S3_ASSETS_BUCKET,
            'Key'    => $key
        ]);

        return true;
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

    /**
     * Create key for buffering
     *
     * @param string $uuid
     *
     * @return string
     */
    protected function createBufferKey(string $uuid): string
    {
        $key = parent::createBufferKey($uuid);
        //In local env, append current user's name
        if (!empty(AWS_S3_BUCKET_USERNAME)) {
            $key = '/' . AWS_S3_BUCKET_USERNAME . $key;
        }
        return "upload" . $key;
    }

    /**
     * Create upload key
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $filename
     * @param string $suffix
     * @param string $fileExt
     *
     * @return string
     */
    protected function createUploadKey(
        string $modelName,
        int $modelId,
        string $filename,
        string $suffix,
        string $fileExt
    ): string {
        $key = parent::createUploadKey($modelName, $modelId, $filename, $suffix, $fileExt);
        if (!empty(AWS_S3_BUCKET_USERNAME)) {
            $key = '/' . AWS_S3_BUCKET_USERNAME . $key;
        }
        return $this->sanitize($key);
    }

    /**
     * Create delete key.
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $fileName
     * @param string $fileExt
     *
     * @return string
     */
    protected function createDeleteKey(
        string $modelName,
        int $modelId,
        string $fileName = "",
        string $fileExt = ""
    ): string {
        $key = parent::createDeleteKey($modelName, $modelId, $fileName, $fileExt);
        if (!empty(AWS_S3_BUCKET_USERNAME)) {
            $key = '/' . AWS_S3_BUCKET_USERNAME . $key;
        }
        return $this->sanitize($key);
    }

    /**
     * Remove heading '/' if exists
     *
     * @param string $key
     *
     * @return string
     */
    private function sanitize(string $key): string
    {
        //If start with '/', remove it
        if (strpos($key, "/") === 0) {
            $key = substr($key, 1);
        }
        return $key;
    }
}