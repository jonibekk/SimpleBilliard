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
                GoalousLog::error('Action not found', [
                    'message' => $error->getMessage(),
                ]);
                $response = ErrorResponse::notFound()->withMessage(__('Not Found'))->getResponse();
                break;
            case MissingControllerException::class:
                GoalousLog::error('Controller not found', [
                    'message' => $error->getMessage(),
                ]);
                $response = ErrorResponse::notFound()->withMessage(__('Not Found'))->getResponse();
                break;
        }

        $this->controller->response->statusCode($response->statusCode());
        $this->controller->response->body($response->body());
        $this->controller->response->header($response->header());
        $this->controller->response->type('json');
        $this->controller->response->send();
    }
}
