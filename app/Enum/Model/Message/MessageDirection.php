<?php

namespace Goalous\Enum\Model\Message;

use MyCLabs\Enum\Enum;

/**
 * Class MessageDirection
 * @method static static OLD()
 * @method static static NEW()
 *
 * @package Goalous\Enum\Model\Message
 */
class MessageDirection extends Enum
{
    const OLD = 'old';
    const NEW = 'new';
}