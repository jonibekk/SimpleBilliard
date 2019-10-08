<?php

/**
 * Class TeamMemberBulkRegisterAggregate
 */
class TeamMemberBulkRegisterAggregate
{
    /** @var int */
    private $success = 0;
    /** @var int */
    private $newUser = 0;
    /** @var int */
    private $existUser = 0;
    /** @var int */
    private $failed = 0;
    /** * @var int */
    private $excluded = 0;

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addSuccess(): self
    {
        $this->success += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addNewUser(): self
    {
        $this->newUser += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addExistUser(): self
    {
        $this->existUser += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addFailed(): self
    {
        $this->failed += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addExcluded(): self
    {
        $this->excluded += 1;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getNewUser(): int
    {
        return $this->newUser;
    }

    /**
     * @return int
     */
    public function getExistUser(): int
    {
        return $this->existUser;
    }

    /**
     * @return int
     */
    public function getFailed(): int
    {
        return $this->failed;
    }

    /**
     * @return int
     */
    public function getExcluded(): int
    {
        return $this->excluded;
    }
}
