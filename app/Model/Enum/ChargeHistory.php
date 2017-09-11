<?php
namespace Goalous\Model\Enum\ChargeHistory;

use MyCLabs\Enum\Enum;

/**
 * @method static Action MONTHLY_FEE()
 * @method static Action USER_INCREMENT_FEE()
 * @method static Action USER_ACTIVATION_FEE()
 */
class ChargeType extends Enum
{
    const MONTHLY_FEE = 0;
    const USER_INCREMENT_FEE = 1;
    const USER_ACTIVATION_FEE = 2;
}

/**
 * @method static Action ERROR()
 * @method static Action SUCCESS()
 * @method static Action FAIL()
 */
class ResultType extends Enum
{
    const ERROR = 0;
    const SUCCESS = 1;
    const FAIL = 2;
}
