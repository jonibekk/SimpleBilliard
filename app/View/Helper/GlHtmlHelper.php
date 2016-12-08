<?php
App::uses('AppHelper', 'View/Helper');
App::uses('NumberExHelper', 'View/Helper');

/**
 * GlHtmlHelper
 *
 * @property mixed NumberEx
*/
class GlHtmlHelper extends AppHelper
{
    public $helpers = [
        'NumberEx'
    ];

    /**
     * 戻るボタン
     *
     * @param $url
     * @param $label
     *
     * @return string
     */
    function backBtn($url, $label = "")
    {
        $labelTag = "";
        if (!empty($label)) {
            $labelTag = '<span class="btn-back-text">' . $label . '</span>';
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

    /**
     * KR進捗表示
     * @param $kr
     *
     * @return string
     */
    function krProgressBar($kr)
    {
        if (empty($kr)) {
            return "";
        }
        $currentValue = $this->NumberEx->formatProgressValue($kr['current_value'],
            $kr['value_unit']);
        $shortCurrentValue = $this->NumberEx->shortFormatProgressValue($kr['current_value'],
            $kr['value_unit']);

        $startValue = "";
        $targetValue = "";
        $shortStartValue = "";
        $shortTargetValue = "";

        if ($kr['value_unit'] == KeyResult::UNIT_BINARY) {
            $progressRate = empty($kr['completed']) ? 0 : 100;
        } else {
            $progressRate = $this->NumberEx->calcProgressRate($kr['start_value'], $kr['target_value'], $kr['current_value']);
            $this->log(compact('progressRate'));
            $startValue = $this->NumberEx->formatProgressValue($kr['start_value'],
                $kr['value_unit']);
            $targetValue = $this->NumberEx->formatProgressValue($kr['target_value'],
                $kr['value_unit']);
            $shortStartValue = $this->NumberEx->shortFormatProgressValue($kr['start_value'],
                $kr['value_unit']);
            $shortTargetValue = $this->NumberEx->shortFormatProgressValue($kr['target_value'],
                $kr['value_unit']);
        }

        $progressClass = $progressRate == 100 ? "is-complete" : "is-incomplete mod-rate" . $progressRate;
        return <<<HTML
        <div class="krProgress js-show-detail-progress-value"
             data-current_value="$currentValue"
             data-start_value="$startValue"
             data-target_value="$targetValue"
        >
            <div class="krProgress-bar">
                <span class="krProgress-text">
                     $shortCurrentValue 
                </span>
                <div class="krProgress-barCurrent $progressClass"></div>
            </div>
            <div class="krProgress-values">
                <div class="krProgress-valuesLeft">
                    <span>
                         $shortStartValue
                    </span>
                </div>
                <div class="krProgress-valuesRight">
                    <span>
                         $shortTargetValue 
                    </span>
                </div>
            </div>
        </div>
HTML;
    }
}
