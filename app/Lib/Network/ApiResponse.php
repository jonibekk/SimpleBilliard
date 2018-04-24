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
 *  return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->addData('this')->getResponse();
 *  return (new ApiResponse(ApiResponse::RESPONSE_RESOURCE_CONFLICT))->addMessage('conflict')
 *      ->addExceptionTrace(array())->getResponse();
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

    public function __construct(int $httpCode)
    {
        parent::__construct();
        $this->statusCode($httpCode);
    }

    /**
     * Add data to response body
     *
     * @param array|string $data Data to be sent to the client
     *
     * @return ApiResponse
     */
    public function setData($data): ApiResponse
    {
        $this->_responseBody['data'] = $data;
        return $this;
    }

    /**
     * Add message to the response body
     *
     * @param string $message Additional message
     *
     * @return ApiResponse
     */
    public function setMessage($message): ApiResponse
    {
        $this->_responseBody['message'] = $message;
        return $this;
    }

    /**
     * Add exception trace to the response body
     *
     * @param $exceptionTrace Exception trace for any errors in the server
     *
     * @return ApiResponse
     */
    public function setExceptionTrace($exceptionTrace): ApiResponse
    {
        $this->_responseBody['exception_trace'] = $exceptionTrace;
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
        $this->body(json_encode($this->_responseBody));
        $this->disableCache();

        return $this;
    }

    /**
     * Set HTTP header for response
     *
     * @param string $value
     */
    public function setHeader($value)
    {
        header($value);
    }
}