<?php
App::uses('AppHelper', 'View/Helper');

class LangHelper extends AppHelper
{
    const LANG_CODE_JP = 'ja';
    const LANG_CODE_EN = 'en';
    const LANG_CODE_PT = 'pt';

    /**
     * get lang code
     * - logged in user
     *   - get by db user setting data
     * - not logged in user
     *   - get by browser lang setting
     *
     * @return void
     */
    public function getLangCode() {
        if(in_array(Configure::read('Config.language'), array('jpn','ja'))) {
            return self::LANG_CODE_JP;
        } elseif (in_array(Configure::read('Config.language'), array('por','pt'))) {
            return self::LANG_CODE_PT;
        } else {
            return self::LANG_CODE_EN;
        }
    }

    /**
     * get user country by lang code
     *
     * @return void
     */
    public function getUserCountryCode()
    {
        $langCode = $this->getLangCode();
        if ($langCode === self::LANG_CODE_JP) {
            return 'JP';
        } else {
            return 'US';
        }
    }

    /**
     * get country by country code
     *
     * @param string $countryCode
     * @return array|null
     */
    public function getCountryByCode(string $countryCode)
    {
        $countries = Configure::read("countries");
        $combined = Hash::combine($countries, "{n}[code=${countryCode}].code", "{n}[code=${countryCode}]");
        if (!$combined || empty($combined[$countryCode])) {
            return null;
        }
        return $combined[$countryCode];
    }

}
