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
     * Regexp for the UUID
     */
    const UUID_REGEXP = "/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/";

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
     * @var string
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
            throw new InvalidArgumentException("File name & file data must exist");
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

    public function getFileName(bool $omitExtension = false): string
    {
        //Remove file extension
        if ($omitExtension) {
            $pointIndex = strrpos($this->fileName, '.');
            return substr($this->fileName, 0, $pointIndex);
        }
        return $this->fileName;
    }

    public function getMIME(): string
    {
        return $this->type . "/" . $this->fileExt;
    }

    public function withUUID(string $uuid): self
    {
        if (preg_match(self::UUID_REGEXP, $uuid) == 0) {
            throw new InvalidArgumentException("Invalid UUID format");
        }
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Decode a base64 encoded file into binary file
     *
     * @param string $encodedFile
     * @param bool   $skipDecoding If the input file is already in binary form,
     *
     * @throws Exception
     */
    private function decodeFile(string $encodedFile, bool $skipDecoding = false)
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
        unset($encodedFile);
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

    /**
     * Package file into JSON formatted string
     *
     * @return string JSON encoded array
     */
    public function toJSON(): string
    {
        $array['file_name'] = $this->getFileName();
        $array['file_data'] = $this->getEncodedFile();

        $json = json_encode($array);

        if (empty($json)) {
            throw new RuntimeException("Failed to encode file to JSON");
        }
        return $json;
    }

    /**
     * Create UploadedFile from JSON formatted string
     *
     * @param string $jsonEncoded
     *
     * @return UploadedFile
     * @throws Exception
     */
    public static function generate(string $jsonEncoded): self
    {
        if (empty($jsonEncoded)) {
            throw new InvalidArgumentException("JSON string cannot be empty");
        }

        $array = json_decode($jsonEncoded, true);
        unset($jsonEncoded);

        if (empty($array['file_data']) || empty ($array['file_name'])) {
            throw new RuntimeException("Failed to decode JSON to file");
        }
        return new UploadedFile($array['file_data'], $array['file_name']);
    }
}