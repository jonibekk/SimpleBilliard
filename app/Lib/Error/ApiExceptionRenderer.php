<?php
App::uses('ExceptionRenderer', 'Error');

/**
 * Class AppExceptionRenderer
 */
class ApiExceptionRenderer extends ExceptionRenderer
{
    public function __construct(Exception $exception)
    {
        parent::__construct($exception);
        $this->method = 'errorApi';
    }

    public function errorApi($error)
    {
        $message = $error->getMessage();
        switch (get_class($error)) {
            case 'MissingActionException':
            case 'MissingControllerException':
                $message = 'Api Endpoint not found.';
                break;
        }

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
