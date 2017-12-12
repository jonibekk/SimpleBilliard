<?php

use Goalous\Model\Enum as Enum;

class TeamStatus {

    /**
     * @var self
     */
    private static $current = null;

    private $teamId = null;

    private $isTeamPaidPlusPlan = false;

    private $isTeamCampaign = false;

    private $enabledVideoPostInEnvironment = false;

    private $serviceUserStatus = null;

    private function __construct()
    {
        $this->serviceUserStatus = Enum\Team\ServiceUseStatus::FREE_TRIAL();
    }

    /**
     * singleton
     * @return TeamStatus
     */
    public static function getCurrentTeam(): self
    {
        if (is_null(self::$current)) {
            self::$current = new self();
        }
        return self::$current;
    }

    public function setServiceUseStatus(Enum\Team\ServiceUseStatus $serviceUseStatus)
    {
        $this->serviceUserStatus = $serviceUseStatus;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     *
     * @param bool $isTeamPaidPlusPlan
     */
    public function setIsTeamPaidPlusPlan(bool $isTeamPaidPlusPlan)
    {
        $this->isTeamPaidPlusPlan = $isTeamPaidPlusPlan;
    }

    /**
     * set true if team paid on campaign
     *
     * @param bool $isTeamCampaign
     */
    public function setIsTeamCampaign(bool $isTeamCampaign)
    {
        $this->isTeamCampaign = $isTeamCampaign;
    }

    /**
     * set bool if current environment is video post enabled
     *
     * @param bool $isEnabled
     */
    public function setEnabledVideoPostInEnvironment(bool $isEnabled)
    {
        $this->enabledVideoPostInEnvironment = $isEnabled;
    }

    /**
     * return true if team is on any paid plan
     *
     * @return bool
     */
    public function isTeamPaid(): bool
    {
        return $this->getServiceUseStatus()->equals(Enum\Team\ServiceUseStatus::PAID());
    }

    /**
     * return true if team is not paid any plan
     *
     * @return bool
     */
    public function isTeamPlanRegular(): bool
    {
        return !$this->isTeamPaid();
    }

    /**
     * return true if able to post video
     *
     * @return bool
     */
    public function isAbleToPostVideo(): bool
    {
        return $this->enabledVideoPostInEnvironment;
    }

    /**
     * this method is for testing
     * do not use in product code
     */
    public static function reset()
    {
        self::$current = null;
    }

    /**
     * return current users team id
     *
     * @return null|int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @return Enum\Team\ServiceUseStatus
     */
    public function getServiceUseStatus(): Enum\Team\ServiceUseStatus
    {
        return $this->serviceUserStatus;
    }

    /**
     * return teams plan
     *
     * @return Enum\TeamPlan
     */
    public function getTeamPlan(): Enum\TeamPlan
    {
        if ($this->getServiceUseStatus()->equals(Enum\Team\ServiceUseStatus::PAID())) {
            if ($this->isTeamPaidPlusPlan) {
                return Enum\TeamPlan::PAID_PLUS();
            } else {
                return Enum\TeamPlan::PAID();
            }
        }
        return Enum\TeamPlan::REGULAR();
    }

    /**
     * return teams video transcoding quality
     *
     * @return Enum\TranscodePattern
     */
    public function getTranscodeQuality(): Enum\TranscodePattern
    {
        if ($this->isTeamPaid()
            && $this->isTeamPaidPlusPlan) {
            return Enum\TranscodePattern::FULL();
        }
        return Enum\TranscodePattern::LIMITED();
    }
}
