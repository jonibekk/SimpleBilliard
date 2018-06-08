<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/24
 * Time: 10:05
 */

/**
 * Class ApiResponse Wrapper of CakeResponse class
 * Use method chaining to add content into the response.
 * Usage sample:
 *  return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData('this')->getResponse();
 *  return (new ApiResponse(ApiResponse::RESPONSE_RESOURCE_CONFLICT))->withMessage('conflict')
 *      ->withExceptionTrace(array())->getResponse();
 */
class ApiResponse extends CakeResponse
{
    const RESPONSE_SUCCESS = 200;
    const RESPONSE_BAD_REQUEST = 400;
    const RESPONSE_UNAUTHORIZED = 401;
    const RESPONSE_FORBIDDEN = 403;
    const RESPONSE_NOT_FOUND = 404;
    const RESPONSE_RESOURCE_CONFLICT = 409;
    const RESPONSE_INTERNAL_SERVER_ERROR = 500;

    private $_responseBody = array();

    private $_responseHeader = array();

    public function __construct(int $httpCode)
    {
        parent::__construct();
        $this->statusCode($httpCode);
    }

    /**
     * Set response's HTTP code
     *
     * @param int $httpCode
     *
     * @return ApiResponse
     */
    public function setHttpCode(int $httpCode): ApiResponse
    {
        $this->statusCode($httpCode);

        return $this;
    }

    /**
     * Method encapsulation for returning exception
     *
     * @param Exception $exception
     *
     * @return ApiResponse
     */
    public function withException(Exception $exception)
    {
        $message = $exception->getMessage();
        $trace = $exception->getTrace();
        return $this->withMessage($message)->withExceptionTrace($trace);
    }

    /**
     * Add data to response body
     *
     * @param array|string $data       Data to be sent to the client
     * @param bool         $appendFlag Append input to existing data
     *
     * @return ApiResponse
     */
    public function withData($data, bool $appendFlag = false): ApiResponse
    {
        if (empty($data)) {
            return $this;
        }
        if (!$appendFlag) {
            $this->_responseBody['data'] = $data;
            return $this;
        }
        if (is_array($data)) {
            if (is_int(array_keys($data)[0])) {
                $this->_responseBody['data'] = array_merge($this->_responseBody['data'],
                    $data);
            } else {
                foreach ($data as $key => $value) {
                    $this->_responseBody['data'][] = [$key => $value];
                }
            }
        } elseif (is_string($data)) {
            $this->_responseBody['data'][] = $data;
        }

        return $this;
    }

    /**
     * Add data to response body
     *
     * @param array|string $data       Data to be sent to the client
     * @param bool         $appendFlag Append input to existing data
     *
     * @return ApiResponse
     */
    public function withBody($data, bool $appendFlag = false): ApiResponse
    {
        if (empty($data)) {
            return $this;
        }
        if (!$appendFlag) {
            $this->_responseBody = $data;
            return $this;
        }
        if (is_array($data)) {
            if (is_int(array_keys($data)[0])) {
                $this->_responseBody = array_merge($this->_responseBody,
                    $data);
            } else {
                foreach ($data as $key => $value) {
                    $this->_responseBody[] = [$key => $value];
                }
            }
        } elseif (is_string($data)) {
            $this->_responseBody[] = $data;
        }

        return $this;
    }

    /**
     * Add message to the response body
     *
     * @param string $message    Additional message
     * @param bool   $appendFlag Append input to existing data
     *
     * @return ApiResponse
     */
    public function withMessage($message, bool $appendFlag = false): ApiResponse
    {
        if (empty($message) || !is_string($message)) {
            return $this;
        }
        if ($appendFlag) {
            $this->_responseBody['message'] .= $message . '\n';
        } else {
            $this->_responseBody['message'] = $message;
        }
        return $this;
    }

    /**
     * Add exception trace to the response body
     *
     * @param array|string $exceptionTrace Exception trace for any errors in the server
     * @param bool         $appendFlag     Append input to existing data
     *
     * @return ApiResponse
     */
    public function withExceptionTrace($exceptionTrace, bool $appendFlag = false): ApiResponse
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
     * Add HTTP header for response
     *
     * @param array|string $value
     * @param bool         $appendFlag Append input to existing data
     *
     * @return ApiResponse
     */
    public function withHeader($value, bool $appendFlag = false): ApiResponse
    {
        if (empty($value)) {
            return $this;
        }
        if (!$appendFlag) {
            $this->_responseHeader = $value;
            return $this;
        }
        if (is_array($value)) {
            $this->_responseHeader = array_merge($this->_responseHeader, $value);
        } elseif (is_string($value)) {
            $this->_responseHeader[] = $value;
        }

        return $this;
    }

    /**
     * Set cursors for paging function
     *
     * @param array $paging
     *
     * @return ApiResponse
     */
    public function setPaging(array $paging): ApiResponse
    {
        $this->_responseBody['paging'] = $paging;

        return $this;
    }

    /**
     * Set count number of data matching given condition
     *
     * @param int $count
     *
     * @return ApiResponse
     */
    public function setCount(int $count): ApiResponse
    {
        $this->_responseBody['count'] = $count;

        return $this;
    }

    /**
     * Create the response to be returned to the client
     *
     * @return CakeResponse
     */
    public function getResponse(): CakeResponse
    {
        $this->type('json');
        $this->header($this->_responseHeader);
        $this->body(json_encode($this->_responseBody));
        $this->disableCache();

        return $this;
    }

}