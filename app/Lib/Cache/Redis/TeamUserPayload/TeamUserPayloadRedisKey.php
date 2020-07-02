<?php

class TeamUserPayloadRedisKey
{
    /**
     * @var string
     */
    private $teamId;

    /**
     * @var string
     */
    private $userId;

    /**
     * TeamUserPayloadRedisKey constructor.
     * @param string $teamId
     * @param string $userId
     */
    public function __construct(string $teamId, string $userId)
    {
        $this->teamId = $teamId;
        $this->userId = $userId;
    }

    public function get(): string
    {
        return sprintf('team_user_payload:user:%s:team:%s',
            $this->userId,
            $this->teamId);
    }
}
