<?php

/**
 * JWT token class for Goalous API authentication
 * @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication#APIv2Authentication-WhatshouldwesetonPayload?
 *
 * ```php
 * // Creating new JWT auth token for Goalous
 * $jwtAuth = new JwtAuthentication($userId = 1, $teamId = 1);
 * echo $jwtAuth->token(); // output JWT token
 * ```
 *
 * ```php
 * // Decoding JWT auth token
 * try {
 *     $jwtAuth = JwtAuthentication::decode($jwtToken);
 * } catch (JwtException $exception) {
 * }
 * $jwtAuth->getTeamId();    // returning teams.id
 * $jwtAuth->getUserId();    // returning users.id
 * $jwtAuth->getJwtId();     // returning JWT token id string
 * $jwtAuth->getExpiredAt(); // returning expire date of GoalousDateTime
 * $jwtAuth->getCreatedAt(); // returning created date of GoalousDateTime
 * ```
 *
 * Class JwtAuthentication
 */
class JwtAuthentication
{
    /**
     * @var GoalousDateTime
     */
    protected $expireAt;

    /**
     * @var GoalousDateTime
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $teamId;

    /**
     * @var string
     */
    protected $jwtId;

    public static function create(int $userId, int $teamId): self
    {
        return new static($userId, $teamId);
    }

    /**
     * JwtAuth constructor.
     *
     * @param int $userId login token for users.id
     * @param int $teamId teams.id to login
     */
    public function __construct(int $userId, int $teamId)
    {
        $this->teamId = $teamId;
        $this->userId = $userId;
    }

    public function withJwtId(string $jwtId): self
    {
        $this->jwtId = $jwtId;
        return $this;
    }

    public function withCreatedAt(GoalousDateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withExpiredAt(GoalousDateTime $expiredAt): self
    {
        $this->expireAt = $expiredAt;
        return $this;
    }

    /**
     * @return string
     */
    public function token(): string
    {
        // :TODO
        if (is_null($this->expireAt)) {
            $this->expireAt = GoalousDateTime::now();
        }
        if (is_null($this->createdAt)) {
            $this->createdAt = GoalousDateTime::now();
        }
        return "";
    }

    /**
     * @return JwtAuthentication
     */
    public static function decode(): self
    {
        // TODO:
        return (new static(1, 1))
            ->withCreatedAt(GoalousDateTime::now())
            ->withExpiredAt(GoalousDateTime::now());
    }

    /**
     * return users.id
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * return teams.id
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->teamId;
    }

    /**
     * return JWT token id
     * @return string
     */
    public function getJwtId(): string
    {
        return $this->jwtId;
    }

    /**
     * return JWT claim "exp" by GoalousDateTime
     * @return GoalousDateTime
     */
    public function expireAt(): GoalousDateTime
    {
        return $this->expireAt;
    }

    /**
     * return JWT claim "iat" by GoalousDateTime
     * @return GoalousDateTime
     */
    public function createdAt(): GoalousDateTime
    {
        return $this->createdAt;
    }
}
