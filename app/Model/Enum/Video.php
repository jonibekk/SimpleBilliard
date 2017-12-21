<?php

namespace Goalous\Model\Enum\Video;

use MyCLabs\Enum\Enum;

/**
 * @method static static ERROR()
 * @method static static NONE()
 * @method static static UPLOADING()
 * @method static static UPLOAD_COMPLETE()
 * @method static static QUEUED()
 * @method static static TRANSCODING()
 * @method static static TRANSCODE_COMPLETE()
 */
class VideoTranscodeStatus extends Enum
{
    const ERROR                 = -1;
    const NONE                  =  0;
    const UPLOADING             =  1;
    const UPLOAD_COMPLETE       =  2;
    const QUEUED                =  3;
    const TRANSCODING           =  4;
    const TRANSCODE_COMPLETE    =  5;
}

/**
 * @method static static PROGRESS()
 * @method static static ERROR()
 * @method static static WARNING()
 * @method static static COMPLETE()
 */
class VideoTranscodeProgress extends Enum
{
    const PROGRESS = 'progress';
    const ERROR    = 'error';
    const WARNING  = 'warning';
    const COMPLETE = 'complete';
}

/**
 * @method static static AWS_ETS()
 */
class Transcoder extends Enum
{
    const AWS_ETS = 'aws_ets';
}

/**
 * @see https://confluence.goalous.com/display/GOAL/Video+Transcode+Output+Versions
 *
 * @method static static V1()
 */
class TranscodeOutputVersion extends Enum
{
    const V1 = '1';
}

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

