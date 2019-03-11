<?php
App::uses('TeamMember', 'Model');
App::import('Service', 'AuthService');

trait AuthTrait
{
    /**
     * Reset auth when switch team
     * @param int $userId
     * @param int $teamId
     * @param JwtAuthentication $jwt
     * @return JwtAuthentication
     */
    protected function resetAuth(int $userId, int $teamId, JwtAuthentication $jwt): JwtAuthentication
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $TeamMember->updateLastLogin($teamId, $userId);
        $this->Session->write('current_team_id', $teamId);
        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init("AuthService");
        $newJwt = $AuthService->recreateJwt($jwt, $teamId);
        return $newJwt;
    }
}
