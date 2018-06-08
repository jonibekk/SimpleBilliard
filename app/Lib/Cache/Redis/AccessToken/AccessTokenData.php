<?php

class AccessTokenData
{
    /**
     *
     *
     * @var string
     */
    private $userAgent;

    /**
     * @var int
     */
    private $timeToLive;

    /**
     * @return int
     */
    public function getTimeToLive(): int
    {
        return $this->timeToLive;
    }

    /**
     * @param int $timeToLive
     *
     * @return $this
     */
    public function withTimeToLive(int $timeToLive)
    {
        $this->timeToLive = $timeToLive;
        return $this;
    }

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