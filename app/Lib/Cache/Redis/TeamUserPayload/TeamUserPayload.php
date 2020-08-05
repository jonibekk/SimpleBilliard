<?php

class TeamUserPayload
{
    private $payloads = [];

    /**
     * TeamUserPayload constructor.
     * @param array $payloads
     */
    public function __construct(array $payloads)
    {
        $this->payloads = $payloads;
    }

    /**
     * @param TeamUserPayloadType $teamUserPayloadType
     * @param null $default
     * @return null|mixed
     */
    public function get(TeamUserPayloadType $teamUserPayloadType, $default = null)
    {
        $key = $teamUserPayloadType->getKeyLowerCase();
        if (isset($this->payloads[$key])) {
            return $this->payloads[$key];
        }
        return $default;
    }
}
