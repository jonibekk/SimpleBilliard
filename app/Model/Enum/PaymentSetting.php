<?php
namespace Goalous\Model\Enum\PaymentSetting;

use MyCLabs\Enum\Enum;

/**
 * @method static Action INVOICE()
 * @method static Action CREDIT_CARD()
 */
class Type extends Enum
{
    const INVOICE = 0;
    const CREDIT_CARD = 1;
}

/**
 * @method static Action JPY()
 * @method static Action USD()
 */
class Currency extends Enum
{
    const JPY = 1;
    const USD = 2;
}
