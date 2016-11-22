<?php
App::uses('ExceptionRenderer', 'Error');

/**
 * Class AppExceptionRenderer
 */
class ApiExceptionRenderer extends ExceptionRenderer
{
    public function __construct($exception)
    {
        parent::__construct($exception);
        $this->method = 'errorApi';
    }

    public function errorApi($error)
    {
        if (in_array(get_class($error), ['Error', 'InternalErrorException'])) {
            $message = __('Internal Server Error');
            $code = 500;
            $log_message = sprintf("[%s] %s\n%s",
                get_class($error),
                CakeLog::error($error->getMessage()),
                CakeLog::error($error->getTraceAsString())
            );
            CakeLog::write(LOG_ERR, $log_message);
        } else {
            $message = $error->getMessage();
            $code = $error->getCode();
        }
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
