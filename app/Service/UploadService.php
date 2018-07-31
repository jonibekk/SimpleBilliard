<?php
App::import('Service', 'AppService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Upload/Processor', 'UploadProcessor');
App::import('Lib/Upload/Uploader', 'UploaderFactory');
App::import('Lib/Upload/Uploader/Local', 'LocalUploader');
App::import('Lib/Upload/Uploader/S3', 'S3Uploader');
App::import('Validator/Lib/Upload', 'UploadValidator');
App::import('Validator/Lib/Upload', 'UploadImageValidator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/25
 * Time: 11:59
 */

use Goalous\Exception as GlException;

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
        $UploadedFile = new UploadedFile($encodedFile, $fileName);

        if (!UploadValidator::validate($UploadedFile)) {
            throw new GlException\Upload\UploadFailedException();
        }

        $uploader = UploaderFactory::generate($teamId, $userId);

        return $uploader->buffer($UploadedFile);
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

        $uploader = UploaderFactory::generate($teamId, $userId);

        return $uploader->getBuffer($uuid);
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