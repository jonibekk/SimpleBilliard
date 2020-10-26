<?php

/**
 * Class AccessTokenKey
 *
 * @see https://confluence.goalous.com/x/kYTT
 *      for access token key document
 */
class AccessTokenKey
{
    const CACHE_KEY = "token:auth";

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @param $userId
     * @param $teamId
     * @param $uuid
     */
    public function __construct(string $userId, ?string $teamId, string $uuid)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->uuid   = $uuid;
    }

    public function get(): string
    {
        return sprintf('%s:user:%s:team:%s:%s',
            self::CACHE_KEY,
            $this->userId,
            $this->teamId ?? 'null',
            $this->uuid);
    }
}
