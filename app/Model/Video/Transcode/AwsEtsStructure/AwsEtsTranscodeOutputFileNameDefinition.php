<?php

use Goalous\Model\Enum as Enum;

class AwsEtsTranscodeOutputFileNameDefinition
{
    /**
     * @param Enum\Video\VideoSourceType $videoSourceType
     * @param                            $key
     *
     * @return string
     */
    public static function getSourceBaseName(Enum\Video\VideoSourceType $videoSourceType, $key): string
    {
        switch ($videoSourceType->getValue()) {
            case Enum\Video\VideoSourceType::PLAYLIST_M3U8_HLS:
                // $key expected like 'playlist'
                return sprintf('%s.m3u8', $key);
            case Enum\Video\VideoSourceType::VIDEO_WEBM:
                // $key expected like 'webm_500k/video.webm'
                return $key;
            case Enum\Video\VideoSourceType::NOT_RECOMMENDED:
            default:
                return $key;
        }
    }
}
