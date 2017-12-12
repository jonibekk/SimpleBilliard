<?php

namespace Goalous\Model\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static REGULAR()
 * @method static static PAID()
 * @method static static PAID_PLUS()
 */
class TeamPlan extends Enum
{
    const REGULAR   = 0;
    const PAID      = 1;
    const PAID_PLUS = 2;
}
