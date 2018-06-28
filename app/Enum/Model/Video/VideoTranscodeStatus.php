<?php

namespace Goalous\Enum\Model\Video;

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
