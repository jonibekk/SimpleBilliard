<?php


class TwoFATokenData
{
    /** @var int User ID */
    private $userId;

    /** @var int|null Team ID to login to */
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

    public static function parseArray(array $data): self
    {
        $instance = new self(null, null);

        if (!is_null($data['user_id'])) {
            $instance->userId = $data['user_id'];
        }
        if (!is_null($data['team_id'])) {
            $instance->teamId = $data['team_id'];
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
