<?php

class MobileAppVersion
{
    /**
     * return true if Goalous Mobile App version is supporting
     * @param string $versionAtLeast Goalous guaranteed mobile app version (最低動作保証バージョン)
     * @param string $versionCompare comparing version, usually, mobile app version
     * @return bool
     */
    static function isSupporting(string $versionAtLeast, string $versionCompare): bool
    {
        return version_compare($versionAtLeast, $versionCompare, '<=');
    }

    /**
     * return true if Goalous Mobile App version is out of support
     * @param string $versionAtLeast
     * @param string $versionCompare
     * @return bool
     */
    static function isExpired(string $versionAtLeast, string $versionCompare): bool
    {
        return !static::isSupporting($versionAtLeast, $versionCompare);
    }
}
