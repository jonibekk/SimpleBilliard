<?php
App::uses('ExceptionRenderer', 'Error');

/**
 * 例外時のレイアウトファイルを変更する為、既存のExceptionRendererを継承しlayoutだけ変更
 * Class AppExceptionRenderer
 */
class AppExceptionRenderer extends ExceptionRenderer
{
    public function __construct(Exception $exception)
    {
        parent::__construct($exception);

        if ($exception instanceof ApiException) {
            $this->method = 'errorApi';
        }
    }

    public function errorApi($error)
    {
        CakeLog::error('### errorApi');
        $message = $error->getMessage();
        $this->controller->response->statusCode($error->getCode());
        $this->controller->set('data', json_encode(['message' => $message]));
        $this->controller->response->type('json');
        $this->_outputMessage('api_error', false);
    }

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
