<?php
namespace Goalous\Enum\Model\PaymentSetting;

use MyCLabs\Enum\Enum;

/**
 * @method static static JPY()
 * @method static static USD()
 */
class Currency extends Enum
{
    const JPY = 1;
    const USD = 2;
}
