<?php

namespace Goalous\Enum\Network\Response;

use MyCLabs\Enum\Enum;

/**
 * @method static static GLOBAL()
 * @method static static VALIDATION()
 */
class ErrorType extends Enum
{
    const GLOBAL     = 'global';
    const VALIDATION = 'validation';
}
