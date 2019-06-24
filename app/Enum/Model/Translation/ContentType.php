<?php

namespace Goalous\Enum\Model\Translation;

use MyCLabs\Enum\Enum;

/**
 * Class ContentType
 *
 * @package Goalous\Enum\Model\Translation
 *
 * @method static static CIRCLE_POST()
 * @method static static CIRCLE_POST_COMMENT()
 * @method static static ACTION_POST()
 * @method static static ACTION_POST_COMMENT()
 */
class ContentType extends Enum
{
    const CIRCLE_POST = 1;
    const CIRCLE_POST_COMMENT = 2;
    const ACTION_POST = 3;
    const ACTION_POST_COMMENT = 4;
}