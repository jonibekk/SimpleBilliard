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

    public function __construct(JwtAuthentication $jwtAuthentication)
    {
        $this->jwtAuthentication = $jwtAuthentication;
    }

    public function getUserId(): int
    {
        return $this->jwtAuthentication->getUserId();
    }

    public function getTeamId(): int
    {
        return $this->jwtAuthentication->getTeamId();
    }

    public function getJwtAuthentication(): JwtAuthentication
    {
        return $this->jwtAuthentication;
    }

    public function token(): string
    {
        return $this->getJwtAuthentication()->token();
    }
}
