<?php
App::uses('AccessTokenClient', 'Lib/Cache/Redis/AccessToken');

class AuthenticationSessionDataService
{
    public function read(string $userId, string $teamId, string $jwtId): AccessTokenData
    {
        $cacheClient = new AccessTokenClient();
        $cacheKey = new AccessTokenKey($userId, $teamId, $jwtId);
        return $cacheClient->read($cacheKey);
    }

    public function write(string $userId, string $teamId, string $jwtId, AccessTokenData $accessTokenData): bool
    {
        $cacheClient = new AccessTokenClient();
        $cacheKey = new AccessTokenKey($userId, $teamId, $jwtId);
        return $cacheClient->write($cacheKey, $accessTokenData);
    }
}
