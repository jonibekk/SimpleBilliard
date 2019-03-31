<?php
class UserResourceRequest {
    /* @var int */
    protected $id;
    /* @var int */
    protected $teamId;
    /* @var bool */
    protected $isMe = false;

    /**
     * @param int $id
     * @param int $teamId
     * @param bool $isMe
     */
    public function __construct(int $id, int $teamId, bool $isMe = false)
    {
        $this->id = $id;
        $this->teamId = $teamId;
        $this->isMe = $isMe;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
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


    /**
     * @return bool
     */
    public function isMe(): bool
    {
        return $this->isMe;
    }

    /**
     * @param bool $isMe
     */
    public function setIsMe(bool $isMe)
    {
        $this->isMe = $isMe;
    }
}
