<?php
App::uses('BaseApiResponse', 'Lib/Network/Response');
App::uses('ErrorTypeGlobal', 'Lib/Network/Response/ErrorResponseBody');
App::uses('ErrorTypeValidation', 'Lib/Network/Response/ErrorResponseBody');

class ErrorResponse extends BaseApiResponse
{
    const RESPONSE_BAD_REQUEST = 400;
    const RESPONSE_UNAUTHORIZED = 401;
    const RESPONSE_FORBIDDEN = 403;
    const RESPONSE_NOT_FOUND = 404;
    const RESPONSE_RESOURCE_CONFLICT = 409;
    const RESPONSE_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var AbstractErrorType[]
     */
    private $errors = [];

    public function __construct(int $httpCode)
    {
        parent::__construct($httpCode);
        $this->_responseBody['message'] = '';
    }

    /**
     * @param AbstractErrorType $errorType
     *
     * @return $this
     */
    public function withError(AbstractErrorType $errorType): self
    {
        array_push($this->errors, $errorType);
        return $this;
    }

    /**
     * @param \Respect\Validation\Exceptions\AllOfException $exception
     * @return $this
     */
    public function addErrorsFromValidationException(\Respect\Validation\Exceptions\AllOfException $exception): self
    {
        $validationExceptions = $exception->getIterator();

        foreach ($validationExceptions as $exception) {
            $this->withError(new ErrorTypeValidation($exception->getName(), $exception->getMessage()));
        }

        return $this;
    }

    /**
     * Add exception trace to the response body
     *
     * @param array|string $exceptionTrace Exception trace for any errors in the server
     * @param bool         $appendFlag     Append input to existing data
     *
     * @return $this
     */
    public function withExceptionTrace($exceptionTrace, bool $appendFlag = false): self
    {
        if (empty($exceptionTrace)) {
            return $this;
        }
        if (ENV_NAME !== "dev") {
            return $this;
        }
        if (!$appendFlag) {
            $this->_responseBody['exception_trace'] = $exceptionTrace;
            return $this;
        }
        if (is_array($exceptionTrace)) {
            if (is_int(array_keys($exceptionTrace)[0])) {
                $this->_responseBody['exception_trace'] = array_merge($this->_responseBody['exception_trace'],
                    $exceptionTrace);
            } else {
                foreach ($exceptionTrace as $key => $value) {
                    $this->_responseBody['exception_trace'][] = [$key => $value];
                }
            }
        } elseif (is_string($exceptionTrace)) {
            $this->_responseBody['exception_trace'][] = $exceptionTrace;
        }

        return $this;
    }

    /**
     * Method encapsulation for returning exception
     *
     * @param Exception $exception
     *
     * @return $this
     */
    public function withException(Exception $exception)
    {
        $message = $exception->getMessage();
        $trace = $exception->getTrace();
        return $this->withMessage($message)->withExceptionTrace($trace);
    }

    /**
     * @return $this
     */
    public function getResponse(): BaseApiResponse
    {
        parent::getResponse();
        $this->body(json_encode($this->getBody()));
        return $this;
    }

    /**
     * Returning error format response body by array
     * @return array
     */
    private function getBody(): array
    {
        return [
            'message' => $this->getMainMessage(),
            'errors' => array_map(function(AbstractErrorType $error) {
                return $error->toArray();
            }, $this->errors),
        ];
    }

    /**
     * Solve main message of error response
     * @return string
     */
    private function getMainMessage(): string
    {
        if (empty($this->errors)) {
            return $this->_responseBody['message'] ?? '';
        }

        if (empty($this->_responseBody['message'])) {
            return reset($this->errors)->getMessage();
        }

        return $this->_responseBody['message'];
    }

}
