<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static LIMITED()
 * @method static static FULL()
 */
class TranscodePattern extends Enum
{
    const LIMITED = 0;
    const FULL    = 1;
}
