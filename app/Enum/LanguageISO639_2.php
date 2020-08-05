<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static JPN()
 * @method static static ENG()
 *
 * @deprecated use |Goalous\Enum\Language
 *
 * Language code based on ISO 639-2 (3 letters)
 * @see https://en.wikipedia.org/wiki/List_of_ISO_639-2_codes
 */
class LanguageISO639_2 extends Enum
{
    const JPN = 'jpn';
    const ENG = 'eng';
    const POR = 'por';
}
