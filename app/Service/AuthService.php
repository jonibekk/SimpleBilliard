<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Util');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('User', 'Model');
App::import('Service', 'AppService');

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
     * @param string $username
     * @param string $password
     *
     * @return JwtAuthentication Authentication token of the user. Will return null on failed login
     */
    public function authenticateUser(string $username, string $password)
    {
        /** @var .\Model\User $User */
        $User = ClassRegistry::init('User');

        $user = $User->findUserByEmail($username);

        if (empty ($user)) {
            return null;
        }

        $storedHashedPassword = $user['password'];

        if ($this->_isSha1($storedHashedPassword)) {
            // SHA1 passwords are stored before payment release.
            // Ols passwords will be changed to sha256 when user change password
            if (!$this->_verifySha1Password($password, $storedHashedPassword)) {
                return null;
            }
            if (!$this->_savePasswordAsSha256($user, $password)) {
                return null;
            }
        } elseif (!$this->passwordHasher->check($password, $storedHashedPassword)) {
            return null;
        }

        return AccessAuthenticator::publish($user['id'], $user['default_team_id'])->getJwtAuthentication();
    }

    /**
     * Remove user's JWT from Redis cache during logout
     *
     * @param string $token JWT token of the user
     *
     * @throws Exception
     */
    public function invalidateUser(string $token)
    {
        $jwt = JwtAuthentication::decode($token);

        if (empty($jwt)) {
            throw new AuthenticationException();
        }

        $jwtClient = new AccessTokenClient();
        $jwtKey = new AccessTokenKey($jwt->getUserId(), $jwt->getTeamId(), $jwt->getJwtId());
        $jwtClient->del($jwtKey);
    }

    /**
     * Is password Sha1?
     * Old password (SHA1) if 40 bytes.
     *
     * @param string $hashedPassword
     *
     * @return bool
     */
    private function _isSha1(
        string $hashedPassword
    ): bool {
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
    private function _verifySha1Password(
        string $inputPlainPassword,
        string $storedHashedPassword
    ): bool {
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
     * @param array  $userData
     * @param string $plainPassword
     *
     * @return bool
     */
    private function _savePasswordAsSha256(
        array $userData,
        string $plainPassword
    ): bool {
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
}