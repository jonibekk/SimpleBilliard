<?php

/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('ActionResult', 'Model');
App::uses('KeyResult', 'Model');
App::uses('GoalMember', 'Model');
App::uses('Post', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('ActionResultFile', 'Model');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
App::uses('Service', 'UploadService');
App::uses('Service', 'AttachedFileService');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');
App::import('Model/Entity', 'ActionResultEntity');
App::import('Model/Entity', 'ActionResultFileEntity');

use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;
use Guzzle\Common\ToArrayInterface;

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

                'team_id'       => $teamId,
                'user_id'       => $userId,
                'type'          => ActionResult::TYPE_KR,
                'name'          => Hash::get($action, 'name'),
                'key_result_id' => $krId,
                'completed'     => $now
            ];
            if (!$ActionResult->save($actionSaveData, false)) {
                throw new Exception(sprintf(
                    "Failed create action. data:%s",
                    var_export($actionSaveData, true)
                ));
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
                throw new Exception(sprintf(
                    "Failed save kr progress log. data:%s",
                    var_export($progressLogSaveData, true)
                ));
            }

            // アクションとしての投稿
            if (!$Post->addGoalPost(
                Post::TYPE_ACTION,
                $goalId,
                $userId,
                false,
                $newActionId,
                $share,
                PostShareCircle::SHARE_TYPE_ONLY_NOTIFY
            )) {
                throw new Exception(sprintf(
                    "Failed create post. data:%s",
                    var_export(compact('newActionId', 'goalId', 'userId'), false)
                ));
            }

            // アクション画像保存
            if (!$AttachedFile->saveRelatedFiles($newActionId, AttachedFile::TYPE_MODEL_ACTION_RESULT, $fileIds)) {
                throw new Exception(sprintf(
                    "Failed save attached files. data:%s",
                    var_export(compact('newActionId', 'fileIds'), false)
                ));
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
                throw new Exception(sprintf(
                    "Failed update kr progress. data:%s",
                    var_export($updateKr, false)
                ));
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

        $postId = $Post->getByActionResultId($newActionId)['Post']['id'];

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');
        if ($TranslationService->canTranslate($teamId)) {
            $TranslationService->createDefaultTranslation($teamId, TranslationContentType::ACTION_POST(), $postId);
        }

        return (int) $newActionId;
    }

    public function createAngular(array $data)
    {
        try {
            $this->TransactionManager->begin();
            $newAction = $this->createAction($data);
            $this->updateKrAndProgress($newAction['id'], $data);
            $this->createGoalPost($newAction['id'], $data);
            $this->createAttachedFiles($newAction['id'], $data);
            $this->refreshKrCache($data['goal_id']);
            $this->TransactionManager->commit();

            $this->refreshKrCache($data['goal_id']);
            $this->translateActionPost($data['team_id'], $newAction['id']);
            return $newAction;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }
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

        return ($postType == Post::TYPE_ACTION) && (!empty($TeamMember->getIdByTeamAndUserId($teamId, $userId)));
    }

    private function createAction(array $data)
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");

        $actionSaveData = [
            'goal_id'       => $data['goal_id'],
            'team_id'       => $data['team_id'],
            'user_id'       => $data['user_id'],
            'type'          => ActionResult::TYPE_KR,
            'name'          => $data['name'],
            'key_result_id' => $data['key_result_id'],
            'completed'     => REQUEST_TIMESTAMP
        ];

        $ActionResult->create();
        $result = $ActionResult->useType()->useEntity()->save($actionSaveData, false);
        return $result;
    }

    private function updateKrAndProgress(int $newActionId, array $data)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init("KrProgressLog");

        $krId = $data['key_result_id'];
        $kr = $KeyResult->getById($krId);
        $krCurrentVal = $data['key_result_current_value'];
        $krChangeVal = $krCurrentVal - Hash::get($kr, 'current_value');

        $progressLogSaveData = [
            'goal_id'          => $data['goal_id'],
            'team_id'          => $data['team_id'],
            'user_id'          => $data['user_id'],
            'key_result_id'    => $krId,
            'action_result_id' => $newActionId,
            'value_unit'       => Hash::get($kr, 'value_unit'),
            'before_value'     => Hash::get($kr, 'current_value'),
            'change_value'     => $krChangeVal,
            'target_value'     => Hash::get($kr, 'target_value'),
        ];

        if (!$KrProgressLog->save($progressLogSaveData)) {
            throw new Exception(sprintf(
                "Failed save kr progress log. data:%s",
                var_export($progressLogSaveData, true)
            ));
        }

        $updateKr = [
            'id'              => $krId,
            'current_value'   => $krCurrentVal,
            'latest_actioned' => REQUEST_TIMESTAMP
        ];

        if ($krCurrentVal == Hash::get($kr, 'target_value')) {
            $updateKr['completed'] = REQUEST_TIMESTAMP;
        }

        if (!$KeyResult->save($updateKr, false)) {
            throw new Exception(sprintf(
                "Failed update kr progress. data:%s",
                var_export($updateKr, false)
            ));
        }
    }

    private function createGoalPost(int $newActionId, array $data)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $goalPost = $Post->addGoalPost(
            Post::TYPE_ACTION,
            $data['goal_id'],
            $data['user_id'],
            false,
            $newActionId,
            [],
            PostShareCircle::SHARE_TYPE_ONLY_NOTIFY
        );

        if (!$goalPost) {
            throw new Exception(sprintf(
                "Failed create post. data:%s",
                var_export(compact('newActionId', 'goalId', 'userId'), false)
            ));
        }
    }

    private function createAttachedFiles(int $newActionId, array $data)
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init("UploadService");
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init("AttachedFileService");
        /** @var ActionResultFile $ActionResultFile */
        $ActionResultFile = ClassRegistry::init('ActionResultFile');

        $userId = $data['user_id'];
        $teamId = $data['team_id'];
        $fileIds = $data['file_ids'];
        $addedFiles = [];
        $actionFileIdx = 0;

        try {
            foreach ($fileIds as $id) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $id);

                /** @var AttachedFileEntity $attachedFile */
                $attachedFile = $AttachedFileService->add($userId, $teamId, $uploadedFile, AttachedModelType::TYPE_MODEL_ACTION_RESULT());
                $addedFiles[] = $attachedFile['id'];
                $newData = [
                    'action_result_id' => $newActionId,
                    'attached_file_id' => $attachedFile['id'],
                    'team_id'          => $teamId,
                    'index_num'        => $actionFileIdx,
                    'del_flag'         => false,
                    'created'          => GoalousDateTime::now()->getTimestamp()
                ];
                $ActionResultFile->create();
                $ActionResultFile->useType()->useEntity()->save($newData, false);
                $actionFileIdx += 1;
                $UploadService->saveWithProcessing("AttachedFile", $attachedFile['id'], 'attached', $uploadedFile);
            }
        } catch (Exception $e) {
            foreach ($addedFiles as $id) {
                $UploadService->deleteAsset('AttachedFile', $id);
            }

            throw new Exception("Failed to save attached files.");
        }
    }

    private function refreshKrCache(int $goalId)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $KeyResultService->removeGoalMembersCacheInDashboard($goalId, false);
        Cache::delete($KeyResult->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');
    }

    private function translateActionPost(int $teamId, int $newActionId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        $postId = $Post->getByActionResultId($newActionId)['Post']['id'];

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');
        if ($TranslationService->canTranslate($teamId)) {
            $TranslationService->createDefaultTranslation(
                $teamId,
                TranslationContentType::ACTION_POST(),
                $postId
            );
        }
    }
}
