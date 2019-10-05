<?php
App::import('Lib/Storage/Client', 'StorageClient');
App::import('Lib/Storage/Client', 'BaseStorageClient');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/02
 * Time: 15:25
 */

use Aws\S3\Exception\S3Exception;
use Goalous\Exception as GlException;
use Aws\CommandPool;

class AssetsStorageClient extends BaseStorageClient implements StorageClient
{
    /**
     * Name of the model
     *
     * @var string
     */
    private $modelName;

    /**
     * ID of the model
     *
     * @var int
     */
    private $modelId;

    public function __construct(string $modelName, int $modelId)
    {
        parent::__construct();
        $this->modelName = $modelName;
        $this->modelId = $modelId;
    }

    /**
     * Upload single file
     *
     * @param UploadedFile $file
     * @param string       $suffix
     *
     * @return bool
     */
    public function save(UploadedFile $file, string $suffix = ""): bool
    {
        $key = $this->createFileKey($file->getFileName(), $suffix, $file->getFileExt());
        $key = $this->sanitize($key);

        try {
            $this->upload(S3_ASSETS_BUCKET, $key, $file->getBinaryFile(), $file->getMimeWithCharset());

        } catch (S3Exception $exception) {
            GoalousLog::error("Failed saving to S3. Model: $this->modelName, ID: $this->modelId, File:" . $file->getFileName());
            throw new RuntimeException("Failed saving to S3");
        }

        return true;
    }

    /**
     * Bulk upload
     *
     * @param UploadedFile[] $files
     *
     * @return bool
     */
    public function bulkSave(array $files): bool
    {
        try {
            $commands = [];

            foreach ($files as $file) {
                $key = $this->createFileKey($file->getFileName(), $file->getSuffix(), $file->getFileExt());
                $key = $this->sanitize($key);

                $commands[] = $this->getCommandForUpload(S3_ASSETS_BUCKET, $key, $file->getBinaryFile(),
                    $file->getMimeWithCharset());
            }

            CommandPool::batch($this->s3Instance, $commands);

        } catch (AwsException $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        return true;
    }

    public function delete(string $fileName): bool
    {
        $key = $this->createFileKey($fileName);
        $key = $this->sanitize($key);

        try {
            $result = $this->s3Instance->listObjectsV2([
                'Bucket' => S3_ASSETS_BUCKET,
                'Prefix' => $key
            ]);
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed listing objects from S3. Model: $this->modelName, ID: $this->modelId");
            throw new RuntimeException("Failed  listing objects from S3.");
        }

        $keyList = Hash::extract($result->toArray(), 'Contents.{n}.Key');

        foreach ($keyList as $key) {
            $this->deleteByFullKey($key);
        }

        return true;
    }

    public function deleteByFullKey(string $key): bool
    {
        $key = $this->sanitize($key);

        try {
            $this->s3Instance->deleteObject([
                'Bucket' => S3_ASSETS_BUCKET,
                'Key'    => $key,
            ]);
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed deleting from S3.  Key: " . $key);
            throw new RuntimeException("Failed deleting from to S3");
        }

        return true;

    }

    public function get(string $fileName, string $suffix = "", string $ext = ""): UploadedFile
    {
        $key = $this->createFileKey($fileName, $suffix, $ext);
        $key = $this->sanitize($key);

        try {
            $response = $this->s3Instance->getObject([
                'Bucket' => S3_ASSETS_BUCKET,
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

        $file = new UploadedFile($data->getContents(), $fileName, true);

        return $file;
    }

    /**
     * @param string $fileName File name
     * @param string $suffix
     * @param string $fileExt  If extension not given, assume filename is already hashed
     *
     * @return string
     */
    protected final function createFileKey(string $fileName, string $suffix = "", string $fileExt = ""): string
    {
        $fileInfo = UploadBehavior::_pathinfo($fileName);

        $hashedFileName = md5(($fileInfo['filename'] ?: "") . Configure::read('Security.salt'));

        if (empty($hashedFileName)) {
            $message = "Empty hashed file name";
            GoalousLog::error($message);
            throw new RuntimeException($message);
        }

        $key = $this->getLocalPrefix() . "/" . Inflector::tableize($this->modelName) . "/" . $this->modelId . "/" . $hashedFileName;

        if (!empty($fileExt)) {
            if (!empty($suffix)) {
                $key .= $suffix;
            }
            $key .= "." . $fileExt;
        }

        return $key;
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
            'ACL'                  => 'public-read',
        ]);
        return !empty($response);
    }

    /**
     * For bulk commands execution
     * Get command for uploading a file to S3
     *
     * @param string $bucket
     * @param string $key
     * @param string $body
     * @param string $type
     *
     * @return \Aws\Command
     */
    protected function getCommandForUpload(string $bucket, string $key, string $body, string $type): Aws\Command
    {
        return $this->s3Instance->getCommand('PutObject', [
            'Bucket'               => $bucket,
            'Key'                  => $key,
            'Body'                 => $body,
            'ContentType'          => $type,
            'StorageClass'         => 'STANDARD',
            'ServerSideEncryption' => 'AES256',
            'ACL'                  => 'public-read',
        ]);
    }
}
