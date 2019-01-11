<?php
App::uses('ExceptionRenderer', 'Error');
App::uses('ErrorResponse', 'Lib/Network/Response');

/**
 * Class ApiV2ExceptionRenderer
 */
class ApiV2ExceptionRenderer extends ExceptionRenderer
{
    public function __construct($exception)
    {
        parent::__construct($exception);
        $this->method = 'makeResponse';
    }

    public function makeResponse($error)
    {
        $response = null;
        switch (get_class($error)) {
            case ReflectionException::class:
            case MissingActionException::class:
                if (getenv('ENV_NAME') !== 'isao' && getenv('ENV_NAME') !== 'www') {
                    GoalousLog::error('Action not found', [
                        'message' => $error->getMessage(),
                    ]);
                }
                $response = ErrorResponse::notFound()->withMessage(__('Not Found'))->getResponse();
                break;
            case MissingControllerException::class:
                if (getenv('ENV_NAME') !== 'isao' && getenv('ENV_NAME') !== 'www') {
                    GoalousLog::error('Controller not found', [
                        'message' => $error->getMessage(),
                    ]);
                }
                $response = ErrorResponse::notFound()->withMessage(__('Not Found'))->getResponse();
                break;
            default:
                GoalousLog::error('Uncaught exception', [
                    'exception' => get_class($error),
                    'message'   => $error->getMessage(),
                    'file'      => $error->getFile(),
                    'line'      => $error->getLine(),
                ]);
                $response = ErrorResponse::internalServerError()->withMessage(__('Internal Server Error'))
                                         ->getResponse();
                break;
        }

        $this->controller->response->statusCode($response->statusCode());
        $this->controller->response->body($response->body());
        $this->controller->response->header($response->header());
        $this->controller->response->type('json');
        $this->controller->response->send();
    }
}
