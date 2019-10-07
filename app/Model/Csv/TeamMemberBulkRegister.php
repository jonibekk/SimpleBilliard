<?php

/**
 * Class TeamMemberBulkRegister
 */
class TeamMemberBulkRegister
{
    /** @var array */
    private $team;
    /** @var bool */
    private $dry_run;
    /** @var array */
    private $records;
    /** @var int */
    private $team_all_circle_id;
    /** @var int */
    private $agreed_terms_of_service_id;
    /** @var array */
    private $exist_users;

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
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dry_run;
    }

    /**
     * @param bool $dry_run
     * @return TeamMemberBulkRegister
     */
    public function setDryRun(bool $dry_run): self
    {
        $this->dry_run = $dry_run;
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
        return $this->agreed_terms_of_service_id;
    }

    /**
     * @param int $agreed_terms_of_service_id
     * @return TeamMemberBulkRegister
     */
    public function setAgreedTermsOfServiceId(int $agreed_terms_of_service_id): self
    {
        $this->agreed_terms_of_service_id = $agreed_terms_of_service_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeamAllCircleId(): int
    {
        return $this->team_all_circle_id;
    }

    /**
     * @param int $team_all_circle_id
     * @return TeamMemberBulkRegister
     */
    public function setTeamAllCircleId(int $team_all_circle_id): self
    {
        $this->team_all_circle_id = $team_all_circle_id;
        return $this;
    }

    /**
     * @param array $exist_users
     * @return TeamMemberBulkRegister
     */
    public function setExistUsers(array $exist_users): self
    {
        $this->exist_users = $exist_users;
        return $this;
    }

    /**
     * @return array
     */
    public function getExistUsers(): array
    {
        return $this->exist_users;
    }

    /**
     * @return array
     */
    public function getEmailUserMap(): array
    {
        return Hash::combine($this->getExistUsers(), '{n}.email', '{n}.user_id');
    }

    /**
     * @param string $email_id
     * @return string|null
     */
    public function getExistUserId(string $email_id): ?string
    {
        $email_user_map = $this->getEmailUserMap();
        return $email_user_map[$email_id] ?? null;
    }

    /**
     * @return array
     */
    private function getTeam(): array
    {
        return $this->team;
    }
}
