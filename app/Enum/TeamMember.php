<?php
namespace Goalous\Enum\TeamMember;

use MyCLabs\Enum\Enum;

/**
 * @method static static INVITED()
 * @method static static ACTIVE()
 * @method static static INACTIVE()
 */
class Status extends Enum
{
    const INVITED = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
}
