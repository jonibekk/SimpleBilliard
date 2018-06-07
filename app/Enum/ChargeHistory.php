<?php
namespace Goalous\Enum\ChargeHistory;

use MyCLabs\Enum\Enum;

/**
 * @method static static MONTHLY_FEE()
 * @method static static USER_INCREMENT_FEE()
 * @method static static USER_ACTIVATION_FEE()
 * @method static static UPGRADE_PLAN_DIFF()
 * @method static static RECHARGE()
 */
class ChargeType extends Enum
{
    const MONTHLY_FEE = 0;
    const USER_INCREMENT_FEE = 1;
    const USER_ACTIVATION_FEE = 2;
    const UPGRADE_PLAN_DIFF = 3;
    const RECHARGE = 4;
}

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
}
