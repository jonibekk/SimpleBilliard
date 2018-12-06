<?php

App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('User', 'Model');
App::uses('Team', 'Model');

/**
 * Class AuthorizedAccessInfo
 *
 * This class holding login information of user
 */
final class AuthorizedAccessInfo
{
    /**
     * @var JwtAuthentication
     */
    private $jwtAuthentication;

    /**
     * AuthorizedAccessInfo constructor.
     *
     * @param JwtAuthentication $jwtAuthentication
     */
    public function __construct(JwtAuthentication $jwtAuthentication)
    {
        $this->jwtAuthentication = $jwtAuthentication;
    }

    /**
     * Return authorized users.id
     * @return int
     */
    public function getUserId(): int
    {
        return $this->jwtAuthentication->getUserId();
    }

    /**
     * Return authorized teams.id
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->jwtAuthentication->getTeamId();
    }

    /**
     * Return authorized env name
     * @return string
     */
    public function getEnvName(): string
    {
        return $this->jwtAuthentication->getEnvName();
    }

    /**
     * @return JwtAuthentication
     */
    public function getJwtAuthentication(): JwtAuthentication
    {
        return $this->jwtAuthentication;
    }

    /**
     * Return token string used for authentication
     * @return string
     */
    public function token(): string
    {
        return $this->getJwtAuthentication()->token();
    }
}
