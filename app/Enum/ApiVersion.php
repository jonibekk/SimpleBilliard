<?php

namespace Goalous\Enum\ApiVersion;

use MyCLabs\Enum\Enum;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/07
 * Time: 10:23
 */

/**
 * Class ApiVersion
 *
 * @package Goalous\Model\Enum\ApiVersion
 */
class ApiVersion extends Enum
{
    const API_VERSION_2 = 2;

    /** @var array Available API versions */
    const AVAILABLE_API_VERSIONS = [self::API_VERSION_2];

    /**
     * Get the latest API version
     *
     * @return int
     */
    public static function getLatestApiVersion()
    {
        return max(self::AVAILABLE_API_VERSIONS);
    }
}