<?php

App::uses('LoginAuthentication', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('AuthenticationException', 'Lib/Auth/Exception');
App::uses('AuthenticationOutOfTermException', 'Lib/Auth/Exception');
App::uses('AuthenticationNotManagedException', 'Lib/Auth/Exception');

/**
 * Class LoginAuthenticator
 *
 * This class verify/create the authorization bearer
 *
 * @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication
 * ```php
 * App::uses('LoginAuthenticator', 'Lib/Auth');
 * try {
 *     // LoginAuthenticator::auth(string) method do
 *     //   1. Verify the JWT token
 *     //   2. Check JWT token in Redis
 *     //   3. Return user authentication info
 *     $loginAuthentication = LoginAuthenticator::verify($authorizationBearer);
 * } catch (AuthenticationOutOfTermException $e) {
 *     // If token has expired
 * } catch (AuthenticationNotManagedException $e) {
 *     // If token has not managed in Redis
 * } catch (AuthenticationException $e) {
 *     // If failed on verify authorization bearer token
 * }
 * // Login verify succeed
 * $loginAuthentication->getUserId(); // return users.id
 * $loginAuthentication->getUser();   // return user data array
 * $loginAuthentication->getTeamId(); // return teams.id
 * $loginAuthentication->getTeam();   // return team data array
 * $loginAuthentication->token();     // return token string
 * ```
 *
 * ```php
 * App::uses('LoginAuthenticator', 'Lib/Auth');
 * // LoginAuthenticator::authorize(int, int) method do
 * //   1. Create new JWT token for user login
 * //   2. Save JWT token into Redis
 * //     @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication#APIv2Authentication-RediskeyofJWTtoken
 * //   3. Return user authentication info
 * $loginAuthentication = LoginAuthenticator::publish($userId, $teamId);
 *
 * // Creating login token succeed
 * $loginAuthentication->getUserId(); // return users.id
 * $loginAuthentication->getUser();   // return user data array
 * $loginAuthentication->getTeamId(); // return teams.id
 * $loginAuthentication->getTeam();   // return team data array
 * $loginAuthentication->token();     // return token string
 * ```
 */
class LoginAuthenticator
{
    /**
     * Verify the access token
     *
     * @param string $authorizationBearer
     *
     * @throws AuthenticationException           Any reason of failed verifying token
     * @throws AuthenticationNotManagedException Will throw this if token is not saved in Redis
     * @throws AuthenticationOutOfTermException  Will throw this if token is expired or before enabled
     * @return LoginAuthentication
     */
    public static function verify(string $authorizationBearer): LoginAuthentication {
        try {
            $jwtAuth = JwtAuthentication::decode($authorizationBearer);
            $token = $jwtAuth->token();
            if ($ifTokenIsNotInRedis = false) {
                // TODO: Check $token in Redis
                // Write process after flexible Redis write/read class is created
                throw new AuthenticationNotManagedException();
            }
            return new LoginAuthentication($jwtAuth);
        } catch (JwtSignatureException $exception) {
            throw new AuthenticationException($exception->getMessage());
        } catch (JwtOutOfTermException $exception) {
            throw new AuthenticationOutOfTermException($exception->getMessage());
        } catch (JwtException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    /**
     * Create new authentication token
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return LoginAuthentication
     */
    public static function publish(int $userId, int $teamId): LoginAuthentication {
        $jwtAuthentication = new JwtAuthentication($userId, $teamId);
        $newToken = $jwtAuthentication->token();
        // TODO: save $newToken into Redis
        // Write process after flexible Redis write/read class is created
        return new LoginAuthentication(new JwtAuthentication($userId, $teamId));
    }
}