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
     * Regexp for the base64 with header
     *
     * @see here for base64 format
     * https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URIs
     */
    const BASE64_REGEXP = "/^(data:?([^;]+\/[^,;]+)?(;base64)?,)?(.+)$/";

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
     * Suffix of file name
     *
     * @var string
     */
    private $suffix = '';

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * Metadata of the file
     *
     * @var string
     */
    private $metadata;

    /**
     * MIME data of file
     *
     * @var string
     */
    private $mimeData;

    /**
     * MIME encoding of file
     *
     * @var string
     */
    private $mimeEncoding = '';

    public function __construct(string $encodedFile, string $fileName, bool $skipDecoding = false)
    {
        if (empty($encodedFile) || empty($fileName)) {
            throw new InvalidArgumentException("File name & file data must exist");
        }

        $this->decodeFile($encodedFile, $skipDecoding, $fileName);

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

    /**
     * @param bool $withBase64Header Always returning result with base64 header
     *
     * @return string
     */
    public function getEncodedFile(bool $withBase64Header = false): string
    {
        $encodedFile = empty($this->encodedFile) ? base64_encode($this->binaryFile) : $this->encodedFile;
        if (!$withBase64Header) {
            return $encodedFile;
        }
        $hasBase64Header = (0 === strpos($this->encodedFile, 'data:'));
        return $hasBase64Header ? $encodedFile : sprintf('data://%s%s;base64,%s', $this->getMIME(), $this->getCharSetForData(), $encodedFile);
    }

    public function isEmpty(): bool
    {
        return empty($this->binaryFile);
    }

    public function getUUID(): string
    {
        if (empty($this->uuid)) {
            $this->uuid = uniqid("", true);
        }
        return $this->uuid;
    }

    public function getMetadata(): string
    {
        return $this->metadata;
    }

    private function getCharSetForData(): string
    {
        if (strlen($this->mimeEncoding) < 1) {
            return '';
        }
        return ';charset=' . $this->mimeEncoding;
    }

    public function getFileName(bool $omitExtension = false): string
    {
        //Remove file extension
        if ($omitExtension) {
            $originalLocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'C');
            $fileName = pathinfo($this->fileName, PATHINFO_FILENAME);
            setlocale(LC_CTYPE, $originalLocale);
            return $fileName;
        }
        return $this->fileName;
    }

    public function getMIME(): string
    {
        return $this->mimeData;
    }

    public function getMimeWithCharset(): string
    {
        return $this->mimeData . $this->getCharSetForData();
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
     * @param string $fileName     File name of uploaded file
     *
     * @throws Exception
     */
    private function decodeFile(string $encodedFile, bool $skipDecoding = false, string $fileName = "")
    {
        if (empty($encodedFile)) {
            throw new InvalidArgumentException("File can't be empty");
        }

        //If input file stream is already binary, skip decoding
        if ($skipDecoding) {
            $rawFile = $encodedFile;
            //Remove base64 encoded string
            if (!empty($this->encodedFile)) {
                unset($this->encodedFile);
            }
        } else {
            $match = [];
            preg_match(self::BASE64_REGEXP, $encodedFile, $match);
            if (empty($match)) {
                GoalousLog::error("Failed matching base64 regex");
                throw new RuntimeException("Failed to decode string to file");
            }
            unset($encodedFile);
            $rawFile = base64_decode($match[4], true);
            unset($match);
            if (empty($rawFile)) {
                GoalousLog::error("Failed to decode string to file");
                throw new RuntimeException("Failed to decode string to file");
            }
        }
        $this->binaryFile = $rawFile;

        $fInfo = new finfo();
        $fileDesc = $fInfo->buffer($rawFile, FILEINFO_MIME_TYPE);
        $this->mimeEncoding = $fInfo->buffer($rawFile, FILEINFO_MIME_ENCODING);
        list($type, $fileExt) = explode("/", $fileDesc, 2);
        if (empty($type) || empty($fileExt)) {
            GoalousLog::error("Failed to get file extension");
            throw new RuntimeException("Failed to get file extension");
        }
        $this->mimeData = $fileDesc;
        $this->metadata = $fInfo->buffer($rawFile);
        $this->type = $type;

        if (!empty($fileName)) {
            $parsedFileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            if (!empty($parsedFileExt)) {
                $fileExt = $parsedFileExt;
            }
        }
        $this->fileExt = $fileExt;
        $this->size = strlen($rawFile);
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

        if (empty($array['file_name']) || empty($array['file_data'])) {
            throw new RuntimeException("Missing file information");
        }

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
