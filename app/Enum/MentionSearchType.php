<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static REGULAR()
 * @method static static PAID()
 */
class MentionSearchType extends Enum
{
    const COMMENT   = 1;
    const POST      = 2;
}
