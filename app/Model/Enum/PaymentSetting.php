<?php
namespace Goalous\Model\Enum;

use MyCLabs\Enum\Enum;

/**
 * PaymentSetting model constants
 */
class PaymentSetting extends Enum
{
    /* type */
    const TYPE_INVOICE = 0;
    const TYPE_CREDIT_CARD = 1;

    /* currency */
    const CURRENCY_JPY = 1;
    const CURRENCY_USD = 2;

    /* charge_type */
    const CHARGE_TYPE_MONTHLY_FEE = 0;
    const CHARGE_TYPE_USER_INCREMENT_FEE = 1;
    const CHARGE_TYPE_USER_ACTIVATION_FEE = 2;
}
