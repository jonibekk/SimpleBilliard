<?php
App::uses('ExceptionRenderer', 'Error');

/**
 * 例外時のレイアウトファイルを変更する為、既存のExceptionRendererを継承しlayoutだけ変更
 * Class AppExceptionRenderer
 */
class AppExceptionRenderer extends ExceptionRenderer
{
    protected function _outputMessage($template)
    {
        $this->controller->layout = LAYOUT_ONE_COLUMN;
        parent::_outputMessage($template);
    }
}