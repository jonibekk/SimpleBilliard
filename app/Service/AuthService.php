<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Util');
App::uses('Email', 'Model');
App::uses('JwtAuthentication', 'Lib/Jwt');

/**
 * Class for handling authentication
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/31
 * Time: 9:34
 */
class AuthService
{
    private $passwordHasher;

    public function __construct()
    {
        $this->passwordHasher = new SimplePasswordHasher(['hashType' => 'sha256']);
    }

    /**
     * Authenticate given email address with given password.
     *
     * @param string $username
     * @param string $password
     *
     * @return JwtAuthentication
     * @throws Exception
     */
    public function authenticateUser(string $username, string $password)
    {
        $Email = new Email();
        $User = new User();

        $findUserConditions = [
            'conditions' => [
                'Email.email'   => $username,
                'Email.del_flg' => false
            ],
            //            'table'      => 'emails',
            //            'alias'      => 'Email',
            'fields'     => 'user_id'
            //            'joins'      => [
            //                'type'       => 'LEFT',
            //                'table'      => 'users',
            //                'alias'      => 'User',
            //                'conditions' => 'Email.user_id = User.id'
            //            ]
        ];

        $userId = (int)$Email->find('first', $findUserConditions);
        $user = $User->getById($userId);

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
                throw new Exception("Unable to save SHA256 password");
            }
        } elseif (!$this->passwordHasher->check($password, $storedHashedPassword)) {
            // Normal case
            return null;
        }

        return AccessAuthenticator::publish($user['id'], $user['default_team_id'])->getJwtAuthentication();
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
     * @param string $inputPlanePassword
     * @param string $storedHashedPassword
     *
     * @return bool
     */
    private function _verifySha1Password(
        string $inputPlanePassword,
        string $storedHashedPassword
    ): bool {
        $passwordHasher = new SimplePasswordHasher(['hashType' => 'sha1']);
        $inputHashedPassword = $passwordHasher->hash($inputPlanePassword);
        if ($inputHashedPassword === $storedHashedPassword) {
            return true;
        }
        return false;
    }

    /**
     * Save new password as SHA256
     *
     * @param array  $userData
     * @param string $planePassword
     *
     * @return bool
     */
    private function _savePasswordAsSha256(
        array $userData,
        string $planePassword
    ): bool {
        $User = new User();
        $newHashedPassword = $this->passwordHasher->hash($planePassword);
        printf($newHashedPassword);

        try {
            $User->save([
                'id'       => $userData['id'],
                'password' => $newHashedPassword,
            ], false);
        } catch (Exception $e) {
            CakeLog::emergency(sprintf("Failed to save SHA256 password. errorMsg: %s, userData: %s, Trace: %s",
                $e->getMessage(),
                AppUtil::jsonOneLine($userData),
                AppUtil::jsonOneLine(Debugger::trace())
            ));
            return false;
        }
        return true;
    }
}