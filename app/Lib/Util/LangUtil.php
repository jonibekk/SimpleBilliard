<?php

use Goalous\Enum as Enum;

/**
 * Class LangUtil
 */
class LangUtil
{
    static $defaultLang     = 'en';
    static $defaultLangISO3 = 'eng';
    static $ISOMap          = [
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
    public static function convertISOFrom3to2(string $code): string
    {
        return !empty(self::$ISOMap[$code]) ? self::$ISOMap[$code] : self::$defaultLang;
    }

    /**
     * Convert Language code from ISO 639-1 (2 characters (en, ja)) to ISO 639-2 (3 characters (eng, jpn))
     *
     * @param string $code
     *
     * @return string
     */
    public static function convertISOFrom2To3(string $code): string
    {
        foreach (self::$ISOMap as $key => $value) {
            if ($value === $code) {
                return $key;
            }
        }
        return self::$defaultLangISO3;
    }

    /**
     * Ensure that language code will alwaye be in ISO 639-2
     *
     * @param string $code
     *
     * @return string
     */
    public static function convertToISO3(string $code): string
    {
        if (strlen($code) === 3) {
            return $code;
        } else {
            return self::convertISOFrom2To3($code);
        }
    }
}
