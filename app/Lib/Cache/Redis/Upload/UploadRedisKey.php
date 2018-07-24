<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/24
 * Time: 14:25
 */

class UploadRedisKey
{
    const CACHE_KEY = "file:upload";

    /** @var int */
    private $userId;

    /** @var int */
    private $teamId;

    /** @var string */
    private $fileUUID;

    public function __construct(int $userId, int $teamId, string $fileUUID)
    {
        $this->userId = $userId;

        $this->teamId = $teamId;

        $this->fileUUID = $fileUUID;
    }

    public function get(): string
    {
        return sprintf('%s:user:%d:team:%d:%s',
            self::CACHE_KEY,
            $this->userId,
            $this->teamId,
            $this->fileUUID);
    }

    public function getWithoutID(): string
    {
        return sprintf('%s:user:%d:team:%d',
            self::CACHE_KEY,
            $this->userId,
            $this->teamId);
    }
}