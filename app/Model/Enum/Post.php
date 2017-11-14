<?php

namespace Goalous\Model\Enum\Post;

use MyCLabs\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static VIDEO_STREAM()
 */
class PostResourceType extends Enum
{
    const NONE  = 0;
    const VIDEO_STREAM = 1;
}
