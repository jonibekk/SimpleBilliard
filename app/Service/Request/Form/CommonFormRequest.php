<?php
class CommonFormRequest {
    /* @var int */
    protected $userId;
    /* @var int */
    protected $teamId;

    /**
     * CommonResourceRequest constructor.
     * @param int $userId
     * @param int $teamId
     */
    public function __construct(int $id, int $userId, int $teamId)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }
}
