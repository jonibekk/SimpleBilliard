<?php
/**
 * Created by PhpStorm.
 * User: raharjas
 * Date: 16/04/2018
 * Time: 14:48
 */

class ApiResponse
{
    private $_protocol = 'HTTP/1.1';

    private $_header = [];

    /**
     * Create JSON response message, encoded in UTF-8
     *
     * @param int         $httpResponseCode HTTP status code of the message
     * @param string|null $message          Response message content
     * @param string|null $exception        Error message, if exists
     * @param string|null $exceptionTrace   Error trace, if exists
     */
    public function _createResponse(
        int $httpResponseCode,
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $response['message'] = $message;
        $response['exception'] = $exception;
        $response['exception_trace'] = $exceptionTrace;

        $this->_sendHeader("{$this->_protocol} {$httpResponseCode}");
        $this->_setContentLength();
        $this->_setContentType();
        foreach ($this->_header as $header => $values) {
            foreach ((array)$values as $value) {
                $this->_sendHeader($header, $value);
            }
        }
        $this->_sendResponse(json_encode($response));
    }

    public function addHeaderEntry($header = null, $value = null)
    {
        if ($header === null) {
            return $this->_header;
        }
        $headers = is_array($header) ? $header : array($header => $value);
        foreach ($headers as $header => $value) {
            if (is_numeric($header)) {
                list($header, $value) = array($value, null);
            }
            if ($value === null && strpos($header, ':') !== false) {
                list($header, $value) = explode(':', $header, 2);
            }
            $this->_header[$header] = is_array($value) ? array_map('trim', $value) : trim($value);
        }
        return $this->_header;
    }

    /**
     * @param string      $name  HTTP Header key name
     * @param string|null $value HTTP header value
     */
    private function _sendHeader(string $name, string $value = null)
    {
        header("{$name}: {$value}");
    }

    private function _setContentType()
    {
        $this->_sendHeader('Content-Type', "'text/html'; charset='UTF-8'");
    }

    /**
     * @param string $responseBody
     */
    private function _sendResponse(string $responseBody)
    {
        echo $responseBody;
    }
}