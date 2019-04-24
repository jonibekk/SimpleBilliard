<?php
namespace Goalous\Enum\Model\KeyResult;

use MyCLabs\Enum\Enum;

/**
 * @method static static UNIT_PERCENT()
 * @method static static UNIT_NUMBER()
 * @method static static UNIT_BINARY()
 * @method static static UNIT_YEN()
 * @method static static UNIT_DOLLAR()
 */
class ValueUnit extends Enum
{
    const UNIT_PERCENT = 0;
    const UNIT_NUMBER = 1;
    const UNIT_BINARY = 2;
    const UNIT_YEN = 3;
    const UNIT_DOLLAR = 4;
}
