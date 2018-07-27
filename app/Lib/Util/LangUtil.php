<?php

/**
 * Class LangUtil
 */
class LangUtil
{
    static $defaultLang = 'en';
    static $ISOMap = [
        'eng' => 'en',
        'jpn' => 'ja',
    ];

    /**
     * Convert Language code from ISO 639-2 (3 characters (eng, jpn)) to ISO 639-1 (2 characters (en, ja))
     *
     * @param string $code
     *
     * @return string
     */
    static function convertISOFrom3to2(string $code) : string
    {
        return !empty(self::$ISOMap[$code]) ? self::$ISOMap[$code] : self::$defaultLang;
    }
}
