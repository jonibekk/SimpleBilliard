<?php

/**
 * Class TeamMemberBulkRegisterAggregate
 */
class TeamMemberBulkRegisterAggregate
{
    /** @var int */
    private $successCount = 0;
    /** @var int */
    private $newUserCount = 0;
    /** @var int */
    private $existUserCount = 0;
    /** @var int */
    private $failedCount = 0;
    /** * @var int */
    private $excludedCount = 0;

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addSuccessCount(): self
    {
        $this->successCount += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addNewUserCount(): self
    {
        $this->newUserCount += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addExistUserCount(): self
    {
        $this->existUserCount += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addFailedCount(): self
    {
        $this->failedCount += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addExcludedCount(): self
    {
        $this->excludedCount += 1;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * @return int
     */
    public function getNewUserCount(): int
    {
        return $this->newUserCount;
    }

    /**
     * @return int
     */
    public function getExistUserCount(): int
    {
        return $this->existUserCount;
    }

    /**
     * @return int
     */
    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    /**
     * @return int
     */
    public function getExcludedCount(): int
    {
        return $this->excludedCount;
    }
}
