<?php

class AccessTokenData
{
    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var int|null
     */
    private $timeToLive;

    /**
     * @var bool
     */
    private $hideGoalCreateGuidance;

    /**
     * @return int|null
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * @param int $timeToLive
     *
     * @return $this
     */
    public function withTimeToLive(int $timeToLive)
    {
        $this->timeToLive = $timeToLive;
        return $this;
    }

    /**
     * @param string $userAgent
     *
     * @return $this
     */
    public function withUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @param bool $isHide
     * @return $this
     */
    public function withHideGoalCreateGuidance(bool $isHide)
    {
        $this->hideGoalCreateGuidance = $isHide;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent ?? '';
    }

    /**
     * @return bool
     */
    public function isHideGoalCreateGuidance(): bool
    {
        return $this->hideGoalCreateGuidance ?? false;
    }

    public static function parseFromArray(array $data): self
    {
        $instance = new static();

        if (isset($data['user_agent'])) {
            $instance->withUserAgent($data['user_agent']);
        }
        if (isset($data['hide_goal_create_guidance'])) {
            $instance->withHideGoalCreateGuidance($data['hide_goal_create_guidance']);
        }

        return $instance;
    }

    public function toArray(): array
    {
        $r = [];

        if (!is_null($this->userAgent)) {
            $r['user_agent'] = $this->userAgent;
        }
        if (!is_null($this->hideGoalCreateGuidance)) {
            $r['hide_goal_create_guidance'] = $this->hideGoalCreateGuidance;
        }

        return $r;
    }
}
