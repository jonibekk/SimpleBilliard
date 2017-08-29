<?php
namespace Goalous\Model\Enum\Team;

use MyCLabs\Enum\Enum;

/**
 * @method static Action FREE_TRIAL()
 * @method static Action PAID()
 * @method static Action READ_ONLY()
 * @method static Action CANNOT_USE()
 */
class ServiceUseStatus extends Enum
{
    const FREE_TRIAL = 0;
    const PAID = 1;
    const READ_ONLY = 2;
    const CANNOT_USE = 3;
    // TODO: add status withdrawal
}
