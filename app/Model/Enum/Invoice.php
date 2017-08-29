<?php
namespace Goalous\Model\Enum\Invoice;

use MyCLabs\Enum\Enum;

/**
 * @method static Action WAITING()
 * @method static Action OK()
 * @method static Action NG()
 */
class CreditStatus extends Enum
{
    const WAITING = 0;
    const OK = 1;
    const NG = 2;
}
