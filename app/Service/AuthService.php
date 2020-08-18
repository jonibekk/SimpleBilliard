<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Util');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('User', 'Model');
App::uses('Team', 'Model');
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::import('Model/Entity', 'UserEntity');
App::import('Lib/DataExtender', 'MeExtender');
App::import('Service', 'TwoFAService');
App::import('Lib/Cache/Redis/AccessToken', 'AccessTokenClient');
App::import('Lib/Cache/Redis/TwoFAToken', 'TwoFATokenData');
App::import('Lib/Cache/Redis/TwoFAToken', 'TwoFATokenKey');
App::import('Lib/Cache/Redis/TwoFAToken', 'TwoFATokenClient');

use Goalous\Exception as GlException;
use Goalous\Enum as Enum;

/**
 * Class for handling authentication
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/31
 * Time: 9:34
 */
class AuthService extends AppService
{
    private $passwordHasher;

    public function __construct()
    {
        parent::__construct();
        $this->passwordHasher = new SimplePasswordHasher(['hashType' => 'sha256']);
    }

    /**
     * Verify password to an user.
     *
     * @param int    $userId
     * @param string $password
     *
     * @return bool Whether password matches
     *
     * @throws GlException\Auth\AuthFailedException Any reason failed authorize(including internal server error)
     */
    public function verifyPassword(int $userId, string $password): bool
    {
        /** @var .\Model\User $User */
        $User = ClassRegistry::init('User');
        /** @var UserEntity $user */
        $user = $User->getEntity($userId);

        $storedHashedPassword = $user['password'];

        if ($this->isSha1($storedHashedPassword)) {
            // SHA1 passwords are stored before payment release.
            // Ols passwords will be changed to sha256 when user change password
            if (!$this->verifySha1Password($password, $storedHashedPassword)) {
                return false;
            }
            if (!$this->savePasswordAsSha256($user, $password)) {
                throw new GlException\Auth\AuthFailedException('failed to save sha256');
            }
        } elseif (!$this->passwordHasher->check($password, $storedHashedPassword)) {
            return false;
        }

        return true;
    }

    /**
     * Remove user's JWT from Redis cache during logout
     *
     * @param JwtAuthentication $jwt JWT Auth of the user
     *
     * @return bool True on successful logout
     * @throws Exception
     */
    public function invalidateUser(JwtAuthentication $jwt): bool
    {
        $jwtClient = new AccessTokenClient();

        $userId = $jwt->getUserId();
        $teamId = $jwt->getTeamId();
        $jwtId = $jwt->getJwtId() ?? '';

        if (empty($userId) || empty($teamId) || empty($jwtId)) {
            return false;
        }

        $jwtKey = new AccessTokenKey($userId, $teamId, $jwtId);
        $jwtClient->del($jwtKey);

        return true;
    }

    /**
     * Create information for login method
     *
     * @param string $email User email address
     *
     * @return array
     */
    public function createLoginRequest(string $email): array
    {
        /** @var .\Model\User $User */
        $User = ClassRegistry::init('User');

        $user = $User->findUserByEmail($email);

        if (empty($user)) {
            // email is not registered
            throw new GlException\Auth\AuthUserNotFoundException('Email does not exist');
        }

        $requestData = ["email" => $email];

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');
        $UserService->updateDefaultTeamIfInvalid($user['id']);

        $user = $User->getById($user['id']);

        $defaultTeamId = $user['default_team_id'];

        if (!empty($defaultTeamId)) {
            $requestData['default_team'] = $this->createTeamData($defaultTeamId);
        }

        $teamLoginMethod = $this->getLoginMethod($defaultTeamId);

        return am($requestData, $teamLoginMethod);
    }

    /**
     * Create login response for password login
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function authenticateWithPassword(string $email, string $password): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        $user = $User->findUserByEmail($email);

        if (empty($user)) {
            // email is not registered
            throw new GlException\Auth\AuthUserNotFoundException('Email does not exist');
        }

        if (!$this->verifyPassword($user['id'], $password)) {
            throw new GlException\Auth\AuthMismatchException('Authentication information does not match');
        }

        $userId = $user['id'];
        $defaultTeamId = $user['default_team_id'];

        if ($user->has2FA()) {
            return [
                "message" => Enum\Auth\Status::REQUEST_2FA,
                "data"    => [
                    "auth_hash" => $this->createAuthHash($userId, $defaultTeamId)
                ]
            ];
        }

        return [
            "message" => Enum\Auth\Status::OK,
            "data"    => $this->createAuthResponseData($userId, $defaultTeamId)
        ];
    }

    /**
     * Perform authentication using 2fa. Assuming that user has authenticated using password
     *
     * @param string $authHash           Hash generated when user authenticated with password
     * @param string $twoFACode
     * @param bool   $useRecoveryCodeFlg Whether recovery code is used instead of totp code
     *
     * @return array
     */
    public function authenticateWith2FA(string $authHash, string $twoFACode, bool $useRecoveryCodeFlg = false)
    {
        $tokenData = $this->getAuthHashInfo($authHash);

        /** @var TwoFAService $TwoFAService */
        $TwoFAService = ClassRegistry::init('TwoFAService');

        if ($useRecoveryCodeFlg) {
            if (!$TwoFAService->useBackupCode($tokenData->getUserId(), $twoFACode)) {
                throw new GlException\Auth\Auth2FAInvalidBackupCodeException("Invalid 2fa backup code.");
            }
        } else {
            if (!$TwoFAService->verifyCode($tokenData->getUserId(), $twoFACode)) {
                throw new GlException\Auth\Auth2FAMismatchException("Wrong 2fa token.");
            }
        }

        $this->removeAuthHash($authHash);

        return [
            "message" => Enum\Auth\Status::OK,
            "data"    => $this->createAuthResponseData($tokenData->getUserId(), $tokenData->getTeamId())
        ];
    }

