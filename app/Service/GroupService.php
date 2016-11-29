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

    /**
     * 全てのグループをメンバー数付きで返す
     *
     * @return array
     */
    function getAllGroupsWithMemberCount()
    {
        /** @var Group $Group */
        $Group = ClassRegistry::init("Group");
        $allGroups = $Group->getAllGroupWithMemberIds();
        foreach ($allGroups as &$group) {
            $group['Group']['member_count'] = count($group['MemberGroup']);
        }
        $ret = Hash::combine($allGroups, '{n}.Group.id', '{n}.Group');
        return $ret;
    }

}
