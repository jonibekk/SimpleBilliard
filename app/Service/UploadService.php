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
     * Write file to main storage
     *
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     * @param string       $suffix
     *
     * @return bool
     */
    public function save(
        string $modelName,
        int $modelId,
        UploadedFile $file,
        string $suffix = ""
    ): bool {

        $assetStorageClient = new AssetsStorageClient($modelName, $modelId);

        return $assetStorageClient->save($file, $suffix);
    }

    /**
     * Delete a specific file
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $fileName Can be input as:
     *                         Pattern 1: d8d19701c_original.jpg -> No need for suffix & ext
     *                         Pattern 2: test.png -> Need to input suffix & ext
     * @param string $suffix   Optional. For specifying suffixes like `_original`
     * @param string $fileExt  Optional. If suffix is specified, file extension will be needed
     *
     * @return bool
     */
    public function deleteAsset(
        string $modelName,
        int $modelId,
        string $fileName,
        string $suffix = "",
        string $fileExt = ""
    ): bool {
        if (!empty($suffix) && empty($fileExt)) {
            throw new InvalidArgumentException();
        }

        $assetStorageClient = new AssetsStorageClient($modelName, $modelId);

        return $assetStorageClient->delete($fileName, $suffix, $fileExt);
    }

    /**
     * Delete multiple objects based on same prefix
     *
     * @param string $modelName
     * @param int    $modelId
     *
     * @return bool
     */
    public function deleteAssets(string $modelName, int $modelId): bool
    {
        $assetStorageClient = new AssetsStorageClient($modelName, $modelId);

        return $assetStorageClient->deleteByPrefix();
    }
}