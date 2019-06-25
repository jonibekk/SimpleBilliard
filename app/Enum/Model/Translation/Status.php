<?php


namespace Goalous\Enum\Model\Translation;

use MyCLabs\Enum\Enum;

/**
 * Class Status
 *
 * @package Goalous\Enum\Model\Translation
 *
 * @method static static PROCESSING()
 * @method static static DONE()
 */
class Status extends Enum
{
    const PROCESSING = 0;
    const DONE = 1;
}