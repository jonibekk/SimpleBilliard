<?php

use Goalous\Enum\NotificationFlag\Name as NotificationFlagName;

class NotificationFlagKey
{
    const CACHE_KEY = "notification_sent";

    /**
     * @var int
     */
    private $teamId;

    /**
     * @var string
     */
    private $notificationName;

    public function __construct(int $teamId, NotificationFlagName $notificationName)
    {
        $this->teamId = $teamId;
        $this->notificationName = $notificationName->getValue();
    }

    public function toRedisKey(): string
    {
        return sprintf('%s:team:%s:%s',
            self::CACHE_KEY,
            $this->teamId,
            $this->notificationName);
    }
}