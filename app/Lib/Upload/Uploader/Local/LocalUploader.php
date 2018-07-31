<?php
App::import('Lib/Upload/Uploader', 'BaseUploader');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/30
 * Time: 18:16
 */

use Goalous\Exception as GlException;

class LocalUploader extends BaseUploader
{
    public function buffer(UploadedFile $file): string
    {
        $key = $this->createKey($file->getUUID());
        try {
            $this->upload("buffer", $key, $this->package($file), "application/json");
        } catch (RuntimeException $exception) {
            GoalousLog::error("Failed saving to buffer. Team: $this->teamId, User: $this->userId, File:" . $file->getFileName(),
                $exception->getTrace());
            throw new RuntimeException("Failed saving to local buffer");
        }
        return $file->getUUID();
    }

    /**
     * Upload file to /webroot folder
     *
     * @param string $bucket Storage type
     * @param string $key    Path to the file. Including file name & ext
     * @param string $body
     * @param string $type
     *
     * @return bool
     */
    protected function upload(string $bucket, string $key, string $body, string $type): bool
    {
        $fullPath = $this->webroot . "/" . $bucket . $key;

        $destDir = dirname($fullPath);
        $parentDir = dirname($destDir);

        if (!file_exists($parentDir)) {
            @mkdir($parentDir, 0775, true);
            @chmod($parentDir, 0775);
        }
        if (!file_exists($destDir)) {
            @mkdir($destDir, 0775, true);
            @chmod($destDir, 0775);
        }
        if (is_dir($destDir) && is_writable($destDir)) {
            return (file_put_contents($fullPath, $body) !== false);
        }
        return false;
    }

    /**
     * Delete buffered file.
     * Only used in local
     *
     * @param string $uuid
     *
     * @return bool
     */
    public function deleteBuffer(string $uuid): bool
    {
        $fullPath = $this->webroot . "\/buffer" . $this->createKey($uuid);

        if (file_exists($fullPath)) {
            return @unlink($fullPath);
        } else {
            throw new GlException\GoalousNotFoundException();
        }
    }

    public function delete(string $modelName, int $modelId, string $uuid): bool
    {
        // TODO GL-7171
    }

    public function getBuffer(string $uuid): UploadedFile
    {
        // TODO GL-7171
    }

    public function save(string $modelName, int $modelId, UploadedFile $file): bool
    {
        // TODO GL-7171
    }
}