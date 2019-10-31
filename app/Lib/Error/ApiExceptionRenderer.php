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
        $error_classes = [
            'Error',
            'ArithmeticError',
            'DivisionByZeroError',
            'AssertionError',
            'ParseError',
            'TypeError',
        ];
        if (in_array(get_class($error), $error_classes)) {
            $message = __('Internal Server Error');
            $code = 500;
            $log_message = sprintf("[%s] %s\n%s",
                get_class($error),
                $error->getMessage(),
                $error->getTraceAsString()
            );
            GoalousLog::error($log_message);
        } else {
            $message = $error->getMessage();
            $code = $error->getCode();
        }
        switch (get_class($error)) {
            case 'MissingActionException':
            case 'MissingControllerException':
                $message = 'Api Endpoint not found.';
                //TODO GL-7836 In rare case, API endpoint is reported as missing
                $errorData = [
                    'URI'   => $_SERVER['REQUEST_URI'],
                    'trace' => $error->getTrace()
                ];
                GoalousLog::error($message, $errorData);
                break;
            case 'BadRequestException':
                if ($message == 'The request has been black-holed') {
                    $message = __('Some error occurred. Please try again from the start.');
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
