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

use Goalous\Exception as GlException;
use Goalous\Exception\Storage\Upload as UploadException;

class UploadService extends AppService
{
    /**
     * @param $userId
     * @param $teamId
     *
     * @return BufferStorageClient
     */
    private function getBufferStorageClient($userId, $teamId): BufferStorageClient
    {
        $registeredClient = ClassRegistry::getObject(BufferStorageClient::class);
        if ($registeredClient instanceof BufferStorageClient) {
            return $registeredClient;
        }
        return new BufferStorageClient($userId, $teamId);
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

        $uploader = new BufferStorageClient($userId, $teamId);

        return $uploader->get($uuid);
    }

    /**
     * Replace file UUID with actual file name
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $mainData
     *
     * @return bool
     */
    public static function link(int $userId, int $teamId, array &$mainData): bool
    {
        //TODO GL-7171
    }

    /**
     * Write the file to server
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $file
     *
     * @return bool
     */
    private function save(int $userId, int $teamId, string $file): bool
    {
        //TODO GL-7171
    }
}