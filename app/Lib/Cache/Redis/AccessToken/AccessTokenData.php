<?php

class AccessTokenData
{
    /**
     * @var string
     */
    private $userAgent;

    /**
     * @param string $userAgent
     *
     * @return $this
     */
    public function withUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent ?? '';
    }
}