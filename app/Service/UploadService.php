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
            if (!UploadValidator::validate($uploadedFile)) {
                throw new UploadException\UploadFailedException();
            }
        } catch (UploadException\UploadTypeException $uploadTypeException) {
            throw new InvalidArgumentException();
        } catch (UploadException\UploadSizeException $uploadSizeException) {
            throw new InvalidArgumentException(__("%sMB is the limit.",
                UploadValidator::MAX_FILE_SIZE));
        } catch (UploadException\UploadResolutionException $uploadResolutionException) {
            throw new InvalidArgumentException(__("%s pixels is the limit.",
                number_format(UploadImageValidator::MAX_PIXELS / 1000000)));
        }

        $uploader = new BufferStorageClient($userId, $teamId);

        return $uploader->save($uploadedFile);
    }

    /**
     * Get buffered data
     *
     * @param int $userId
     * @param int $teamId
     * @param     $uuid $key 13 char HEX UUID
     *
     * @return UploadedFile |null
     */
    public function getBuffer(int $userId, int $teamId, string $uuid)
    {
        if (preg_match("/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $uuid) == 0) {
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