<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Util');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('User', 'Model');
App::import('Service', 'AppService');
App::import('Model/Entity', 'UserEntity');

use Goalous\Exception as GlException;

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
     * Authenticate given email address with given password.
     *
     * @param string $email
     * @param string $password
     *
     * @throws GlException\Auth\AuthMismatchException When user's email+password does not match
     * @throws GlException\Auth\AuthFailedException Any reason failed authorize(including internal server error)
     *
     * @return JwtAuthentication Authentication token of the user. Will return null on failed login
     */
    public function authenticateUser(string $email, string $password)
    {
        /** @var .\Model\User $User */
        $User = ClassRegistry::init('User');

        $user = $User->findUserByEmail($email);

        if (empty($user)) {
            // email is not registered
            throw new GlException\Auth\AuthMismatchException('password and email does not match');
        }

        $storedHashedPassword = $user['password'];

        if ($this->_isSha1($storedHashedPassword)) {
            // SHA1 passwords are stored before payment release.
            // Ols passwords will be changed to sha256 when user change password
            if (!$this->_verifySha1Password($password, $storedHashedPassword)) {
                throw new GlException\Auth\AuthMismatchException('password and email does not match');
            }
            if (!$this->_savePasswordAsSha256($user, $password)) {
                throw new GlException\Auth\AuthFailedException('failed to save sha256');
            }
        } elseif (!$this->passwordHasher->check($password, $storedHashedPassword)) {
            throw new GlException\Auth\AuthMismatchException('password and email does not match');
        }

        try {
            return AccessAuthenticator::publish($user['id'], $user['default_team_id'])->getJwtAuthentication();
        } catch (\Throwable $e) {
            throw new GlException\Auth\AuthFailedException($e->getMessage());
        }
    }

    /**
     * Remove user's JWT from Redis cache during logout
     *
     * @param JwtAuthentication $jwt JWT Auth of the user
     *
     * @throws Exception
     * @return bool True on successful logout
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
     * Is password Sha1?
     * Old password (SHA1) if 40 bytes.
     *
     * @param string $hashedPassword
     *
     * @return bool
     */
    private function _isSha1(string $hashedPassword): bool
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
    private function _verifySha1Password(string $inputPlainPassword, string $storedHashedPassword): bool
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
    private function _savePasswordAsSha256(UserEntity $userData, string $plainPassword): bool
    {
        $User = new User();
        $newHashedPassword = $this->passwordHasher->hash($plainPassword);

        try {
            $User->save([
                'id'       => $userData['id'],
                'password' => $newHashedPassword,
            ], false);
        } catch (Exception $e) {
            GoalousLog::emergency(sprintf("Failed to save SHA256 password. errorMsg: %s, userData: %s, Trace: %s",
                $e->getMessage(),
                AppUtil::jsonOneLine($userData),
                AppUtil::jsonOneLine(Debugger::trace())
            ));
            return false;
        }
        return true;
    }


    /**
     * Recreate new jwt
     * use case: switch team
     *
     * @param JwtAuthentication $jwt: old jwt
     * @param int $teamId
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

}
