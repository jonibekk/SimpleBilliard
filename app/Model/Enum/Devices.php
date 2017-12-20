<?php
namespace Goalous\Model\Enum\Devices;

use MyCLabs\Enum\Enum;

/**
 * @method static static IOS()
 * @method static static ANDROID()
 */
class DeviceType extends Enum
{
    const IOS = 0;
    const ANDROID = 1;
}