<?php
namespace Goalous\Enum\Model\Term;

use MyCLabs\Enum\Enum;

/**
 * @method static static NOT_STARTED()
 * @method static static IN_PROGRESS()
 * @method static static FROZEN()
 * @method static static FINISHED()
 */
class EvaluateStatus extends Enum
{
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const FROZEN = 2;
    const FINISHED = 3;

}
