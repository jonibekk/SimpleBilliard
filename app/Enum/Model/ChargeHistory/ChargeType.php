<?php
namespace Goalous\Enum\Model\ChargeHistory;

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
