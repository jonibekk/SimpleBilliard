<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('ActionResult', 'Model');
App::uses('KeyResult', 'Model');
App::uses('GoalMember', 'Model');
App::uses('Post', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('TeamMember', 'Model');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

/**
 * Class ActionService
 */
class ActionService extends AppService
{

    /**
     * アクション登録
     *
     * @param array $action
     * @param array $fileIds
     * @param       $share
     *
     * @return bool|int
     * @throws Exception
     */
    function create(array $action, array $fileIds, $share)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init("KrProgressLog");
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init("AttachedFile");
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        try {
            $ActionResult->begin();
            $krId = Hash::get($action, 'key_result_id');
            $kr = $KeyResult->getById($krId);
            $krCurrentVal = Hash::get($action, 'key_result_current_value');
            if ($kr['value_unit'] == KeyResult::UNIT_BINARY) {
                $krCurrentVal = empty($krCurrentVal) ? 0 : 1;
            }

            $krChangeVal = $krCurrentVal - Hash::get($kr, 'current_value');
            $goalId = Hash::get($action, 'goal_id');
            $teamId = Hash::get($action, 'team_id');
            $userId = Hash::get($action, 'user_id');
            $now = REQUEST_TIMESTAMP;

            // アクション保存
            $actionSaveData = [
                'goal_id'       => $goalId,
                'team_id'       => $teamId,
                'user_id'       => $userId,
                'type'          => ActionResult::TYPE_KR,
                'name'          => Hash::get($action, 'name'),
                'key_result_id' => $krId,
                'completed'     => $now
            ];
            if (!$ActionResult->save($actionSaveData, false)) {
                throw new Exception(sprintf("Failed create action. data:%s"
                    , var_export($actionSaveData, true)));
            }
            $newActionId = $ActionResult->getLastInsertID();

            // KR進捗ログ保存
            $progressLogSaveData = [
                'goal_id'          => $goalId,
                'team_id'          => $teamId,
                'user_id'          => $userId,
                'key_result_id'    => $krId,
                'action_result_id' => $newActionId,
                'value_unit'       => Hash::get($kr, 'value_unit'),
                'before_value'     => Hash::get($kr, 'current_value'),
                'change_value'     => $krChangeVal,
                'target_value'     => Hash::get($kr, 'target_value'),
            ];
            if (!$KrProgressLog->save($progressLogSaveData)) {
                throw new Exception(sprintf("Failed save kr progress log. data:%s"
                    , var_export($progressLogSaveData, true)));
            }

            // アクションとしての投稿
            if (!$Post->addGoalPost(Post::TYPE_ACTION, $goalId, $userId, false,
                $newActionId, $share, PostShareCircle::SHARE_TYPE_ONLY_NOTIFY)
            ) {
                throw new Exception(sprintf("Failed create post. data:%s"
                    , var_export(compact('newActionId', 'goalId', 'userId'), false)));
            }

            // アクション画像保存
            if (!$AttachedFile->saveRelatedFiles($newActionId, AttachedFile::TYPE_MODEL_ACTION_RESULT, $fileIds)) {
                throw new Exception(sprintf("Failed save attached files. data:%s"
                    , var_export(compact('newActionId', 'fileIds'), false)));
            }

            // KR進捗&最新アクション日時更新
            $updateKr = [
                'id'              => $krId,
                'current_value'   => $krCurrentVal,
                'latest_actioned' => $now
            ];
            if ($krCurrentVal == Hash::get($kr, 'target_value')) {
                $updateKr['completed'] = $now;
            }
            if (!$KeyResult->save($updateKr, false)) {
                throw new Exception(sprintf("Failed update kr progress. data:%s"
                    , var_export($updateKr, false)));
            }

            // ダッシュボードのKRキャッシュ削除
            $KeyResultService->removeGoalMembersCacheInDashboard($goalId, false);
            Cache::delete($KeyResult->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');

            $ActionResult->commit();
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $ActionResult->rollback();
//            $AttachedFile->deleteAllRelatedFiles($newActionId, AttachedFile::TYPE_MODEL_ACTION_RESULT);
            return false;
        }

        // 添付ファイルが存在する場合は一時データを削除
        foreach ($fileIds as $hash) {
            $GlRedis->delPreUploadedFile($teamId, $userId, $hash);
        }

        return (int)$newActionId;

    }

    /**
     * アクション一覧をユーザーIDでグルーピング
     *
     * @param  $actionResults
     */
    function groupByUser(array $actions): array
    {
        $groupedActions = Hash::combine($actions, '{n}.user_id', '{n}');
        // 配列key振り直し
        $groupedActions = array_values($groupedActions);
        return $groupedActions;
    }

    /**
     * Check if user can view the action post
     *
     * @param int $userId
     * @param int $actionPostId
     *
     * @return bool
     */
    public function checkUserAccess(int $userId, int $actionPostId): bool
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $actionPost = $Post->getById($actionPostId);
        $postType = Hash::get($actionPost, 'type');
        $teamId = Hash::get($actionPost, 'team_id');

        return $postType === Post::TYPE_ACTION && !empty($TeamMember->getIdByTeamAndUserId($teamId, $userId));
    }
}
