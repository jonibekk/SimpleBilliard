<?php
App::uses('LangUtil', 'Util');
App::import('Model/Entity', 'BaseEntity');

use Goalous\Enum as Enum;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/04
 * Time: 15:52
 */
class UserEntity extends BaseEntity
{
    protected function initializeDefaults()
    {
        parent::initializeDefaults();
    }

    /**
     * Return user language configuration
     * https://en.wikipedia.org/wiki/ISO_639-1
     *
     * @return Enum\Language
     */
    public function getLanguageByIso639_1(): Enum\Language
    {
        if (Enum\LanguageISO639_2::isValid($this['language'])) {
            $language = LangUtil::convertISOFrom3to2($this['language']);
            return new Enum\Language($language);
        }
        return Enum\Language::JA();
    }

    /**
     * Flag that holding get a language setting from browser(= Accept-Language) or not
     *
     * The DB table User.auto_language_flg has a comment
     *   "自動言語設定フラグ(Onの場合はブラウザから言語を取得する)"
     *
     * @return bool
     */
    public function isSetLanguageFromBrowser(): bool
    {
        return boolval($this['auto_language_flg']);
    }
}