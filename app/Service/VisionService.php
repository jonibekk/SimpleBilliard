<?php
App::import('Service', 'AppService');
App::import('Service', 'GroupService');
App::uses('GroupVision', 'Model');
App::uses('TeamVision', 'Model');
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
            $groupList = $MemberGroup->findGroupListNotExistsVision(false);
        } else {
            $groupList = $MemberGroup->findGroupListNotExistsVision(true);
        }
        return $groupList;
    }

    /**
     * グループビジョンを編集できるのは、グループに所属してるユーザもしくはチーム管理者のみ
     *
     * @param int $groupVisionId
     *
     * @return bool
     */
    function hasPermissionToEdit(int $groupVisionId): bool
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        if ($TeamMember->isAdmin()) {
            return true;
        }
        if ($this->isGroupMemberByGroupVisionId($groupVisionId)) {
            return true;
        }
        return false;
    }

    /**
     * グループビジョンIDを元にログインユーザがグループに所属しているかのチェック
     *
     * @param int $groupVisionId
     *
     * @return bool
     */
    function isGroupMemberByGroupVisionId(int $groupVisionId): bool
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
     * グループビジョンが存在しているかのチェック
     *
     * @param int $groupVisionId
     *
     * @return bool
     */
    function existsGroupVision(int $groupVisionId): bool
    {
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init("GroupVision");
        return $GroupVision->exists($groupVisionId);
    }

    /**
     * グループビジョンリストページ用のデータの構築
     *
     * @param int  $teamId
     * @param bool $activeFlg
     *
     * @return array
     */
    function buildGroupVisionListForResponse(int $teamId, bool $activeFlg): array
    {
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init('GroupService');
        $groupList = $GroupService->findAllGroupsWithMemberCount();
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $groupVisions = $GroupVision->getGroupVision($teamId, $activeFlg);

        foreach ($groupVisions as &$groupVision) {
            $data = $groupVision['GroupVision'];
            $groupId = $data['group_id'];
            $data['photo_path'] = $upload->uploadUrl($data,
                'GroupVision.photo',
                ['style' => 'large']);
            $data['modified'] = $time->elapsedTime($data['modified']);
            if (isset($groupList[$groupId]) === true) {
                $data['group_name'] = $groupList[$groupId]['name'];
                $data['member_count'] = $groupList[$groupId]['member_count'];
            }
            $groupVision['GroupVision'] = $data;
        }
        return $groupVisions;
    }

    /**
     * グループビジョン詳細ページ用のデータの構築
     *
     * @param int  $groupVisionId
     * @param bool $activeFlg
     *
     * @return array
     */
    function buildGroupVisionDetailForResponse(int $groupVisionId, bool $activeFlg): array
    {
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init('GroupService');
        $groupList = $GroupService->findAllGroupsWithMemberCount();
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $data = $GroupVision->getGroupVisionDetail($groupVisionId, $activeFlg);

        $data['GroupVision']['photo_path'] = $upload->uploadUrl($data['GroupVision'], 'GroupVision.photo',
            ['style' => 'original']);
        $data['GroupVision']['modified'] = $time->elapsedTime($data['GroupVision']['modified']);
        if (isset($groupList[$data['GroupVision']['group_id']]) === true) {
            $data['GroupVision']['group_name'] = $groupList[$data['GroupVision']['group_id']]['name'];
            $data['GroupVision']['member_count'] = $groupList[$data['GroupVision']['group_id']]['member_count'];
        }
        return $data;
    }

    /**
     * チームビジョンリストページ用のデータの構築
     *
     * @param int  $teamId
     * @param bool $activeFlg
     *
     * @return array
     */
    function buildTeamVisionListForResponse(int $teamId, bool $activeFlg): array
    {
        /** @var TeamVision $TeamVision */
        $TeamVision = ClassRegistry::init('TeamVision');
        $data = $TeamVision->getTeamVision($teamId, $activeFlg);

        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        foreach ($data as &$team) {
            $team['TeamVision']['photo_path'] = $upload->uploadUrl($team['TeamVision'], 'TeamVision.photo',
                ['style' => 'large']);
            $team['TeamVision']['modified'] = $time->elapsedTime($team['TeamVision']['modified']);
        }
        return $data;
    }

    /**
     * チームビジョン詳細ページ用のデータの構築
     *
     * @param int  $teamVisionId
     * @param bool $activeFlg
     *
     * @return array
     */
    function buildTeamVisionDetailForResponse(int $teamVisionId, bool $activeFlg): array
    {
        /** @var TeamVision $TeamVision */
        $TeamVision = ClassRegistry::init('TeamVision');
        $data = $TeamVision->getTeamVisionDetail($teamVisionId, $activeFlg);

        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        $data['TeamVision']['photo_path'] = $upload->uploadUrl($data['TeamVision'], 'TeamVision.photo',
            ['style' => 'original']);
        $data['TeamVision']['modified'] = $time->elapsedTime($data['TeamVision']['modified']);

        return $data;

    }

}
