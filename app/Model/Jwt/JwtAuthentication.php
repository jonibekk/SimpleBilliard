<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\ExpiredException;

App::uses('JwtException', 'Model/Jwt/Exception');
App::uses('JwtSignatureException', 'Model/Jwt/Exception');
App::uses('JwtExpiredException', 'Model/Jwt/Exception');


/**
 * JWT token class for Goalous API authentication
 * @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication#APIv2Authentication-WhatshouldwesetonPayload?
 *
 * ```php
 * App::uses('JwtAuthentication', 'Model/Jwt');
 *
 * // Creating new JWT auth token for Goalous
 * $jwtAuth = new JwtAuthentication($userId = 1, $teamId = 1);
 * echo $jwtAuth->token(); // output JWT token
 * ```
 *
 * ```php
 * // Decoding JWT auth token
 * try {
 *     $jwtAuth = JwtAuthentication::decode($jwtToken);
 * } catch (JwtSignatureException $exception) {
 *      // When invalid signature
 * } catch (JwtExpiredException $exception) {
 *      // When token is expired
 * } catch (JwtException $exception) {
 *      // When something other is invalid
 * }
 * $jwtAuth->getTeamId(); // returning teams.id
 * $jwtAuth->getUserId(); // returning users.id
 * $jwtAuth->getJwtId();  // returning JWT token id string
 * $jwtAuth->expireAt();  // returning expire date of GoalousDateTime
 * $jwtAuth->createdAt(); // returning created date of GoalousDateTime
 * ```
 *
 * Class JwtAuthentication
 */
class JwtAuthentication
{
    const PAYLOAD_NAMESPACE = 'goalous.com';

    /**
     * Stands for JWT Claim 'exp' (Expiration Time)
     * @var GoalousDateTime
     */
    protected $expireAt;

    /**
     * Stands for JWT Claim 'iat' (Issued At)
     * @var GoalousDateTime
     */
    protected $createdAt;

    /**
     * Stands for JWT Claim 'jti' (JWT ID)
     * @var string
     */
    protected $jwtId;

    /**
     * Integer of users.id
     * @var int
     */
    protected $userId;

    /**
     * Integer of teams.id
     * @var int
     */
    protected $teamId;

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

    /**
     * Set jti(JWT ID) of JWT Claim
     * @param string $jwtId
     *
     * @return JwtAuthentication
     */
    public function withJwtId(string $jwtId): self
    {
        $this->jwtId = $jwtId;
        return $this;
    }

    /**
     * Set iat(Issued At) of JWT Claim
     * @param GoalousDateTime $createdAt
     *
     * @return JwtAuthentication
     */
    public function withCreatedAt(GoalousDateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Set exp(Expiration Time) of JWT Claim
     * @param GoalousDateTime $expiredAt
     *
     * @return JwtAuthentication
     */
    public function withExpiredAt(GoalousDateTime $expiredAt): self
    {
        $this->expireAt = $expiredAt;
        return $this;
    }

    /**
     * Build the JWT token for user authentication
     * @return string
     */
    public function token(): string
    {
        return JWT::encode(
            $this->buildTokenPayload(),
            'token_secret_key',// TODO: move to config
            'HS256'// TODO: move to config? class static property?
        );
    }

    /**
     * Build JWT token payload for goalous authentication
     * @return array
     */
    private function buildTokenPayload(): array
    {
        if (is_null($this->jwtId)) {
            // TODO: change this to uuid4
            $this->jwtId = 'uuid_'.mt_rand(0, PHP_INT_MAX);
        }
        if (is_null($this->expireAt)) {
            // TODO: move expire period day to config
            $this->expireAt = GoalousDateTime::now()->addDays(14);
        }
        if (is_null($this->createdAt)) {
            $this->createdAt = GoalousDateTime::now();
        }
        return [
            'jti' => $this->jwtId,
            'exp' => $this->expireAt->getTimestamp(),
            'iat' => $this->createdAt->getTimestamp(),
            'goalous.com' => [
                'user_id' => $this->userId,
                'team_id' => $this->teamId,
            ],
        ];
    }

    /**
     * Decode the JWT token string and return JwtAuthentication instance
     * @param string $jwtToken
     *
     * @throws JwtException
     * @throws JwtSignatureException
     * @throws JwtExpiredException
     *
     * @return JwtAuthentication
     */
    public static function decode(string $jwtToken): self
    {
        $decodedJwtData = [];
        try {
            $decodedJwt = JWT::decode(
                $jwtToken,
                'token_secret_key',// TODO: move to config,
                ['HS256']// TODO: move to config? class static property?
            );
            $decodedJwtData = (array)$decodedJwt;
        } catch (SignatureInvalidException $e) {
            throw new JwtSignatureException($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new JwtExpiredException($e->getMessage());
        } catch (ExpiredException $e) {
            throw new JwtExpiredException($e->getMessage());
        } catch (UnexpectedValueException $e) {
            throw new JwtException($e->getMessage());
        }

        if (!isset($decodedJwtData[static::PAYLOAD_NAMESPACE]->user_id)) {
            throw new JwtException('user_id not found in payload');
        }
        if (!isset($decodedJwtData[static::PAYLOAD_NAMESPACE]->team_id)) {
            throw new JwtException('team_id not found in payload');
        }
        if (!isset($decodedJwtData['exp'])) {
            throw new JwtException('exp not found in payload');
        }
        if (!isset($decodedJwtData['jti'])) {
            throw new JwtException('jti not found in payload');
        }
        if (!isset($decodedJwtData['iat'])) {
            throw new JwtException('iat not found in payload');
        }
        $userId = $decodedJwtData[static::PAYLOAD_NAMESPACE]->user_id;
        $teamId = $decodedJwtData[static::PAYLOAD_NAMESPACE]->team_id;
        $expiredAt = GoalousDateTime::createFromTimestamp($decodedJwtData['exp']);
        $createdAt = GoalousDateTime::createFromTimestamp($decodedJwtData['iat']);
        $JwtId     = $decodedJwtData['jti'];

        return (new static($userId, $teamId))
            ->withCreatedAt($createdAt)
            ->withExpiredAt($expiredAt)
            ->withJwtId($JwtId);
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
