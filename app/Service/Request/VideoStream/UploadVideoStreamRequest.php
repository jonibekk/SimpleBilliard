<?php

class UploadVideoStreamRequest
{
    /**
     * @var array
     */
    protected $uploadFile;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $teamId;

    /**
     * @var int
     */
    protected $secondsDurationLimit = 60;

    /**
     * UploadVideoStreamRequest constructor.
     * @param array $uploadFile
     * @param int $userId
     * @param int $teamId
     */
    public function __construct(array $uploadFile, int $userId, int $teamId)
    {
        $this->uploadFile = $uploadFile;
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    /**
     * @return int
     */
    public function getSecondsDurationLimit(): int
    {
        return $this->secondsDurationLimit;
    }

    /**
     * @param int $secondsDurationLimit
     */
    public function setSecondsDurationLimit(int $secondsDurationLimit)
    {
        $this->secondsDurationLimit = $secondsDurationLimit;
    }

    /**
     * @return array
     */
    public function getUploadFile(): array
    {
        return $this->uploadFile;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->teamId;
    }
}
