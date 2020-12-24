<?php
App::uses('BaseApiResponse', 'Lib/Network/Response');
App::uses('ErrorTypeGlobal', 'Lib/Network/Response/ErrorResponseBody');
App::uses('ErrorTypeValidation', 'Lib/Network/Response/ErrorResponseBody');
App::import('Utility', 'CustomLogger');

class ErrorResponse extends BaseApiResponse
{
    const RESPONSE_BAD_REQUEST = 400;
    const RESPONSE_UNAUTHORIZED = 401;
    const RESPONSE_FORBIDDEN = 403;
    const RESPONSE_NOT_FOUND = 404;
    const RESPONSE_RESOURCE_CONFLICT = 409;
    const RESPONSE_INTERNAL_SERVER_ERROR = 500;

    /**
     * @param int $httpStatusCode
     *
     * @return self
     */
    private static function createResponse(int $httpStatusCode): self
    {
        return new self($httpStatusCode);
    }

    /**
     * Create response 400
     *
     * @return self
     */
    public static function badRequest(): self
    {
        return self::createResponse(self::RESPONSE_BAD_REQUEST);
    }

    /**
     * Create response 401
     *
     * @return self
     */
    public static function unauthorized(): self
    {
        return self::createResponse(self::RESPONSE_UNAUTHORIZED);
    }

    /**
     * Create response 403
     *
     * @return self
     */
    public static function forbidden(): self
    {
        return self::createResponse(self::RESPONSE_FORBIDDEN);
    }

    /**
     * Create response 404
     *
     * @return self
     */
    public static function notFound(): self
    {
        return self::createResponse(self::RESPONSE_NOT_FOUND);
    }

    /**
     * Create response 409
     *
     * @return self
     */
    public static function resourceConflict(): self
    {
        return self::createResponse(self::RESPONSE_RESOURCE_CONFLICT);
    }

    /**
     * Create response 500
     *
     * @return self
     */
    public static function internalServerError(): self
    {
        return self::createResponse(self::RESPONSE_INTERNAL_SERVER_ERROR);
    }

    /**
     * @var AbstractErrorType[]
     */
    private $errors = [];

    public function __construct(int $httpCode)
    {
        parent::__construct($httpCode);
        $this->setDefaultMessageByStatusCode($httpCode);
    }

    /**
     * Set default response message
     * ※ Can overwrite message by parent::withMessage method
     * @param int $httpCode
     */
    protected function setDefaultMessageByStatusCode(int $httpCode)
    {
        switch ($httpCode) {
            case self::RESPONSE_BAD_REQUEST:
                $this->_responseBody['message'] = __('Validation failed.');
                break;
            case self::RESPONSE_UNAUTHORIZED:
                $this->_responseBody['message'] = __('Please log in.');
                break;
            case self::RESPONSE_FORBIDDEN:
                $this->_responseBody['message'] = __('You have no permission.');
                break;
            case self::RESPONSE_NOT_FOUND:
                $this->_responseBody['message'] = "";
                break;
            case self::RESPONSE_RESOURCE_CONFLICT:
                $this->_responseBody['message'] = "";
                break;
            case self::RESPONSE_INTERNAL_SERVER_ERROR:
                $this->_responseBody['message'] = __('Server error occurred. We apologize for the inconvenience. Please try again.');
                break;
            default:
                $this->_responseBody['message'] = "The requested resource could not be found.";
        }
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
     *
     * @return $this
     */
    public function addErrorsFromValidationException(\Respect\Validation\Exceptions\AllOfException $exception): self
    {
        $validationExceptions = $exception->getIterator();

        foreach ($validationExceptions as $key => $exception) {
            $validationRuleError = new ErrorTypeValidation($exception->getName(), $exception->getMessage());
            if ($key === 0) {
                // Set first error as message
                $this->withMessage($validationRuleError->toArray()['message']);
            }
            $this->withError($validationRuleError);
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
    private function withExceptionTrace($exceptionTrace, bool $appendFlag = false): self
    {
        if (empty($exceptionTrace)) {
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
     * @param Throwable $throwable
     *
     * @return $this
     */
    public function withException(Throwable $throwable)
    {
        CustomLogger::getInstance()->logException($throwable);
        if (!in_array(ENV_NAME, [
            'local',
            'dev'
        ])) {
            return $this;
        }
        $message = sprintf('%s: %s on %s line %s code %s', get_class($throwable), $throwable->getMessage(),
            $throwable->getFile(), $throwable->getLine(), $throwable->getCode());
        $trace = explode(PHP_EOL, $throwable->getTraceAsString());
        return $this
            ->withError(new ErrorTypeGlobal($message))
            ->withExceptionTrace($trace);
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
     *
     * @return array
     */
    private function getBody(): array
    {
        $exceptionTrace = !empty($this->_responseBody['exception_trace']) ? ['trace' => $this->_responseBody['exception_trace']] : [];
        return array_merge([
            'message' => $this->getMainMessage(),
            'errors'  => array_map(function (AbstractErrorType $error) {
                return $error->toArray();
            }, $this->errors),
        ], $exceptionTrace);
    }

    /**
     * Solve main message of error response
     *
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
