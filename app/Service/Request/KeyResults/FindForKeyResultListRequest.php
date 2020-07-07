<?php

class FindForKeyResultListRequest
{
    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var integer
     */
    protected $teamId;

    /**
     * @var array
     */
    protected $currentTermModel;

    /**
     * @var null|integer
     */
    protected $goalIdSelected;

    /**
     * @var null|integer
     */
    protected $limit;

    /**
     * @var null|boolean
     */
    protected $onlyKrIncomplete;

    /**
     * FindForKeyResultListRequest constructor.
     * @param int $userId
     * @param int $teamId
     * @param array $currentTermModel
     */
    public function __construct(int $userId, int $teamId, array $currentTermModel)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->currentTermModel = $currentTermModel;
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

    /**
     * @return array
     */
    public function getCurrentTermModel(): array
    {
        return $this->currentTermModel;
    }

    /**
     * @return int|null
     */
    public function getGoalIdSelected(): ?int
    {
        return $this->goalIdSelected;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return bool|null
     */
    public function getOnlyKrIncomplete(): ?bool
    {
        return $this->onlyKrIncomplete;
    }

    /**
     * @param int|null $goalIdSelected
     */
    public function setGoalIdSelected(?int $goalIdSelected): void
    {
        $this->goalIdSelected = $goalIdSelected;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param bool|null $onlyKrIncomplete
     */
    public function setOnlyKrIncomplete(?bool $onlyKrIncomplete): void
    {
        $this->onlyKrIncomplete = $onlyKrIncomplete;
    }
}
