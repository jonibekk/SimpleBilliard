<?php
namespace Goalous\Enum\Model\ChargeHistory;

use MyCLabs\Enum\Enum;

/**
 * @method static static ERROR()
 * @method static static SUCCESS()
 * @method static static FAIL()
 */
class ResultType extends Enum
{
    const ERROR = 0;
    const SUCCESS = 1;
    const FAIL = 2;
    const NOCHARGE = 3;
}
