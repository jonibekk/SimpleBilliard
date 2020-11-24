<?php


class TwoFATokenKey
{
    const CACHE_KEY = "token:2fa";

    /** @var string */
    private $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toKey(): string
    {
        return sprintf(
            '%s:id:%s',
            self::CACHE_KEY,
            $this->uuid
        );
    }
}
