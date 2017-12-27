<?php

use Goalous\Model\Enum as Enum;

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
    private $fullPathUrl;

    public function __construct(Enum\Video\VideoSourceType $type, string $fullPathUrl)
    {
        $this->sourceType = $type;
        $this->fullPathUrl = $fullPathUrl;
    }

    public function getType(): Enum\Video\VideoSourceType
    {
        return $this->sourceType;
    }

    public function getSource(): string
    {
        return $this->fullPathUrl;
    }
}
