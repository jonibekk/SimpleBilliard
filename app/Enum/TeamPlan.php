<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static REGULAR()
 * @method static static PAID()
 */
class TeamPlan extends Enum
{
    const REGULAR   = 0;
    const PAID      = 1;
}
