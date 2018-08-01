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
     */
    public function link(int $userId, int $teamId, array &$mainData)
    {
        foreach ($mainData as $key => &$value) {
            if (preg_match("/FILE [A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $value)) {

                $uuid = sscanf($value, 'FILE %s');

                $file = $this->read($userId, $teamId, $uuid);

                if (empty($file)) {
                    throw new GlException\GoalousNotFoundException("Specified buffered file not found");
                }

                if ($this->save($userId, $teamId, $file)) {
                    $value = $file->getFileName();
                } else {
                    throw new RuntimeException();
                }
            }
        }
    }

    /**
     * Write the file to server
     *
     * @param int          $userId
     * @param int          $teamId
     * @param UploadedFile $file
     *
     * @return bool
     */
    private function save(int $userId, int $teamId, UploadedFile $file): bool
    {
        //TODO GL-7171
    }
}