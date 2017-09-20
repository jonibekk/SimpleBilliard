<?php
namespace Goalous\Model\Enum\Invoice;

use MyCLabs\Enum\Enum;

/**
 * @method static static WAITING()
 * @method static static OK()
 * @method static static NG()
 */
class CreditStatus extends Enum
{
    const WAITING = 0;
    const OK = 1;
    const NG = 2;
}
