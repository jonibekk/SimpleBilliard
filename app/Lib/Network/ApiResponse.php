<?php
App::uses('BaseApiResponse', 'Lib/Network/Response');

/**
 * Class ApiResponse Wrapper of CakeResponse class
 * Use method chaining to add content into the response.
 * Usage sample:
 *  return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData('this')->getResponse();
 */
class ApiResponse extends BaseApiResponse
{
    const RESPONSE_SUCCESS = 200;

    /**
     * @deprecated
     */
    const RESPONSE_BAD_REQUEST = 400;
    const RESPONSE_UNAUTHORIZED = 401;
    const RESPONSE_FORBIDDEN = 403;
    const RESPONSE_NOT_FOUND = 404;
    const RESPONSE_RESOURCE_CONFLICT = 409;
    const RESPONSE_INTERNAL_SERVER_ERROR = 500;


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
     * Use or replace to app/Lib/Network/Response/ErrorResponse if having error/exception
     * @deprecated
     * @return $this
     */
    public function withExceptionTrace($exceptionTrace, bool $appendFlag = false): self
    {
        return $this;
    }

    /**
     * Use or replace to app/Lib/Network/Response/ErrorResponse if having error/exception
     * @deprecated
     * @return $this
     */
    public function withException(Exception $exception): self
    {
        return $this;
    }
}
