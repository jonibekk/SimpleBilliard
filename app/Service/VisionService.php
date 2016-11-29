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
            $group_list = $MemberGroup->getGroupListNotExistsVision(false);
        } else {
            $group_list = $MemberGroup->getGroupListNotExistsVision(true);
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

    /**
     * グループビジョンリストページ用のデータの構築
     *
     * @param $teamId
     * @param $activeFlg
     *
     * @return array|null
     */
    function buildGroupVisionListForResponse($teamId, $activeFlg)
    {
        App::import('Service', 'GroupService');
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init('GroupService');
        $groupList = $GroupService->getAllGroupsWithMemberCount();
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $groupVisions = $GroupVision->getGroupVision($teamId, $activeFlg);

        foreach ($groupVisions as &$groupVision) {
            $data = $groupVision['GroupVision'];
            $data['photo_path'] = $upload->uploadUrl($data,
                'GroupVision.photo',
                ['style' => 'large']);
            $data['modified'] = $time->elapsedTime(h($data['modified']));
            if (isset($groupList[$data['group_id']]) === true) {
                $data['group_name'] = $groupList[$data['group_id']]['name'];
                $data['member_count'] = $groupList[$data['group_id']]['member_count'];
            }
            $groupVision['GroupVision'] = $data;
        }
        return $groupVisions;
    }

    /**
     * グループビジョン詳細ページ用のデータの構築
     *
     * @param $groupVisionId
     * @param $activeFlg
     *
     * @return array|null
     */
    function buildGroupVisionDetailForResponse($groupVisionId, $activeFlg)
    {
        App::import('Service', 'GroupService');
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init('GroupService');
        $groupList = $GroupService->getAllGroupsWithMemberCount();
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $data = $GroupVision->getGroupVisionDetail($groupVisionId, $activeFlg);

        $data['GroupVision']['photo_path'] = $upload->uploadUrl($data['GroupVision'], 'GroupVision.photo',
            ['style' => 'original']);
        $data['GroupVision']['modified'] = $time->elapsedTime(h($data['GroupVision']['modified']));
        if (isset($groupList[$data['GroupVision']['group_id']]) === true) {
            $data['GroupVision']['group_name'] = $groupList[$data['GroupVision']['group_id']]['name'];
            $data['GroupVision']['member_count'] = $groupList[$data['GroupVision']['group_id']]['member_count'];
        }
        return $data;
    }

    /**
     * チームビジョンリストページ用のデータの構築
     *
     * @param $teamId
     * @param $activeFlg
     *
     * @return array|mixed|null
     */
    function buildTeamVisionListForResponse($teamId, $activeFlg)
    {
        /** @var TeamVision $TeamVision */
        $TeamVision = ClassRegistry::init('TeamVision');
        $data = $TeamVision->getTeamVision($teamId, $activeFlg);

        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        foreach ($data as &$team) {
            $team['TeamVision']['photo_path'] = $upload->uploadUrl($team['TeamVision'], 'TeamVision.photo',
                ['style' => 'large']);
            $team['TeamVision']['modified'] = $time->elapsedTime(h($team['TeamVision']['modified']));
        }
        return $data;
    }

    /**
     * チームビジョン詳細ページ用のデータの構築
     *
     * @param $teamVisionId
     * @param $activeFlg
     *
     * @return array|null
     */
    function buildTeamVisionDetailForResponse($teamVisionId, $activeFlg)
    {
        /** @var TeamVision $TeamVision */
        $TeamVision = ClassRegistry::init('TeamVision');
        $data = $TeamVision->getTeamVisionDetail($teamVisionId, $activeFlg);

        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        $data['TeamVision']['photo_path'] = $upload->uploadUrl($data['TeamVision'], 'TeamVision.photo',
            ['style' => 'original']);
        $data['TeamVision']['modified'] = $time->elapsedTime(h($data['TeamVision']['modified']));

        return $data;

    }

}
