<?php
namespace Goalous\Model\Enum\CampaignTeam;

use MyCLabs\Enum\Enum;

/**
 * @method static static FIXED_MONTHLY_CHARGE()
 * @method static static DISCOUNT_AMOUNT_PER_USER()
 */
class CampaignType extends Enum
{
    const FIXED_MONTHLY_CHARGE = 0;
    const DISCOUNT_AMOUNT_PER_USER = 1;
}
