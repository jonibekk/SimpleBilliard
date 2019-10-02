<?php
namespace Goalous\Enum\Model\Post;

use MyCLabs\Enum\Enum;

/**
 * @method static static NORMAL()
 * @method static static CREATE_GOAL()
 * @method static static ACTION()
 * @method static static BADGE()
 * @method static static KR_COMPLETE()
 * @method static static GOAL_COMPLETE()
 * @method static static CREATE_CIRCLE()
 * @method static static MESSAGE()
 */
class Type extends Enum
{
    const NORMAL = 1;
    const CREATE_GOAL = 2;
    const ACTION = 3;
    const BADGE = 4; // unused now
    const KR_COMPLETE = 5;
    const GOAL_COMPLETE = 6;
    const CREATE_CIRCLE = 7;
    const MESSAGE = 8; // unused now?
}
