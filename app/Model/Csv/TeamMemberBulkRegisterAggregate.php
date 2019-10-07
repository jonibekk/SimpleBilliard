<?php

/**
 * Class TeamMemberBulkRegisterAggregate
 */
class TeamMemberBulkRegisterAggregate
{
    /** @var int */
    private $success = 0;
    /** @var int */
    private $new_user = 0;
    /** @var int */
    private $exist_user = 0;
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
        $this->new_user += 1;
        return $this;
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    public function addExistUser(): self
    {
        $this->exist_user += 1;
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
        return $this->new_user;
    }

    /**
     * @return int
     */
    public function getExistUser(): int
    {
        return $this->exist_user;
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
