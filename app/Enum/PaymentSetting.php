<?php
namespace Goalous\Enum\PaymentSetting;

use MyCLabs\Enum\Enum;

/**
 * @method static static INVOICE()
 * @method static static CREDIT_CARD()
 */
class Type extends Enum
{
    const INVOICE = 0;
    const CREDIT_CARD = 1;
}

/**
 * @method static static JPY()
 * @method static static USD()
 */
class Currency extends Enum
{
    const JPY = 1;
    const USD = 2;
}
