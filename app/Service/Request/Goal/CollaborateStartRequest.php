<?php
App::import('Service/Request/Traits', 'NotifyBizSettableTraits');

class CollaborateStartRequest
{
    use NotifyBizSettableTraits;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @var string
     */
    private $goalId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var \Goalous\Enum\Model\GoalMember\Type
     */
    private $type;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var \Goalous\Enum\Model\GoalMember\ApprovalStatus
     */
    private $approvalStatus;

    /**
     * @var boolean
     */
    private $isWishApproval;

    /**
     * @var boolean
     */
    private $isTargetEvaluation;

    /**
     * CollaborateStartRequest constructor.
     * @param string $teamId
     * @param string $goalId
     * @param string $userId
     * @param \Goalous\Enum\Model\GoalMember\Type $type
     * @param string $role
     * @param string $description
     * @param int $priority
     */
    public function __construct(string $teamId, string $goalId, string $userId, \Goalous\Enum\Model\GoalMember\Type $type, string $role, string $description, int $priority)
    {
        $this->teamId = $teamId;
        $this->goalId = $goalId;
        $this->userId = $userId;
        $this->type = $type;
        $this->role = $role;
        $this->description = $description;
        $this->priority = $priority;
        $this->setApprovalStatus(\Goalous\Enum\Model\GoalMember\ApprovalStatus::NEW());
        $this->setIsWishApproval(true);
        $this->setIsTargetEvaluation(false);
    }

    /**
     * @return string
     */
    public function getTeamId(): string
    {
        return $this->teamId;
    }

    /**
     * @return string
     */
    public function getGoalId(): string
    {
        return $this->goalId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return \Goalous\Enum\Model\GoalMember\Type
     */
    public function getType(): \Goalous\Enum\Model\GoalMember\Type
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return \Goalous\Enum\Model\GoalMember\ApprovalStatus
     */
    public function getApprovalStatus(): \Goalous\Enum\Model\GoalMember\ApprovalStatus
    {
        return $this->approvalStatus;
    }

    /**
     * @param \Goalous\Enum\Model\GoalMember\ApprovalStatus $approvalStatus
     */
    public function setApprovalStatus(\Goalous\Enum\Model\GoalMember\ApprovalStatus $approvalStatus): void
    {
        $this->approvalStatus = $approvalStatus;
    }

    /**
     * @return bool
     */
    public function isWishApproval(): bool
    {
        return $this->isWishApproval;
    }

    /**
     * @param bool $isWishApproval
     */
    public function setIsWishApproval(bool $isWishApproval): void
    {
        $this->isWishApproval = $isWishApproval;
    }

    /**
     * @return bool
     */
    public function isTargetEvaluation(): bool
    {
        return $this->isTargetEvaluation;
    }

    /**
     * @param bool $isTargetEvaluation
     */
    public function setIsTargetEvaluation(bool $isTargetEvaluation): void
    {
        $this->isTargetEvaluation = $isTargetEvaluation;
    }


}
