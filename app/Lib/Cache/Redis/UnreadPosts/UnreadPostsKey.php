<?php

class UnreadPostsKey
{
    const CACHE_KEY = "unread_posts";

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @param $userId
     * @param $teamId
     * @param $uuid
     */
    public function __construct(string $userId, string $teamId)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    public function get(): string
    {
        return sprintf('team:%s:user:%s:%s',
            $this->teamId,
            $this->userId,
            self::CACHE_KEY);
    }
}