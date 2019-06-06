<?php

namespace Goalous\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static static JA()
 * @method static static EN()
 * @method static static ZH_CN()
 * @method static static ZH_TW()
 * @method static static TH()
 * @method static static ES()
 * @method static static DE()
 * @method static static IT()
 * @method static static FR()
 * @method static static ID()
 * @method static static MS()
 *
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
    // Chinese (simplified)
    const ZH_CN = 'zh-CN';
    // Chinese (traditional)
    const ZH_TW = 'zh-TW';
    // Thai
    const TH = 'th';
    // Spanish
    const ES = 'es';
    // German
    const DE = 'de';
    // Italian
    const IT = 'it';
    // French
    const FR = 'fr';
    // Indonesian
    const ID = 'id';
    // Malaysian
    const MS = 'ms';
}
