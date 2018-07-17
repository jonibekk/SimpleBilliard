<?php
namespace Goalous\Enum\Model\PaymentSetting;

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

