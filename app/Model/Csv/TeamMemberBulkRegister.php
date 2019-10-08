<?php

/**
 * Class TeamMemberBulkRegister
 */
class TeamMemberBulkRegister
{
    /** @var array */
    private $team;
    /** @var array */
    private $records;
    /** @var int */
    private $teamAllCircleId;
    /** @var int */
    private $agreedTermsOfServiceId;
    /** @var array */
    private $existUsers;

    /**
     * @param array $team
     * @return TeamMemberBulkRegister
     */
    public function setTeam(array $team): self
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @param array $records
     * @return TeamMemberBulkRegister
     */
    public function setRecords(array $records): self
    {
        $this->records = $records;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeamId(): int
    {
        return (int) Hash::get($this->getTeam(), 'Team.id');
    }

    /**
     * @return string
     */
    public function getTeamName(): string
    {
        return Hash::get($this->getTeam(), 'Team.name');
    }

    /**
     * @return float
     */
    public function getTeamTimezone(): float
    {
        return (float) Hash::get($this->getTeam(), 'Team.timezone');
    }

    /**
     * @return int
     */
    public function getAgreedTermsOfServiceId(): int
    {
        return $this->agreedTermsOfServiceId;
    }

    /**
     * @param int $agreedTermsOfServiceId
     * @return TeamMemberBulkRegister
     */
    public function setAgreedTermsOfServiceId(int $agreedTermsOfServiceId): self
    {
        $this->agreedTermsOfServiceId = $agreedTermsOfServiceId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeamAllCircleId(): int
    {
        return $this->teamAllCircleId;
    }

    /**
     * @param int $teamAllCircleId
     * @return TeamMemberBulkRegister
     */
    public function setTeamAllCircleId(int $teamAllCircleId): self
    {
        $this->teamAllCircleId = $teamAllCircleId;
        return $this;
    }

    /**
     * @param array $existUsers
     * @return TeamMemberBulkRegister
     */
    public function setExistUsers(array $existUsers): self
    {
        $this->existUsers = $existUsers;
        return $this;
    }

    /**
     * @return array
     */
    public function getExistUsers(): array
    {
        return $this->existUsers;
    }

    /**
     * @return array
     */
    public function getEmailUserMap(): array
    {
        return Hash::combine($this->getExistUsers(), '{n}.email', '{n}.user_id');
    }

    /**
     * @param string $emailId
     * @return string|null
     */
    public function getExistUserId(string $emailId): ?string
    {
        $emailUserMap = $this->getEmailUserMap();
        return $emailUserMap[$emailId] ?? null;
    }

    /**
     * @return array
     */
    private function getTeam(): array
    {
        return $this->team;
    }
}
