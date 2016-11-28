<?php
App::import('Service', 'AppService');
App::uses('MemberGroup', 'Model');
App::uses('Group', 'Model');

/**
 * This Class is GroupService
 * Created by PhpStorm.
 * User: bigplants
 * Date: 11/28/16
 * Time: 1:11 PM
 */
class GroupService extends AppService
{
    /**
     * ログインユーザがグループメンバーかどうか？
     *
     * @param $groupId
     *
     * @return bool
     */
    function isGroupMember($groupId): bool
    {
        /** @var MemberGroup $MemberGroup */
        $MemberGroup = ClassRegistry::init("MemberGroup");
        $myGroupList = $MemberGroup->getMyGroupList();
        if (empty($myGroupList)) {
            return false;
        }

        if (!array_key_exists($groupId, $myGroupList)) {
            return false;
        }
        return true;
    }

}
