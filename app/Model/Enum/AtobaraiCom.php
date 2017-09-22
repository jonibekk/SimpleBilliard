<?php
namespace Goalous\Model\Enum\AtobaraiCom;

use MyCLabs\Enum\Enum;

/**
 * @method static static IN_JUDGE()
 * @method static static OK()
 * @method static static NG()
 * @method static static CANCELED()
 * @method static static ORDER_NOT_FOUND()
 */
class Credit extends Enum
{
    const IN_JUDGE        = 0;
    const OK              = 1;
    const NG              = 2;
    const CANCELED        = 3;
    const ORDER_NOT_FOUND = -1;
}
