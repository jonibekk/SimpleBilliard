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
     *
     * @var string
     */
    private $binaryFile;

    /**
     * Base64 encoded file
     *
     * @var string
     */
    private $encodedFile;

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
     * File name
     *
     * @var
     */
    private $fileName;

    /**
     * Metadata of the file
     *
     * @var string
     */
    private $metadata;

    public function __construct(string $encodedFile, string $fileName, bool $skipDecoding = false)
    {
        if (empty($encodedFile) || empty($fileName)) {
            throw new InvalidArgumentException();
        }
        $this->decodeFile($encodedFile, $skipDecoding);
        if (!$skipDecoding) {
            $this->encodedFile = $encodedFile;
        }
        $this->fileName = $fileName;
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

    public function getBinaryFile(): string
    {
        return $this->binaryFile;
    }

    public function getEncodedFile(): string
    {
        if (empty($this->encodedFile)) {
            return base64_encode($this->binaryFile);
        }
        return $this->encodedFile;
    }

    public function isEmpty(): bool
    {
        return empty($this->binaryFile);
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function getMetadata(): string
    {
        return $this->metadata;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getMIME(): string
    {
        return $this->type . "/" . $this->fileExt;
    }

    public function withUUID(string $uuid):self {
        $this->uuid = $uuid;
        return $this;
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
        $this->binaryFile = $rawFile;

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

        $this->uuid = uniqid("", true);
    }
}