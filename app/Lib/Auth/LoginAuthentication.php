<?php

App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('User', 'Model');
App::uses('Team', 'Model');

/**
 * Class LoginAuthentication
 *
 * This class holding login information of user
 */
final class LoginAuthentication
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

    /**
     * Return users table data array
     * if user is not found this method return empty array
     *
     * @return array
     */
    public function getUser(): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        return $User->getById($this->getUserId());
    }

    public function getTeamId(): int
    {
        return $this->jwtAuthentication->getTeamId();
    }

    /**
     * Return teams table data array
     * if team is not found this method return empty array
     *
     * @return array
     */
    public function getTeam(): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team ');
        return $Team->getById($this->getTeamId());
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
