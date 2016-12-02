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
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

/**
 * Class GoalService
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
     * @return mixed|string
     * @throws Exception
     * @internal param int $goalId
     * @internal param int $userId
     * @internal param $goalId
     */
    function create(array $action, array $fileIds, $share)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init("AttachedFile");
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");

        try {
            $ActionResult->begin();
            $krId = Hash::get($action, 'key_result_id');
            $kr = $KeyResult->getById($krId);
            $krCurrentVal = Hash::get($action, 'key_result_current_value');
            $krChangeVal = $krCurrentVal - Hash::get($kr, 'current_value');
            $goalId = Hash::get($action, 'goal_id');
            $teamId = Hash::get($action, 'team_id');
            $userId = Hash::get($action, 'user_id');
            $now = REQUEST_TIMESTAMP;

            $saveAction = [
                'goal_id'                 => $goalId,
                'team_id'                 => $teamId,
                'user_id'                 => $userId,
                'type'                    => ActionResult::TYPE_KR,
                'name'                    => Hash::get($action, 'name'),
                'key_result_id'           => $krId,
                'key_result_before_value' => Hash::get($kr, 'current_value'),
                'key_result_change_value' => $krChangeVal,
                'completed'               => $now
            ];

            // アクション保存
            if (!$ActionResult->save($saveAction, false)) {
                throw new Exception(sprintf("Failed create action. data:%s"
                    , var_export($saveAction, true)));
            }

            // アクションとしての投稿
            $newActionId = $ActionResult->getLastInsertID();
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

            // KR進捗更新
            $updateKr = [
                'id'            => $krId,
                'current_value' => $krCurrentVal,
            ];
            if ($krCurrentVal == Hash::get($kr, 'target_value')) {
                $updateKr['completed'] = $now;
            }
            if (!$KeyResult->save($updateKr, false)) {
                throw new Exception(sprintf("Failed update kr progress. data:%s"
                    , var_export($updateKr, false)));
            }
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

        // キャッシュ削除
        Cache::delete($ActionResult->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        Cache::delete($ActionResult->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');

        return $newActionId;

    }
}
