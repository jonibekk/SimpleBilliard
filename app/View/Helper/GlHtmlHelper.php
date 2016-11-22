<?php
App::uses('AppHelper', 'View/Helper');

/**
 * GlHtmlHelper
 */
class GlHtmlHelper extends AppHelper
{
    /**
     * 戻るボタン
     * @param $url
     * @param $label
     *
     * @return string
     */
    function backBtn($url, $label = "")
    {
        $labelTag = "";
        if (!empty($label)) {
            $labelTag = '<span class="btn-back-text">'.$label.'</span>';
        }
        $html = <<<HTML
            <div class="col-sm-8 col-sm-offset-2">
                <a href="{$url}" class="btn-back">
                    <i class="fa fa-chevron-left font_18px font_lightgray lh_20px"></i>
                    {$labelTag}
                    
                </a>
            </div>

HTML;
        return $html;
    }
}
