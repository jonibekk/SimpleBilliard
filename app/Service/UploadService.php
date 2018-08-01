<?php
App::import('Service', 'AppService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Upload/Processor', 'UploadProcessor');
App::import('Lib/Upload/Uploader', 'UploaderFactory');
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

        $file = $uploader->getBuffer($uuid);

        if (empty($file)) {
            throw new GlException\GoalousNotFoundException("Specified buffered file not found");
        }

        return $file;
    }

    /**
     * Replace file UUID with actual file name
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $mainData
     * @param array $keys JSON data keys that should be replaced with filename
     *
     * @return array of UUIDs
     */
    public function link(int $userId, int $teamId, array &$mainData, array $keys): array
    {
        $uuids = [];

        foreach ($keys as $key) {

            if (!array_key_exists($key, $mainData)) {
                throw new InvalidArgumentException();
            }

            //TODO add validation to POST data
            $uuid = sscanf($mainData[$key], 'FILE %s');

            $uploader = UploaderFactory::generate($userId, $teamId);
            $file = $uploader->getBuffer($uuid);

            if (empty($file)) {
                throw new GlException\GoalousNotFoundException("Specified buffered file not found");
            }

            $mainData[$key] = $file->getFileName();
            $uuids[] = $file->getUUID();
        }

        return $uuids;
    }

    /**
     * Write file to main storage
     *
     * @param int          $userId
     * @param int          $teamId
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     * @param string       $suffix
     *
     * @return bool
     */
    public function save(
        int $userId,
        int $teamId,
        string $modelName,
        int $modelId,
        UploadedFile $file,
        string $suffix = ""
    ): bool {
        $uploader = UploaderFactory::generate($userId, $teamId);

        return $uploader->save($modelName, $modelId, $file, $suffix);
    }

    /**
     * Remove a uploaded file
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $modelName
     * @param int    $modelId
     * @param string $fileName
     *
     * @return bool
     */
    public function delete(int $userId, int $teamId, string $modelName, int $modelId, string $fileName = ""): bool
    {
        $uploader = UploaderFactory::generate($userId, $teamId);

        return $uploader->delete($modelName, $modelId, $fileName);
    }
}