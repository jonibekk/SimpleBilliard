<?php
App::uses('AppHelper', 'View/Helper');

class LangHelper extends AppHelper
{
    public function getLangCode() {
        if(Configure::read('Config.language') == 'eng'):
            return "en";
        else:
            return "jp";
        endif;
    }
}