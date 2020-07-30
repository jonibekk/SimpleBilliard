<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static JA()
 * @method static static EN()
 * Language code based on ISO 639-1 (2 letters)
 *
 * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 */
class Language extends Enum
{
    // Japanese
    const JA = 'ja';
    // English
    const EN = 'en';
    // Português
    const PT = 'pt';
}
