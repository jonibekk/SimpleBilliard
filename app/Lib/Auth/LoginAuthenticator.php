<?php

App::uses('LoginAuthentication', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');

/**
 * Class LoginAuthenticator
 *
 * This class verify/create the authorization bearer
 *
 * @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication
 * ```php
 * App::uses('LoginAuthenticator', 'Lib/Auth');
 * try {
 *     // LoginAuthenticator::auth() method do
 *     //   1. Verify the JWT token
 *     //   2. Check JWT token in Redis
 *     //   3. Return user authentication info
 *     $loginAuthentication = LoginAuthenticator::auth($authorizationBearer);
 * } catch ($e) {
 *
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
 * try {
 *     // LoginAuthenticator::authorize() method do
 *     //   1. Create new JWT token for login
 *     //   2. Save JWT token into Redis
 *     //     @see https://confluence.goalous.com/display/GOAL/API+v2+Authentication#APIv2Authentication-RediskeyofJWTtoken
 *     //   3. Return user authentication info
 *     $loginAuthentication = LoginAuthenticator::authorize($userId, $teamId);
 * } catch ($e) {
 * }
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
     * @param string $authorizationBearer
     *
     * @return LoginAuthentication
     */
    public static function auth(string $authorizationBearer): LoginAuthentication {
        try {
            $jwtAuth = JwtAuthentication::decode($authorizationBearer);
            $token = $jwtAuth->token();
            // TODO: Check $token in Redis
            return new LoginAuthentication($jwtAuth);
//        } catch (JwtSignatureException $exception) {
//            // When invalid signature :TODO
//        } catch (JwtExpiredException $exception) {
//            // When token is expired :TODO
//        } catch (JwtException $exception) {
//            // When something other is invalid :TODO
        } catch (\Throwable $e) {
            GoalousLog::error($e->getMessage());
            GoalousLog::error($e->getTraceAsString());
        }
        // TODO:
        throw new RuntimeException('Could not authorize and catch exception in any case.');
    }

    /**
     * @param int $userId
     * @param int $teamId
     *
     * @return LoginAuthentication
     */
    public static function authorize(int $userId, int $teamId): LoginAuthentication {
        $jwtAuthentication = new JwtAuthentication($userId, $teamId);
        $newToken = $jwtAuthentication->token();
        // TODO: save $newToken into Redis
        return new LoginAuthentication(new JwtAuthentication($userId, $teamId));
    }
}