<?php


namespace Goalous\Enum\Model\GoalMember;

use MyCLabs\Enum\Enum;

/**
 * @method static static COLLABORATOR()
 * @method static static OWNER()
 */
class Type extends Enum
{
    const COLLABORATOR = 0;
    const OWNER = 1;
}
