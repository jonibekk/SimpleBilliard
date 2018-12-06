<?php

namespace Goalous\Enum\Model\Video;

use MyCLabs\Enum\Enum;

/**
 * Class VideoSourceType
 *
 * @see Media Types https://www.rfc-editor.org/rfc/rfc4281.txt
 *       Media formats for HTML audio and video https://developer.mozilla.org/en-US/docs/Web/HTML/Supported_media_formats
 *
 * @method static static NOT_RECOMMENDED()
 * @method static static VIDEO_WEBM()
 * @method static static PLAYLIST_M3U8_HLS()
 */
class VideoSourceType extends Enum
{
    /**
     * this NOT_RECOMMENDED definition is for video source
     * that should/will not use for video.source tag
     */
    const NOT_RECOMMENDED = 'NOT_RECOMMENDED';

    const VIDEO_WEBM = 'video/webm';
    const PLAYLIST_M3U8_HLS  = 'application/x-mpegURL';
}
