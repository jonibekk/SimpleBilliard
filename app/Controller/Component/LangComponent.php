<?php

class LangComponent extends CakeObject
{

    public $name = "Lang";
    var $User;
    var $Invite;

    /**
     * @var $L10n L10n
     */
    public $L10n;
    /**
     * @var $Languages Languages
     */
    public $Languages;
    /**
     * @var AppController
     */
    var $Controller;

    var $model;

    public $availableLanguages = array(
        'eng' => null,
        'jpn' => null,
    );

    protected function _setLangList()
    {
        $this->availableLanguages['jpn'] = __("Japanese");
        $this->availableLanguages['eng'] = __("English");
    }

    function initialize()
    {
        if (!$this->L10n) {
            App::uses('L10n', 'I18n');
            $this->L10n = new L10n();
        }
        if (!$this->Languages) {
            App::import('Lib', 'Utils.Languages');
            $this->Languages = new Languages();
        }
        $this->_setLangList();

    }

    function startup($controller)
    {

    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    function getShortLang()
    {
        $res = $this->L10n->map($this->L10n->catalog(Configure::read('Config.language')));
        if ($res === false) {
            return null;
        }
        $res = array_shift($res);
        return $res;
    }

    function getLanguage()
    {
        $catalog_searched = $this->L10n->catalog($this->L10n->get());
        if (isset($catalog_searched['localeFallback']) && is_array($catalog_searched)) {
            foreach ($this->availableLanguages as $key => $val) {
                //使用可能言語の場合はその言語を返す
                if ($catalog_searched['localeFallback'] == $key) {
                    return $catalog_searched['localeFallback'];
                }
            }
            //使用可能言語じゃなければ英語を返す
            return 'eng';
        } else {
            return null;
        }
    }

    function changeLang($lang = null)
    {
        $set_lang = null;
        $catalog_searched = $this->L10n->catalog($lang);
        if (isset($catalog_searched['localeFallback']) && is_array($catalog_searched)) {
            foreach ($this->availableLanguages as $language) {
                //使用可能言語の場合はその言語を返す
                if ($catalog_searched['localeFallback'] == $language) {
                    $set_lang = $catalog_searched['localeFallback'];
                    break;
                }
            }
        }
        if ($set_lang) {
            //指定された言語が使用可能な場合
            Configure::write('Config.language', $lang);
        } else {
            //使用可能でない場合は英語をセット
            Configure::write('Config.language', 'eng');
        }
    }

    /**
     * 利用可能な言語のリストを返却
     *
     * @return array
     */
    public function getAvailLangList()
    {
        return $this->availableLanguages;
    }
}
