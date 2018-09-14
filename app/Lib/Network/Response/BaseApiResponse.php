<?php
App::uses('CakeResponse', 'Network');

abstract class BaseApiResponse extends CakeResponse
{
    protected $_responseBody = [];

    protected $_responseHeader = [];

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
     * @return $this
     */
    public function setHttpCode(int $httpCode): self
    {
        $this->statusCode($httpCode);

        return $this;
    }

    /**
     * Add data to response body
     *
     * @param array|string $data       Data to be sent to the client
     * @param bool         $appendFlag Append input to existing data
     *
     * @return $this
     */
    public function withBody($data, bool $appendFlag = false): self
    {
        if (empty($data)) {
            return $this;
        }
        if (!$appendFlag) {
            $this->_responseBody = $data;
            return $this;
        }
        if (is_array($data)) {
            $data = $this->convertElementsToString($data);
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
     * @return $this
     */
    public function withMessage($message, bool $appendFlag = false): self
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
     * Add HTTP header for response
     *
     * @param array|string $value
     * @param bool         $appendFlag Append input to existing data
     *
     * @return $this
     */
    public function withHeader($value, bool $appendFlag = false): self
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
     * Create the response to be returned to the client
     *
     * @return $this
     */
    public function getResponse(): self
    {
        $this->type('json');
        $this->header($this->_responseHeader);
        $this->body(json_encode($this->_responseBody));
        $this->disableCache();

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function convertElementsToString(array $data): array
    {
        $keys = array_keys($data);

        foreach ($keys as $key) {
            if (is_array($data["key"])) {
                $data[$key] = $this->convertElementsToString($data[$key]);
            } elseif (strpos($key, "id") !== false && !is_string($data[$key])) {
                $data[$key] = strval($data[$key]);
            }
        }
    }

}
