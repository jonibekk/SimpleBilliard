<?php
App::import('Service', 'AppService');
App::uses('User', 'Model');

/**
 * Class UserService
 */
class UserService extends AppService
{
    /**
     * @param array  $userIds   e.g. [1,2,3]
     * @param string $delimiter
     * @param string $fieldName it should be included in user profile fields.
     *
     * @return string
     */
    function getUserNamesAsString(array $userIds, string $delimiter = ', ', string $fieldName = "display_first_name")
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        $users = $User->findProfilesByIds($userIds);
        $userNames = Hash::extract($users, "{n}.$fieldName");
        $ret = implode($delimiter, $userNames);
        return $ret;
    }

}
