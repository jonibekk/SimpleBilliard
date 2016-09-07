<?php
App::uses('ExceptionRenderer', 'Error');

/**
 * 例外時のレイアウトファイルを変更する為、既存のExceptionRendererを継承しlayoutだけ変更
 * Class AppExceptionRenderer
 */
class AppExceptionRenderer extends ExceptionRenderer
{
    protected function _outputMessage($template, $layout = true)
    {
        if ($layout) {
            $this->controller->layout = LAYOUT_ONE_COLUMN;
        } else {
            $this->controller->layout = null;
        }
        parent::_outputMessage($template);
    }
}
