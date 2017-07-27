<?php

class MobileAppVersion
{
    /**
     * @param string $versionAtLeast Goalous guaranteed mobile app version (最低動作保証バージョン)
     * @param string $versionCompare comparing version, usually, mobile app version
     * @return bool
     */
    static function isGuaranteed(string $versionAtLeast, string $versionCompare): bool
    {
        return version_compare($versionAtLeast, $versionCompare, '<=');
    }
}
