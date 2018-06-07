<?php

use Goalous\Enum as Enum;

/**
 * <source>タグ内に利用する値の出力
 * @see https://developer.mozilla.org/ja/docs/Web/HTML/Element/source
 *
 * Class VideoSource
 */
class VideoSource
{
    /**
     * @var Enum\Video\VideoSourceType
     */
    private $sourceType;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * VideoSource constructor.
     *
     * @param Enum\Video\VideoSourceType $type
     * @param string                     $filePath
     */
    public function __construct(Enum\Video\VideoSourceType $type, string $filePath)
    {
        $this->sourceType = $type;
        $this->filePath = $filePath;
    }

    /**
     * @param null|string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return Enum\Video\VideoSourceType
     */
    public function getType(): Enum\Video\VideoSourceType
    {
        return $this->sourceType;
    }

    /**
     * return relative path to video source
     * e.g. s3 file key
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * return full path url string to video source
     * @return string
     */
    public function getSource(): string
    {
        if (is_string($this->baseUrl)) {
            return $this->baseUrl . $this->filePath;
        }
        return $this->filePath;
    }
}
