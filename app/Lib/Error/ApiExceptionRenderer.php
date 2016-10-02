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
        $code = $error->getCode();
        switch (get_class($error)) {
            case 'MissingActionException':
            case 'MissingControllerException':
                $message = __('Api Endpoint not found.');
                break;
            case 'BadRequestException':
                if ($message == 'The request has been black-holed') {
                    $message = __('CSRF Error!');
                    $code = 403;
                }
                break;
        }

        $this->controller->response->statusCode($code);
        $this->controller->set('data', json_encode(['message' => $message]));
        $this->controller->response->type('json');
        $this->_outputMessage('api_error');
    }

    protected function _outputMessage($template)
    {
        $this->controller->layout = null;
        parent::_outputMessage($template);
    }
}
