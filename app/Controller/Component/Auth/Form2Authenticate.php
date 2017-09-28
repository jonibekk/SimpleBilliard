<?php /**
 * Controller/Component/Auth/Form2Authenticate.php
 */
App::uses('FormAuthenticate', 'Controller/Component/Auth');

class Form2Authenticate extends FormAuthenticate
{

    /**
     * @param array|string $username
     * @param Mixed        $password The password, only use if passing as $conditions = 'username'.
     *
     * @internal param Mixed $conditions The username/identifier, or an array of find conditions.
     * @return Mixed Either false on failure, or an array of user data.
     */
    protected function _findUser($username, $password = null)
    {
        $userModel = $this->settings['userModel'];
        /** @var User $User */
        $User = ClassRegistry::init($userModel);

        list(, $model) = pluginSplit($userModel);
        $fields = $this->settings['fields'];

        if (is_array($username)) {
            $conditions = $username;
        } else {
            //TODO ハードコーディングでPrimaryEmailモデルを指定、もし複数アドレスを許可する場合は書き換える必要あり。
            $conditions = array(
                'PrimaryEmail' . '.' . $fields['username'] => $username,
                'PrimaryEmail.del_flg'                     => false,
            );
        }

        if (!empty($this->settings['scope'])) {
            $conditions = array_merge($conditions, $this->settings['scope']);
        }
        $result = $User->find('first', array(
            'conditions' => $conditions,
            'recursive'  => $this->settings['recursive'],
            'contain'    => $this->settings['contain'],
        ));
        if (empty($result[$model])) {
            $this->passwordHasher()->hash($password);
            return false;
        }

        $user = $result[$model];
        if ($password !== null) {
            $storedHashedPassword = $user[$fields['password']];
            if ($this->_isSha1($storedHashedPassword)) {
                // SHA1 passwords are stored before payment release.
                // Ols passwords will be changed to sha256 when user change password
                if (!$this->_verifySha1Password($password, $storedHashedPassword)) {
                    return false;
                }
                if (!$this->_savePasswordAsSha256($user, $password)) {
                    return false;
                }
            } elseif (!$this->passwordHasher()->check($password, $storedHashedPassword)) {
                // Normal case
                return false;
            }
            unset($user[$fields['password']]);
        }

        unset($result[$model]);
        return array_merge($user, $result);
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
     * @param string $inputPlanePassword
     * @param string $storedHashedPassword
     *
     * @return bool
     */
    private function _verifySha1Password(string $inputPlanePassword, string $storedHashedPassword): bool
    {
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
    private function _savePasswordAsSha256(array $userData, string $planePassword): bool
    {
        $userModel = $this->settings['userModel'];
        /** @var User $User */
        $User = ClassRegistry::init($userModel);
        $newHashedPassword = $this->passwordHasher()->hash($planePassword);
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
