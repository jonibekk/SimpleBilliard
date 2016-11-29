<?php
App::import('Service', 'AppService');
App::import('Service', 'GroupService');
App::uses('GroupVision', 'Model');
App::uses('MemberGroup', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class VisionService
 */
class VisionService extends AppService
{
    /**
     * Visionを追加できるグループのリストを取得
     * # 対象グループの条件
     * - チーム管理者の場合はアクティブなvisionが存在しない全てのグループ。
     * - チーム管理者以外の場合は自分が所属しているグループでかつアクティブなvisionが存在しないグループ。
     *
     * @return array|null
     */
    function getGroupListAddableVision()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var MemberGroup $MemberGroup */
        $MemberGroup = ClassRegistry::init('MemberGroup');

        if ($TeamMember->isAdmin()) {
            $group_list = $MemberGroup->getMyGroupListNotExistsVision(false);
        } else {
            $group_list = $MemberGroup->getMyGroupListNotExistsVision(true);
        }
        return $group_list;
    }

    /**
     * グループビジョンを編集できるのは、グループに所属してるユーザもしくはチーム管理者のみ
     *
     * @param $groupVisionId
     *
     * @return bool
     */
    function hasPermissionToEdit($groupVisionId): bool
    {
        $isGroupMember = $this->isGroupMemberByGroupVisionId($groupVisionId);
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $isAdmin = $TeamMember->isAdmin();

        if ($isGroupMember || $isAdmin) {
            return true;
        }

        return false;
    }

    /**
     * グループビジョンIDを元にログインユーザがグループに所属しているかのチェック
     *
     * @param $groupVisionId
     *
     * @return bool
     */
    function isGroupMemberByGroupVisionId($groupVisionId): bool
    {
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init("GroupVision");
        $groupVision = Hash::get($GroupVision->findById($groupVisionId), 'GroupVision');
        if (empty($groupVision)) {
            return false;
        }
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init("GroupService");
        return $GroupService->isGroupMember($groupVision['group_id']);
    }

    /**
     * @param $group_vision_id
     *
     * @return bool
     */
    function isExistsGroupVision($group_vision_id): bool
    {
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init("GroupVision");
        return (bool)$GroupVision->exists($group_vision_id);
    }
}
