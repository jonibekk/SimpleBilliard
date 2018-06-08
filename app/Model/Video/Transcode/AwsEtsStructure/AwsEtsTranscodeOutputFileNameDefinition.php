<?php

use Goalous\Enum as Enum;

class AwsEtsTranscodeOutputFileNameDefinition
{
    /**
     * @param Enum\Model\Video\VideoSourceType $videoSourceType
     * @param                            $key
     *
     * @return string
     */
    public static function getSourceBaseName(Enum\Model\Video\VideoSourceType $videoSourceType, $key): string
    {
        switch ($videoSourceType->getValue()) {
            case Enum\Model\Video\VideoSourceType::PLAYLIST_M3U8_HLS:
                // $key expected like 'playlist'
                return sprintf('%s.m3u8', $key);
            case Enum\Model\Video\VideoSourceType::VIDEO_WEBM:
                // $key expected like 'webm_500k/video.webm'
                return $key;
            case Enum\Model\Video\VideoSourceType::NOT_RECOMMENDED:
            default:
                return $key;
        }
    }
}
