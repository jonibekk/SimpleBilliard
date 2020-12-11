<?php

use Goalous\Enum as Enum;

/**
 * Class LangUtil
 */
class LangUtil
{
    static $defaultLang = 'en';
    static $ISOMap = [
        Enum\LanguageISO639_2::ENG => Enum\Language::EN,
        Enum\LanguageISO639_2::JPN => Enum\Language::JA,
        Enum\LanguageISO639_2::POR => Enum\Language::PT,
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
