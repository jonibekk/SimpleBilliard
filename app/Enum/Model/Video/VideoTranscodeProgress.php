<?php

namespace Goalous\Enum\Model\Video;

use MyCLabs\Enum\Enum;


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
