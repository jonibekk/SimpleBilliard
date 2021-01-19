<?php

App::uses('AuthorizedAccessInfo', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('AccessTokenClient', 'Lib/Cache/Redis/AccessToken');

use Goalous\Exception as GlException;

/**
 * Class AccessAuthenticator
 *
 * This class verify/create the authorization bearer
 *
 * @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication
 * ```php
 * App::uses('AccessAuthenticator', 'Lib/Auth');
 * try {
 *     // AccessAuthenticator::auth(string) method do
 *     //   1. Verify the JWT token
 *     //   2. Check JWT token in Redis
 *     //   3. Return user authentication info
 *     $authorizedAccessInfo = AccessAuthenticator::verify($authorizationBearer);
 * } catch (\Goalous\Exception\Auth\AuthOutOfTermException $e) {
 *     // If token has expired
 * } catch (\Goalous\Exception\Auth\AuthNotManagedException $e) {
 *     // If token has not managed in Redis
 * } catch (\Goalous\Exception\Auth\AuthFailedException $e) {
 *     // If failed on verify authorization bearer token
 * }
 * // Login verify succeed
 * $authorizedAccessInfo->getUserId(); // return users.id
 * $authorizedAccessInfo->getTeamId(); // return teams.id
 * $authorizedAccessInfo->token();     // return token string
 * ```
 *
 * ```php
 * App::uses('AccessAuthenticator', 'Lib/Auth');
 * // AccessAuthenticator::authorize(int, int) method do
 * //   1. Create new JWT token for user login
 * //   2. Save JWT token into Redis
 * //     @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication#APIv2Authentication-RediskeyofJWTtoken
 * //   3. Return user authentication info
 * $authorizedAccessInfo = AccessAuthenticator::publish($userId, $teamId);
 *
 * // Creating login token succeed
 * $authorizedAccessInfo->getUserId(); // return users.id
 * $authorizedAccessInfo->getTeamId(); // return teams.id
 * $authorizedAccessInfo->token();     // return token string
 * ```
 */
class AccessAuthenticator
{
    /**
     * Verify the access token
     *
     * @param string $authorizationBearer
     *
     * @throws GlException\Auth\AuthFailedException           Any reason of failed verifying token
     * @throws GlException\Auth\AuthNotManagedException Will throw this if token is not saved in Redis
     * @throws GlException\Auth\AuthOutOfTermException  Will throw this if token is expired or before enabled
     * @return AuthorizedAccessInfo
     */
    public static function verify(string $authorizationBearer): AuthorizedAccessInfo {

        try {
            $jwtAuth = JwtAuthentication::decode($authorizationBearer);

            // Check in the cache key is exist or not
            $cacheClient = new AccessTokenClient();
            $cacheKey = new AccessTokenKey($jwtAuth->getUserId(), $jwtAuth->getTeamId(), $jwtAuth->getJwtId());
            $cachedAuthorizedData = $cacheClient->read($cacheKey);
            if (is_null($cachedAuthorizedData)) {
                throw new GlException\Auth\AuthNotManagedException();
            }

            return new AuthorizedAccessInfo($jwtAuth);
        } catch (GlException\Auth\Jwt\JwtSignatureException $exception) {
            throw new GlException\Auth\AuthFailedException($exception->getMessage());
        } catch (GlException\Auth\Jwt\JwtOutOfTermException $exception) {
            throw new GlException\Auth\AuthOutOfTermException($exception->getMessage());
        } catch (GlException\Auth\Jwt\JwtException $exception) {
            throw new GlException\Auth\AuthFailedException($exception->getMessage());
        }
    }

    /**
     * Create new authentication token
     *
     * @param int $userId
     * @param int|null $teamId
     *
     * @throws GlException\Auth\AuthFailedException
     *
     * @return AuthorizedAccessInfo
     */
    public static function publish(int $userId, ?int $teamId): AuthorizedAccessInfo {
        $jwtAuth = new JwtAuthentication($userId, $teamId);

        // build token information
        $jwtAuth->token();

        // Store token into cache
        $cacheClient = new AccessTokenClient();
        $cacheKey = new AccessTokenKey($jwtAuth->getUserId(), $jwtAuth->getTeamId(), $jwtAuth->getJwtId());
        $cachedAuthorizedData = (new AccessTokenData())->withTimeToLive($jwtAuth->expireInSeconds());
        if (!$cacheClient->write($cacheKey, $cachedAuthorizedData)) {
            throw new GlException\Auth\AuthFailedException('failed to cache token');
        }

        return new AuthorizedAccessInfo($jwtAuth);
    }
}
