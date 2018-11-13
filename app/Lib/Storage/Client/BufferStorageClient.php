<?php
App::import('Lib/Storage/Client', 'StorageClient');
App::import('Lib/Storage/Client', 'BaseStorageClient');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/02
 * Time: 15:24
 */

use Aws\S3\Exception\S3Exception;
use Goalous\Exception as GlException;
use Aws\Exception\AwsException as AwsException;
use Aws\CommandPool;

class BufferStorageClient extends BaseStorageClient implements StorageClient
{
    /**
     * Current user ID
     *
     * @var int
     */
    private $userId;

    /**
     * Current team ID
     *
     * @var int
     */
    private $teamId;

    public function __construct(int $userId, int $teamId)
    {
        parent::__construct();

        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    /**
     * Save file into temporary bucket
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function save(UploadedFile $file): bool
    {
        $key = $this->createFileKey($file->getUUID());
        $key = $this->sanitize($key);

        try {
            $this->upload(AWS_S3_BUCKET_TMP, $key, $file->toJSON(), "application/json");
        } catch (S3Exception $exception) {
            GoalousLog::error("Failed saving to S3. Team: $this->teamId, User: $this->userId, File:" . $file->getFileName());
            throw new GlException\Storage\Upload\UploadFailedException("Failed saving to S3");
        }

        return true;
    }

    /**
     * Buffered file is automatically deleted within 2 days
     * Always return true
     *
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(string $fileName): bool
    {
        return true;
    }

    /**
     * Get buffered file from temporary bucket
     *
     * @param string $uuid
     *
     * @return UploadedFile
     */
    public function get(string $uuid): UploadedFile
    {
        $key = $this->createFileKey($uuid);
        $key = $this->sanitize($key);

        try {
            $response = $this->s3Instance->getObject([
                'Bucket' => AWS_S3_BUCKET_TMP,
                'Key'    => $key,
            ]);
        } catch (S3Exception $exception) {
            if ($exception->getStatusCode() === 404) {
                throw new GlException\GoalousNotFoundException("Cannot find buffered file");
            } else {
                throw new RuntimeException($exception->getMessage());
            }
        }

        if (empty($response['Body'])) {
            throw new UnexpectedValueException("File body can't be empty");
        }

        /** @var GuzzleHttp\Psr7\Stream $data */
        $data = $response['Body'];

        return UploadedFile::generate($data->getContents());
    }

    /**
     * Get buffered file from temporary bucket
     *
     * @param string[] $uuids
     * @return UploadedFile[]
     * @throws Exception
     */
    public function bulkGet(array $uuids): array
    {
        try {

            $commands = [];

            foreach ($uuids as $uuid) {
                $key = $this->createFileKey($uuid);
                $key = $this->sanitize($key);

                $commands[] = $this->getCommandForGetObject(AWS_S3_BUCKET_TMP, $key);
            }
            $responses = CommandPool::batch($this->s3Instance, $commands);
        } catch (AwsException $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        $fileContents = [];
        foreach ($responses as $response) {
            if (empty($response['Body'])) {
                throw new UnexpectedValueException("File body can't be empty");
            }
            /** @var GuzzleHttp\Psr7\Stream $data */
            $data = $response['Body'];
            $fileContents[] = UploadedFile::generate($data->getContents());
        }
        return $fileContents;
    }

    /**
     * Create buffer key
     *
     * @param string $uuid
     *
     * @return string
     */
    protected function createFileKey(string $uuid): string
    {
        if (empty($uuid)) {
            throw new InvalidArgumentException();
        }
        return "/upload" . $this->getLocalPrefix() . "/$this->teamId/$this->userId/" . $uuid . ".json";
    }

}
