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
        list(, $model) = pluginSplit($userModel);
        $fields = $this->settings['fields'];

        if (is_array($username)) {
            $conditions = $username;
        }
        else {
            //TODO ハードコーディングでPrimaryEmailモデルを指定、もし複数アドレスを許可する場合は書き換える必要あり。
            $conditions = array(
                'PrimaryEmail' . '.' . $fields['username'] => $username,
            );
        }

        if (!empty($this->settings['scope'])) {
            $conditions = array_merge($conditions, $this->settings['scope']);
        }
        $result = ClassRegistry::init($userModel)->find('first', array(
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
            if (!$this->passwordHasher()->check($password, $user[$fields['password']])) {
                return false;
            }
            unset($user[$fields['password']]);
        }

        unset($result[$model]);
        return array_merge($user, $result);
    }
}