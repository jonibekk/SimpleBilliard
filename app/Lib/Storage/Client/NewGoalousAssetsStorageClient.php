<?php
App::import('Lib/Storage/Client', 'StorageClient');
App::import('Lib/Storage/Client', 'BaseStorageClient');
App::import('Lib/Storage', 'UploadedFile');

use Aws\S3\Exception\S3Exception;
use Aws\CommandPool;

class NewGoalousAssetsStorageClient extends BaseStorageClient
{
    private $assetKeyPrefixList = ['main', 'scripts', 'runtime', 'polyfills'];
    public function __construct()
    {
        parent::__construct();
    }

    public function getKeys(): array
    {
        if (AWS_S3_BUCKET_NEW_GOALOUS === '') {
            return [];
        }

        try {
            // At first, prefetch only main js files to load faster when old Goalous to new Goalous
            foreach ($this->assetKeyPrefixList as $prefix) {
                $commands[] = $this->getCommandForListObject($prefix);
            }

            $responses = CommandPool::batch($this->s3Instance, $commands);

        } catch (S3Exception $exception) {
            throw new RuntimeException();
        }

        $allKeys = [];
        foreach ($responses as $response) {
            if (empty($response['Contents'])) {
                continue;
            }
            $resKeys = Hash::extract($response['Contents'], '{n}.Key') ?? [];
            $allKeys = array_merge($allKeys, $resKeys);
        }

        return $allKeys;
    }

    /**
     * For bulk commands execution
     * Get command for list object
     *
     * @param string $prefix
     * @return \Aws\Command
     */
    protected final function getCommandForListObject(string $prefix): Aws\Command {
        return $this->s3Instance->getCommand('ListObjects', [
            'Bucket' => AWS_S3_BUCKET_NEW_GOALOUS,
            'Prefix' => $prefix.'.',
        ]);
    }



    /**
     * Save file into specified bucket
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function save(UploadedFile $file): bool
    {
        // TODO: Implement save() method.
    }

    /**
     * Delete file from a bucket
     *
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(string $fileName): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * Get a file from a bucket
     *
     * @param string $fileName
     *
     * @return UploadedFile
     */
    public function get(string $fileName): UploadedFile
    {
        // TODO: Implement get() method.
    }

    /**
     * Create key for uploading
     *
     * @param string $base Base string to make key out of
     *
     * @return string
     */
    protected function createFileKey(string $base): string
    {
        // TODO: Implement createFileKey() method.
    }
}