    /**
     * Is password Sha1?
     * Old password (SHA1) if 40 bytes.
     *
     * @param string $hashedPassword
     *
     * @return bool
     */
    private function isSha1(string $hashedPassword): bool
    {
        return strlen($hashedPassword) == 40;
    }

    /**
     * SHA1 password verification
     *
     * @param string $inputPlainPassword
     * @param string $storedHashedPassword
     *
     * @return bool
     */
    private function verifySha1Password(string $inputPlainPassword, string $storedHashedPassword): bool
    {
        $passwordHasher = new SimplePasswordHasher(['hashType' => 'sha1']);
        $inputHashedPassword = $passwordHasher->hash($inputPlainPassword);
        if ($inputHashedPassword === $storedHashedPassword) {
            return true;
        }
        return false;
    }

    /**
     * Save new password as SHA256
     *
     * @param UserEntity $userData
     * @param string     $plainPassword
     *
     * @return bool
     */
    private function savePasswordAsSha256(UserEntity $userData, string $plainPassword): bool
    {
        $User = new User();
        $newHashedPassword = $this->passwordHasher->hash($plainPassword);

        try {
            $User->save(
                [
                    'id'       => $userData['id'],
                    'password' => $newHashedPassword,
                ],
                false
            );
        } catch (Exception $e) {
            GoalousLog::emergency(
                sprintf(
                    "Failed to save SHA256 password. errorMsg: %s, userData: %s, Trace: %s",
                    $e->getMessage(),
                    AppUtil::jsonOneLine($userData->toArray()),
                    AppUtil::jsonOneLine(Debugger::trace())
                )
            );
            return false;
        }
        return true;
    }

    /**
     * Create login method information
     *
     * @param int|null $teamId
     *
     * @return array
     */
    private function getLoginMethod(?int $teamId): array
    {
        //TODO read from DB. For now, it's hard-coded for password

        $loginMethod = "";

        switch ($loginMethod) {
            case Enum\Auth\Method::PASSWORD:
            default:
                return [
                    "auth_method"  => Enum\Auth\Method::PASSWORD,
                    "auth_content" => ""
                ];
        }
    }

    /**
     * Create array for default team information during login
     *
     * @param int $teamId
     *
     * @return array
     */
    private function createTeamData(int $teamId): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $team = $Team->getById($teamId);

        return [
            "name" => $team['name']
        ];
    }


    /**
     * Recreate new jwt
     * use case: switch team
     *
     * @param JwtAuthentication $jwt : old jwt
     * @param int               $teamId
     *
     * @return JwtAuthentication new jwt
     */
    public function recreateJwt(JwtAuthentication $jwt, int $teamId): JwtAuthentication
    {
        try {
            $userId = $jwt->getUserId();
            // Del old jwt
            $this->invalidateUser($jwt);
            // Recreate with switched team id
            return AccessAuthenticator::publish($userId, $teamId)->getJwtAuthentication();
        } catch (\Throwable $e) {
            throw new GlException\Auth\AuthFailedException($e->getMessage());
        }
    }

    /**
     * Get user information
     *
     * @param int      $userId
     * @param int|null $teamId
     *
     * @return array
     */
    public function getUserInfo(int $userId, ?int $teamId): array
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');

        if (empty($teamId)) {
            return $UserService->getMinimum($userId, true);
        }

        $req = new UserResourceRequest($userId, $teamId, true);
        return $UserService->get($req, [MeExtender::EXTEND_ALL]);
    }

    /**
     * Create successful login response, containing user personal information and JWT token.
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return array
     */
    private function createAuthResponseData(int $userId, ?int $teamId): array
    {
        try {
            //TODO make sure null team works
            $userInfo = $this->getUserInfo($userId, $teamId);
            $jwt = AccessAuthenticator::publish($userId, $teamId)->getJwtAuthentication();

            return [
                "me"    => $userInfo,
                "token" => $jwt->token()
            ];
        } catch (\Throwable $e) {
            throw new GlException\Auth\AuthFailedException($e->getMessage());
        }
    }

    /**
     * Create unique auth hash for 2fa login and store it in Redis
     *
     * @param int      $userId
     * @param int|null $teamId
     *
     * @return string
     */
    private function createAuthHash(int $userId, ?int $teamId): string
    {
        //FNV-1a has very low collision chance with excellent performance
        $hash = hash("fnv1a64", $userId . $teamId . uniqid());

        $tokenKey = new TwoFATokenKey($hash);
        $tokenData = new TwoFATokenData($userId, $teamId);
        $tokenClient = new TwoFATokenClient();

        if (!$tokenClient->write($tokenKey, $tokenData)) {
            throw new GlException\Auth\AuthFailedException("Failed to save 2fa auth hash token");
        }

        return $hash;
    }

    /**
     * Get cached auth hash information for 2fa login
     *
     * @param string $authHash
     *
     * @return TwoFATokenData
     */
    private function getAuthHashInfo(string $authHash): TwoFATokenData
    {
        $tokenKey = new TwoFATokenKey($authHash);
        $tokenClient = new TwoFATokenClient();

        $tokenData = $tokenClient->read($tokenKey);

        if (empty($tokenData)) {
            throw new GlException\Auth\AuthUserNotFoundException("Missing 2fa auth hash token.");
        }

        return $tokenData;
    }

    /**
     * Remove cached auth hash after successful login
     *
     * @param string $authHash
     */
    private function removeAuthHash(string $authHash)
    {
        $tokenKey = new TwoFATokenKey($authHash);
        $tokenClient = new TwoFATokenClient();

        $tokenClient->del($tokenKey);
    }
}
