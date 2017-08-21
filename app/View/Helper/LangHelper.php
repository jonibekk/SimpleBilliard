<?php
App::uses('AppHelper', 'View/Helper');

class LangHelper extends AppHelper
{
    const LANG_CODE_JP = 'jp';
    const LANG_CODE_EN = 'en';

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
        } else {
            return self::LANG_CODE_EN;
        }
    }

    public function getUserCountryCode()
    {
        $langCode = $this->getLangCode();
        if ($langCode === self::LANG_CODE_JP) {
            return 'JP';
        } else {
            return 'US';
        }
    }
}