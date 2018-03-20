<?php
namespace Goalous\Model\Enum\Evaluation;

use MyCLabs\Enum\Enum;

/**
 * @method static static NOT_ENTERED()
 * @method static static DRAFT()
 * @method static static DONE()
 */
class Status extends Enum
{
    const NOT_ENTERED = 0;
    const DRAFT = 1;
    const DONE = 2;
}
