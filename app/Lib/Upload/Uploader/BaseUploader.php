<?php
App::import('Lib/Upload/Uploader', 'Uploader');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/30
 * Time: 18:16
 */

use Goalous\Exception as GlException;

abstract class BaseUploader implements Uploader
{
    /** @var int */
    protected $teamId;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $webroot;

    public function __construct(int $teamId, int $userId, string $webroot)
    {
        if (empty($teamId) || empty($userId) || empty($webroot)) {
            throw new InvalidArgumentException();
        }

        $this->teamId = $teamId;
        $this->userId = $userId;
        $this->webroot = $webroot;
    }

    /**
     * Upload the file to a temp storage
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    abstract public function buffer(UploadedFile $file): string;

    /**
     * Move the file from temp storage to permanent one
     * Encapsulation of getBuffer() & save().
     *
     * @param string   $modelName
     * @param int      $modelId
     * @param string   $uuid
     * @param callable $preprocess Functions to be run on file before saving
     *
     * @return bool
     */
    public function move(string $modelName, int $modelId, string $uuid, callable $preprocess = null): bool
    {
        $file = $this->getBuffer($uuid);

        if (empty($file)) {
            throw new GlException\GoalousNotFoundException("Buffered file not found");
        }

        if (!empty($preprocess)) {
            $file = $preprocess($file);
        }

        if ($this->save($modelName, $modelId, $file)) {
            if (!$this->deleteBuffer($uuid)) {
                throw new RuntimeException("Couldn't delete buffer");
            }
            return true;
        }
        return false;
    }

    /**
     * Upload a file to S3
     *
     * @param string $bucket
     * @param string $key
     * @param string $body
     * @param string $type
     *
     * @return mixed
     */
    abstract protected function upload(string $bucket, string $key, string $body, string $type): bool;

    /**
     * Delete file from buffer
     *
     * @param string $uuid
     *
     * @return mixed
     */
    abstract public function deleteBuffer(string $uuid): bool;

    /**
     * Delete file from storage
     *
     * @param string $modelName
     * @param int    $modelId
     * @param string $uuid
     *
     * @return mixed
     */
    abstract public function delete(string $modelName, int $modelId, string $uuid): bool;

    /**
     * Get buffered file
     *
     * @param string $uuid
     *
     * @return UploadedFile
     */
    abstract public function getBuffer(string $uuid): UploadedFile;

    /**
     * Save file to permanent storage
     *
     * @param string       $modelName
     * @param int          $modelId
     * @param UploadedFile $file
     *
     * @return bool
     */
    abstract public function save(string $modelName, int $modelId, UploadedFile $file): bool;

    /**
     * Compress binary string
     *
     * @param string $fileData
     *
     * @return string
     */
    protected final function compress(string $fileData): string
    {
        return gzcompress($fileData, 3);
    }

    /**
     * Decompress binary string
     *
     * @param string $compressedData
     *
     * @return string
     */
    protected final function uncompress(string $compressedData): string
    {
        return gzuncompress($compressedData);
    }

    /**
     * Package file into JSON format
     *
     * @param UploadedFile $file
     *
     * @return string JSON encoded array
     */
    protected final function package(UploadedFile $file): string
    {
        if (empty ($file->getFileName()) || empty ($file->getBinaryFile())) {
            throw new InvalidArgumentException();
        }

        $array['file_name'] = $file->getFileName();
        $array['file_data'] = bin2hex($this->compress($file->getBinaryFile()));

        $json = json_encode($array);

        if (empty($json)) {
            throw new RuntimeException();
        }
        return $json;
    }

    /**
     * Unpackage JSON into UploadedFile
     *
     * @param string $jsonEncoded
     *
     * @return UploadedFile
     */
    protected final function unpackage(string $jsonEncoded): UploadedFile
    {
        if (empty($jsonEncoded)) {
            throw new InvalidArgumentException();
        }
        $array = json_decode($jsonEncoded);
        if (empty($array['file_data']) || empty ($array['file_name'])) {
            throw new RuntimeException();
        }
        return new UploadedFile(hex2bin($this->uncompress($array['file_data'])), $array['file_name'], true);
    }

    /**
     * Create MD5 Hash out of filename
     *
     * @param string $fileName
     *
     * @return string
     */
    protected final function createHash(string $fileName): string
    {
        return md5($fileName ?? "") . Configure::read('Security.salt');
    }

    /**
     * Create buffer key
     *
     * @param string $uuid
     *
     * @return string
     */
    protected function createKey(string $uuid): string
    {
        if (empty($uuid)) {
            throw new InvalidArgumentException();
        }
        return "/$this->teamId/$this->userId/" . $uuid . ".json";
    }
}