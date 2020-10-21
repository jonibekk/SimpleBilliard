<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\ExpiredException;
use \Ramsey\Uuid\Uuid;
use Goalous\Exception as GlException;

App::uses('GoalousDateTime', 'DateTime');


/**
 * JWT token class for Goalous API authentication
 * @see https://confluence.goalous.com/x/kYTT#APIv2Authentication-WhatshouldwesetonPayload?
 *
 * ```php
 * App::uses('JwtAuthentication', 'Lib/Jwt');
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
 * } catch (\Goalous\Exception\JwtSignatureException $exception) {
 *      // When invalid signature
 * } catch (\Goalous\Exception\JwtOutOfTermException $exception) {
 *      // When token is expired
 * } catch (\Goalous\Exception\JwtException $exception) {
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
     * We have a 64bit cpu on current environment (2018-04-20)
     * $ uname -a
     * Linux goalous-dev-app2 3.13.0-144-generic #193-Ubuntu SMP Thu Mar 15 17:03:53 UTC 2018 x86_64 x86_64 x86_64 GNU/Linux
     *
     * And SHA512 having better performance than SHA256 by comparing command below
     * $ openssl speed sha256 sha512
     *
     * @see https://confluence.goalous.com/x/kYTT#APIv2Authentication-JWTalgorythm
     */
    const JWT_ALGORITHM = 'HS512';

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
     * Environment target of token is issued for
     * @var string
     */
    protected $env;

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
     * @param int|null $teamId teams.id to login
     */
    public function __construct(int $userId, ?int $teamId)
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
     * Set env name to payload
     * @param string $env
     *
     * @return JwtAuthentication
     */
    public function withEnvName(string $env): self
    {
        $this->env = $env;
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
            JWT_TOKEN_SECRET_KEY_AUTH,
            static::JWT_ALGORITHM
        );
    }

    /**
     * Get token expire period by minute from CakePHP Config
     * @return int
     */
    private function getExpirePeriodMinute(): int
    {
        return Configure::read('Session')['timeout'];
    }

    /**
     * Build JWT token payload for goalous authentication
     * @return array
     */
    private function buildTokenPayload(): array
    {
        if (is_null($this->jwtId)) {
            // see here for document https://github.com/ramsey/uuid#examples
            $this->jwtId = Uuid::uuid4();
        }
        if (is_null($this->expireAt)) {
            $this->expireAt = GoalousDateTime::now()->addMinutes($this->getExpirePeriodMinute());
        }
        if (is_null($this->createdAt)) {
            $this->createdAt = GoalousDateTime::now();
        }
        if (is_null($this->env)) {
            $this->env = ENV_NAME;
        }
        return [
            'jti' => $this->jwtId,
            'exp' => $this->expireAt->getTimestamp(),
            'iat' => $this->createdAt->getTimestamp(),
            self::PAYLOAD_NAMESPACE => [
                'env'     => $this->env,
                'user_id' => $this->userId,
                'team_id' => $this->teamId,
            ],
        ];
    }

    /**
     * Decode the JWT token string and return JwtAuthentication instance
     *
     * @param string $jwtToken
     *
     * @throws GlException\Auth\Jwt\JwtException
     * @throws GlException\Auth\Jwt\JwtSignatureException
     * @throws GlException\Auth\Jwt\JwtOutOfTermException
     * @return JwtAuthentication
     */
    public static function decode(string $jwtToken): self
    {
        try {
            $decodedJwt = JWT::decode(
                $jwtToken,
                JWT_TOKEN_SECRET_KEY_AUTH,
                [static::JWT_ALGORITHM]
            );
        } catch (SignatureInvalidException $e) {
            throw new GlException\Auth\Jwt\JwtSignatureException($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new GlException\Auth\Jwt\JwtOutOfTermException($e->getMessage());
        } catch (ExpiredException $e) {
            throw new GlException\Auth\Jwt\JwtOutOfTermException($e->getMessage());
        } catch (\DomainException $e) {
            throw new GlException\Auth\Jwt\JwtSignatureException($e->getMessage());
        } catch (\Throwable $e) {
            throw new GlException\Auth\Jwt\JwtException($e->getMessage());
        }

        if (!isset($decodedJwt->{static::PAYLOAD_NAMESPACE}->env)) {
            throw new GlException\Auth\Jwt\JwtException('env not found in payload');
        }
        $env = $decodedJwt->{static::PAYLOAD_NAMESPACE}->env;
        if ($env !== ENV_NAME) {
            throw new GlException\Auth\Jwt\JwtException('env has difference between this env and token payload');
        }
        if (!isset($decodedJwt->{static::PAYLOAD_NAMESPACE}->user_id)) {
            throw new GlException\Auth\Jwt\JwtException('user_id not found in payload');
        }
        if (!isset($decodedJwt->{static::PAYLOAD_NAMESPACE}->team_id)) {
            throw new GlException\Auth\Jwt\JwtException('team_id not found in payload');
        }
        if (!isset($decodedJwt->exp)) {
            throw new GlException\Auth\Jwt\JwtException('exp not found in payload');
        }
        if (!isset($decodedJwt->jti)) {
            throw new GlException\Auth\Jwt\JwtException('jti not found in payload');
        }
        if (!isset($decodedJwt->iat)) {
            throw new GlException\Auth\Jwt\JwtException('iat not found in payload');
        }
        $userId = $decodedJwt->{static::PAYLOAD_NAMESPACE}->user_id;
        $teamId = $decodedJwt->{static::PAYLOAD_NAMESPACE}->team_id;
        $expiredAt = GoalousDateTime::createFromTimestamp($decodedJwt->exp);
        $createdAt = GoalousDateTime::createFromTimestamp($decodedJwt->iat);
        $JwtId     = $decodedJwt->jti;

        return (new static($userId, $teamId))
            ->withEnvName($env)
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
     * @return int|null
     */
    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    /**
     * return env name this token is used for
     * @return string
     */
    public function getEnvName(): string
    {
        return $this->env;
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
     * Return remaining seconds of this token to expires.
     * @return int
     */
    public function expireInSeconds(): int
    {
        return $this->expireAt()->diffInSeconds(GoalousDateTime::now());
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
