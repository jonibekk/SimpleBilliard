<?php
namespace Goalous\Model\Enum\TeamMember;

use MyCLabs\Enum\Enum;

/**
 * @method static Action INVITED()
 * @method static Action ACTIVE()
 * @method static Action INACTIVE()
 */
class Status extends Enum
{
    const INVITED = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
}
