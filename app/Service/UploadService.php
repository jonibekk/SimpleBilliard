<?php
App::import('Service', 'AppService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Storage/Processor', 'UploadProcessor');
App::import('Lib/Storage/Client', 'BufferStorageClient');
App::import('Lib/Storage/Client', 'AssetsStorageClient');
App::import('Validator/Lib/Storage', 'UploadValidator');
App::import('Validator/Lib/Storage', 'UploadImageValidator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/25
 * Time: 11:59
 */

use Goalous\Exception\Storage\Upload as UploadException;

class UploadService extends AppService
{
    /**
     * @param int $userId
     * @param int $teamId
     *
     * @return BufferStorageClient
     */
    private function getBufferStorageClient(int $userId, int $teamId): BufferStorageClient
    {
        $registeredClient = ClassRegistry::getObject(BufferStorageClient::class);
        if ($registeredClient instanceof BufferStorageClient) {
            return $registeredClient;
        }
        return new BufferStorageClient($userId, $teamId);
    }

    /**
     * @param string $modelName
     * @param int    $modelId
     *
     * @return AssetsStorageClient
     */
    private function getAssetsStorageClient(string $modelName, int $modelId): AssetsStorageClient
    {
        $registeredClient = ClassRegistry::getObject(AssetsStorageClient::class);
        if ($registeredClient instanceof AssetsStorageClient) {
            return $registeredClient;
        }
        return new AssetsStorageClient($modelName, $modelId);
    }

    /**
     * Add a file into buffer
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $encodedFile
     * @param string $fileName
     *
     * @return string
     */
    public function buffer(int $userId, int $teamId, string $encodedFile, string $fileName): string
    {
        $uploadedFile = new UploadedFile($encodedFile, $fileName);

        try {
            UploadValidator::validate($uploadedFile);
        } catch (UploadException\UploadValidationException $uploadValidationException) {
            throw new InvalidArgumentException($uploadValidationException->getMessage());
        }

        $uploader = $this->getBufferStorageClient($userId, $teamId);

        return $uploader->save($uploadedFile);
    }

    /**
     * Get buffered data
     *
     * @param int $userId
     * @param int $teamId
     * @param     $uuid $key 13 char HEX UUID
     *
     * @return UploadedFile
     */
    public function getBuffer(int $userId, int $teamId, string $uuid): UploadedFile
    {
        if (preg_match(UploadedFile::UUID_REGEXP, $uuid) == 0) {
            throw new InvalidArgumentException(("Invalid FILE UUID"));
        }

        $uploader = $this->getBufferStorageClient($userId, $teamId);

        return $uploader->get($uuid);
    }

    /**
     * Write file to main storage
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
        $assetStorageClient = $this->getAssetsStorageClient($modelName, $modelId);

        return $assetStorageClient->save($file, $suffix);
    }

    /**
     * Delete multiple objects based on same prefix
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $fileName Specific file name to delete
     *
     * @return bool
     */
    public function deleteAsset(string $modelName, int $modelId, string $fileName = ""): bool
    {
        $assetStorageClient = $this->getAssetsStorageClient($modelName, $modelId);

        return $assetStorageClient->delete($fileName);
    }
}