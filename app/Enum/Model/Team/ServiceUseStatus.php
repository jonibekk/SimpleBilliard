<?php
namespace Goalous\Enum\Model\Team;

use MyCLabs\Enum\Enum;

/**
 * @method static static FREE_TRIAL()
 * @method static static PAID()
 * @method static static READ_ONLY()
 * @method static static CANNOT_USE()
 * @method static static DELETED_MANUAL()
 * @method static static DELETED_AUTO()
 */
class ServiceUseStatus extends Enum
{
    const FREE_TRIAL = 0;
    const PAID = 1;
    const READ_ONLY = 2;
    const CANNOT_USE = 3;
    const DELETED_MANUAL = 4;
    const DELETED_AUTO = 5;
}
