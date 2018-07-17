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
 * @method static static VER_2()
 *
 * @package Goalous\Enum\ApiVersion
 */
class ApiVersion extends Enum
{
    const VER_2 = 2;

    /** @var array Available API versions */
    const AVAILABLE_API_VERSIONS = [ApiVersion::VER_2];

    /**
     * Get the latest API version
     *
     * @return int
     */
    public static function getLatestApiVersion()
    {
        return max(ApiVersion::AVAILABLE_API_VERSIONS);
    }

    /**
     * Check whether the version is available
     *
     * @param int $apiVersion
     *
     * @return bool
     */
    public static function isAvailable(int $apiVersion): bool
    {
        return in_array($apiVersion, ApiVersion::AVAILABLE_API_VERSIONS);
    }
}