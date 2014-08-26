<?php

/**
 * Textヘルパーを拡張
 *
 * @author daikihirakata
 * @property SessionHelper $Session
 * @property TextHelper    $Text
 */
class TextExHelper extends AppHelper
{

    public $helpers = array(
        'Text',
    );

    function autoLink($text)
    {
        return nl2br($this->Text->autoLink(h($text), ['target' => 'blank', 'escape' => false]));
    }

}
