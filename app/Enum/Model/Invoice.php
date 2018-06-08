<?php
namespace Goalous\Enum\Model\Invoice;

use MyCLabs\Enum\Enum;

/**
 * @method static static WAITING()
 * @method static static OK()
 * @method static static NG()
 */
class CreditStatus extends Enum
{
    const WAITING         = 0;
    const OK              = 1;
    const NG              = 2;
    const CANCELED        = 3;
    const ORDER_NOT_FOUND = -1;
}
