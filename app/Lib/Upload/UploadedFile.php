<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/24
 * Time: 15:09
 */

class UploadedFile
{

    /**
     * Binary data of the file
     */
    private $file;

    /**
     * Size of the file
     *
     * @var int
     */
    private $size;

    /**
     * Type of the file.
     * E.g. image
     *
     * @var string
     */
    private $type;

    /**
     * File extension of the file
     * E.g. jpeg
     *
     * @var string
     */
    private $fileExt;

    /**
     * Unique ID of this file
     *
     * @var string
     */
    private $uuid;

    /**
     * Metadata of the file
     *
     * @var string
     */
    private $metadata;

    public function __construct(string $encodedFile, bool $skipDecoding = false)
    {
        $this->decodeFile($encodedFile, $skipDecoding);
    }

    public function getFileSize(): int
    {
        return $this->size;
    }

    public function getFileType(): string
    {
        return $this->type;
    }

    public function getFileExt(): string
    {
        return $this->fileExt;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function isEmpty(): bool
    {
        return empty($this->file);
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function getMetadata(): string
    {
        return $this->metadata;
    }

    private function decodeFile(string $encodedFile, bool $skipDecoding)
    {
        if (empty($encodedFile)) {
            throw new InvalidArgumentException("File can't be empty");
        }

        if ($skipDecoding) {
            $rawFile = $encodedFile;
        } else {
            $rawFile = base64_decode($encodedFile, true);
            if (empty($rawFile)) {
                GoalousLog::error("Failed to decode string to file");
                throw new RuntimeException("Failed to decode string to file");
            }
        }
        $this->file = $rawFile;

        $fInfo = new finfo();
        $fileDesc = $fInfo->buffer($rawFile, FILEINFO_MIME_TYPE);
        list($type, $fileExt) = explode("/", $fileDesc, 2);
        if (empty($type) || empty($fileExt)) {
            GoalousLog::error("Failed to get file extension");
            throw new RuntimeException("Failed to get file extension");
        }
        $this->metadata = $fInfo->buffer($rawFile);
        $this->type = $type;
        $this->fileExt = $fileExt;

        $this->size = strlen($rawFile);

        $this->uuid = uniqid();
    }
}