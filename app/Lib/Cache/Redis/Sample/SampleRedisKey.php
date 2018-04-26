<?php

class SampleRedisKey
{
    const CACHE_KEY_SAMPLE = "sample_cache_key";

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $teamId;

    /**
     * SampleRedisKey constructor.
     *
     * @param $userId
     * @param $teamId
     */
    public function __construct(string $userId, string $teamId)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    public function get(): string
    {
        return sprintf('%s:user:%s:team:%s',
            self::CACHE_KEY_SAMPLE,
            $this->userId,
            $this->teamId);
    }
}
