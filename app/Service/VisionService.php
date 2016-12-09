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
     * 全グループのキャッシュ用
     *
     * @var array
     */
    private $allGroups = [];

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
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $groupVisions = $GroupVision->findGroupVisions($teamId, $activeFlg);

        foreach ($groupVisions as &$groupVision) {
            $groupVision['GroupVision'] = $this->extendGroupVision($groupVision['GroupVision'], 'large');
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
        /** @var GroupVision $GroupVision */
        $GroupVision = ClassRegistry::init('GroupVision');
        $data = $GroupVision->getGroupVisionDetail($groupVisionId, $activeFlg);
        $data['GroupVision'] = $this->extendGroupVision($data['GroupVision'], 'original');
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

        foreach ($data as &$team) {
            $team['TeamVision'] = $this->extendTeamVision($team['TeamVision'], 'large');
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
        $data['TeamVision'] = $this->extendTeamVision($data['TeamVision'], 'original');
        return $data;
    }

    /**
     * グループビジョンのデータ拡張
     * # 拡張するデータ
     * - 画像パス
     * - 更新日のフォーマット
     * - グループ名
     * - グループのメンバー数
     *
     * @param array  $data
     * @param string $imgSize
     *
     * @return array
     */
    function extendGroupVision(array $data, string $imgSize = 'large'): array
    {
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        /** @var GroupService $GroupService */
        $GroupService = ClassRegistry::init('GroupService');
        $allGroups = $this->allGroups ?? $GroupService->findAllGroupsWithMemberCount();

        $groupId = $data['group_id'];
        $data['photo_path'] = $upload->uploadUrl($data, 'GroupVision.photo', ['style' => $imgSize]);
        $data['modified'] = $time->elapsedTime($data['modified']);
        if (isset($allGroups[$groupId])) {
            $data['group_name'] = $allGroups[$groupId]['name'];
            $data['member_count'] = $allGroups[$groupId]['member_count'];
        }
        unset($upload, $time);
        return $data;
    }

    /**
     * チームビジョンのデータ拡張
     * # 拡張するデータ
     * - 画像パス
     * - 更新日のフォーマット
     *
     * @param array  $data
     * @param string $imgSize
     *
     * @return array
     */
    function extendTeamVision(array $data, string $imgSize = 'large'): array
    {
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());
        $data['photo_path'] = $upload->uploadUrl($data, 'TeamVision.photo',
            ['style' => $imgSize]);
        $data['modified'] = $time->elapsedTime($data['modified']);
        unset($upload, $time);
        return $data;
    }

}
