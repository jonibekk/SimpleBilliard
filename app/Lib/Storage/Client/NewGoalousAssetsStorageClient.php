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
            $result = $this->s3Instance->listObjectsV2([
                'Bucket' => AWS_S3_BUCKET_NEW_GOALOUS,
            ]);
        } catch (S3Exception $exception) {
            throw new RuntimeException();
        }

        $allKeys = Hash::extract($result->toArray(), 'Contents.{n}.Key');
        // Filter only js for loading in root folder
        $keys = preg_grep('/^[a-zA-Z0-9.]+.js$/', $allKeys);
        return $keys;
    }

    /**
     * For bulk commands execution
     * Get command for list object
     *
     * @param string $prefix
     * @return \Aws\Command
     */
    protected final function getCommandForListObject(string $prefix = ''): Aws\Command {
        $options = [
            'Bucket' => AWS_S3_BUCKET_NEW_GOALOUS,
        ];

        if (!empty($prefix)) {
            $options['Prefix'] = $prefix;
        }
        return $this->s3Instance->getCommand('ListObjects', $options);
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
