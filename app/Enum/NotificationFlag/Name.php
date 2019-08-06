<?php


namespace Goalous\Enum\NotificationFlag;


use MyCLabs\Enum\Enum;

/**
 * Class Name
 *
 * @package Goalous\Enum\NotificationFlag
 *
 * @method static static TYPE_TRANSLATION_LIMIT_REACHED()
 * @method static static TYPE_TRANSLATION_LIMIT_CLOSING()
 */
class Name extends Enum
{
    const TYPE_TRANSLATION_LIMIT_REACHED = 'translation_limit_reached';
    const TYPE_TRANSLATION_LIMIT_CLOSING = 'translation_limit_closing';
}