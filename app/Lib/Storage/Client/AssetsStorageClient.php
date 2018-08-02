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

class AssetsStorageClient extends BaseStorageClient implements StorageClient
{
    public function save(UploadedFile $file): string
    {
        // TODO: Implement save() method.
    }

    public function delete(string $fileName): bool
    {
        // TODO: Implement delete() method.
    }

    public function get(string $fileName): UploadedFile
    {
        // TODO: Implement get() method.
    }

    /**
     * Create MD5 Hash out of filename
     *
     * @param string $fileName
     *
     * @return string
     */
    protected final function createFileKey(string $fileName): string
    {
        return md5($fileName ?? "") . Configure::read('Security.salt');
    }

}