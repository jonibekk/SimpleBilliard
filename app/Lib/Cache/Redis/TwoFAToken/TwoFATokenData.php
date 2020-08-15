<?php


class TwoFATokenData
{
    /** @var int User ID */
    private $userId;

    /** @var int Team ID to login to */
    private $teamId;

    public function __construct(?int $userId, ?int $teamId)
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
     * @return int|null
     */
    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId(int $teamId): void
    {
        $this->teamId = $teamId;
    }


    public static function parseArray(array $data): self
    {
        $instance = new self(null, null);

        if (!is_null($data['user_id'])) {
            $instance->setUserId($data['user_id']);
        }
        if (!is_null($data['team_id'])) {
            $instance->setTeamId($data['team_id']);
        }

        return $instance;
    }

    public function toArray(): array
    {
        $array = [];

        if (!is_null($this->userId)) {
            $array['user_id'] = $this->userId;
        }
        if (!is_null($this->teamId)) {
            $array['team_id'] = $this->teamId;
        }

        return $array;
    }

}
