<?php
App::uses('AppHelper', 'View/Helper');

class LangHelper extends AppHelper
{
    public function getLangCode() {
        if(in_array(Configure::read('Config.language'),array('jpn','ja'))) {
            return "ja";
        }else {
            return "en";
        }
    }
}