<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Goal', 'Model');
class GoalApprovalService
{
    function countUnapprovedGoal($userId)
    {
        $Goal = ClassRegistry::init("Goal");
        // Redisのキャッシュデータ取得
        $count = Cache::read($Goal->Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), 'user_data');
        // Redisから無ければDBから取得してRedisに保存
        if ($count === false) {
            $count = $Goal->Collaborator->countUnapprovedGoal($userId);
            Cache::set('duration', 60 * 1, 'user_data');//1 minute
            Cache::write($Goal->Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), $count, 'user_data');
        }
        return $count;
    }

    /**
     * 認定ページアクセス権限チェック
     * 認定ページにおいてユーザーがコラボレーターの情報にアクセスできるかチェック
     * @param  integer $collaboratorId
     * @param  integer $userId
     * @return boolean
     */
    function haveAccessAuthoriyOnApproval($collaboratorId, $userId)
    {
        $Goal = ClassRegistry::init("Goal");

        // チームの評価設定が有効かチェック
        if (!$Goal->Team->EvaluationSetting->isEnabled()) {
            return false;
        }

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeUserIds = $Goal->Team->TeamMember->getMyMembersList($userId);

        // ユーザーのコーチのユーザーIDを取得
        $coachUserId = $Goal->Team->TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとしてのアクセス権限
        $collaboratorUserId = $Goal->Collaborator->getUserIdByCollaboratorId($collaboratorId);
        $haveAuthoriyAsCoach = in_array($collaboratorUserId, $coacheeUserIds);

        // コーチーとしてのアクセス権限
        $haveAuthoriyAsCoachee = $collaboratorId == $userId;

        // コラボレーターがコーチでもコーチーでもない場合はアクセス権限無し
        if(!$haveAuthoriyAsCoach && !$haveAuthoriyAsCoachee) {
            return false;
        }

        return true;
    }

    /**
     * 認定処理未着手カウントのキャッシュ削除
     * @param  array|integer $userIds integerで渡ってきたら内部で配列に変換
     * @return array $deletedCacheUserIds
     */
    function deleteUnapprovedCountCache($userIds)
    {
        $Goal = ClassRegistry::init("Goal");

        if(getType($userIds) === "integer") $userIds = [$userIds];
        $deletedCacheUserIds = [];
        foreach($userIds as $userId) {
            $successDelete = Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $userId), 'user_data');
            if($successDelete) $deletedCacheUserIds[] = $userId;
        }
        return $deletedCacheUserIds;
    }

    /**
     * コラボレーター情報の更新と認定履歴の保存
     * @param  array $saveData
     * @return boolean
     */
    function saveApproval($saveData)
    {
        $Goal = ClassRegistry::init("Goal");

        $Goal->Collaborator->begin();
        $isSaveSuccessCollaborator = $Goal->Collaborator->save($saveData);
        $isSaveSuccessApprovalHistory = $Goal->Collaborator->ApprovalHistory->add($saveData);
        if ($isSaveSuccessCollaborator && $isSaveSuccessApprovalHistory) {
            $Goal->Collaborator->commit();
            return true;
        }

        $Goal->Collaborator->rollback();
        return false;
    }

}
