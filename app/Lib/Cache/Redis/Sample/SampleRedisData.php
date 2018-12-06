<?php

class SampleRedisData
{
    /**
     * @var string
     */
    private $stringData;

    /**
     * SampleRedisData constructor.
     *
     * @param string $stringData
     */
    public function __construct(string $stringData)
    {
        $this->stringData = $stringData;
    }

    /**
     * @return string
     */
    public function getStringData(): string
    {
        return $this->stringData;
    }
}