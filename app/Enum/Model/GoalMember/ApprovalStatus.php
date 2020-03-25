<?php


namespace Goalous\Enum\Model\GoalMember;

use MyCLabs\Enum\Enum;

/**
 * @method static static NEW()
 * @method static static REAPPLICATION()
 * @method static static DONE()
 * @method static static WITHDRAWN()
 */
class ApprovalStatus extends Enum
{
    const NEW = 0;
    const REAPPLICATION = 1;
    const DONE = 2;
    const WITHDRAWN = 3;
}
