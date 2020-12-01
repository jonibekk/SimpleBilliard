<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::uses('Goal', 'Model');
App::uses('ApprovalHistory', 'Model');
App::uses('GoalMember', 'Model');
App::uses('GoalChangeLog', 'Model');
App::uses('KrChangeLog', 'Model');
App::uses('KeyResult', 'Model'); App::import('Service', 'GoalMemberService');
App::import('Policy', 'GoalPolicy');

class GoalApprovalService extends AppService
{
    function get($goalMemberId, $loginUserId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var ApiGoalApprovalService $ApiGoalApprovalService */
        $ApiGoalApprovalService = ClassRegistry::init("ApiGoalApprovalService");

        $goalMember = $GoalMember->getForApproval($goalMemberId);
        $res = $ApiGoalApprovalService->process($goalMember, $loginUserId);
        return $res;
    }

    /**
     * コーチとしての未対応認定件数取得
     *
     * @param $userId
     *
     * @return mixed
     */
    function countUnapprovedGoal($userId, $teamId = null)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        if (!empty($teamId)) {
            $GoalMember->current_team_id = $teamId;
            $GoalMember->Team->current_team_id = $teamId;
            $GoalMember->Team->Term->current_team_id = $teamId;
        }
        // Redisのキャッシュデータ取得
        $count = Cache::read($GoalMember->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $userId), 'user_data');
        // Redisから無ければDBから取得してRedisに保存
        if ($count === false) {
            $count = $GoalMember->countUnapprovedGoal($userId);
            Cache::write($GoalMember->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $userId), $count, 'user_data');
        }
        return $count;
    }

    /**
     * 認定コメントリスト取得
     *
     * @param $goalMemberId
     *
     * @return array
     */
    function findHistories($goalMemberId)
    {
        if (empty($goalMemberId)) {
            return [];
        }
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        // 認定コメントリスト取得
        $histories = $ApprovalHistory->findByGoalMemberId($goalMemberId);
        if(empty($histories)) {
            return [];
        }

        $histories = Hash::extract($ApprovalHistory->findByGoalMemberId($goalMemberId), '{n}.ApprovalHistory');
        $goalMember = $GoalMemberService->get($goalMemberId, [
            GoalMemberService::EXTEND_COACH,
            GoalMemberService::EXTEND_COACHEE,
        ]);

        // 認定履歴に評価者からの評価コメント追加
        $histories = $this->addClearImportantWordToApprovalHistories($histories, $goalMember['user_id']);

        foreach ($histories as &$v) {
            $v['user'] = ($v['user_id'] == $goalMember['user_id']) ?
                $goalMember['coachee'] : $goalMember['coach'];
        }
        return $histories;
    }

    /**
     * 認定ページアクセス権限チェック
     * 認定ページにおいてユーザーがコラボレーターの情報にアクセスできるかチェック
     *
     * @param  integer $goalMemberId
     * @param  integer $userId
     *
     * @return boolean
     */
    function haveAccessAuthorityOnApproval($goalMemberId, $userId)
    {
        $GoalMember = ClassRegistry::init("GoalMember");
        $Team = ClassRegistry::init("Team");
        $TeamMember = ClassRegistry::init("TeamMember");
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");

        // チームの評価設定が有効かチェック
        if (!$EvaluationSetting->isEnabled()) {
            return false;
        }

        if (!($goalMemberId && $userId)) {
            return false;
        }

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeUserIds = $TeamMember->getMyMembersList($userId);

        // ユーザーのコーチのユーザーIDを取得
        $coachUserId = $TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとしてのアクセス権限
        $goal_memberUserId = $GoalMember->getUserIdByGoalMemberId($goalMemberId);
        $haveAuthoriyAsCoach = in_array($goal_memberUserId, $coacheeUserIds);

        // コーチーとしてのアクセス権限
        $haveAuthoriyAsCoachee = $userId == $goal_memberUserId;

        return $haveAuthoriyAsCoach || $haveAuthoriyAsCoachee;
    }

    /**
     * 認定処理未着手カウントのキャッシュ削除
     *
     * @param  array|integer $userIds integerで渡ってきたら内部で配列に変換
     *
     * @return array $deletedCacheUserIds
     */

    function deleteUnapprovedCountCache($userIds)
    {
        $Goal = ClassRegistry::init("Goal");
        if (gettype($userIds) === "integer") {
            $userIds = [$userIds];
        }
        $deletedCacheUserIds = [];
        foreach ($userIds as $userId) {
            $successDelete = Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $userId), 'user_data');
            if ($successDelete) {
                $deletedCacheUserIds[] = $userId;
            }
        }
        return $deletedCacheUserIds;
    }

    /**
     * コラボレーター情報の更新と認定履歴の保存
     *
     * @param  array $saveData
     *
     * @return boolean
     */
    function saveApproval($saveData)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $GoalMember->begin();

        try {
            // コラボ情報の保存
            $goalMemberId = Hash::get($saveData, 'GoalMember.id');
            if ($goalMemberId) {
                $isSaveSuccessGoalMember = $GoalMember->save($saveData);
                if (!$isSaveSuccessGoalMember) {
                    throw new Exception(sprintf("Failed to save goal member. data:%s"
                        , var_export($saveData, true)));
                }

                // コラボレータとコーチの認定未処理件数キャッシュを削除
                $goalMemberUserId = $GoalMember->getUserIdByGoalMemberId($goalMemberId);
                $coachUserId = $TeamMember->getCoachId($goalMemberUserId);
                $this->deleteUnapprovedCountCache([$goalMemberUserId, $coachUserId]);

                // コーチの場合はゴール/tkr情報のスナップショットを撮る
                if($coachUserId == $GoalMember->my_uid) {
                    $isSaveSuccessSnapshot = $this->saveSnapshotForApproval($goalMemberId);
                    if(!$isSaveSuccessSnapshot) {
                        throw new Exception(sprintf("Failed to save snapshot. data:%s"
                            , var_export($goalMemberId, true)));
                    }
                }
            }

            // 認定履歴情報の保存
            if (Hash::get($saveData, 'ApprovalHistory')) {
                $isSaveSuccessApprovalHistory = $ApprovalHistory->add($saveData);
                if (!$isSaveSuccessApprovalHistory) {
                    throw new Exception(sprintf("Failed to save approval history. data:%s"
                        , var_export($saveData, true)));
                }
            }

            $GoalMember->commit();

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $GoalMember->rollback();
            return false;
        }

        return true;
    }

    /**
     * ゴール認定POSTのバリデーション
     *
     * @param  array $data 検証するデータ
     *
     * @return true|CakeResponse
     */
    function validateApprovalPost($data)
    {
        $GoalMember = ClassRegistry::init("GoalMember");
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        $validation = [];

        // goal_member validation
        if (Hash::get($data, 'GoalMember')) {
            $GoalMember->set($data['GoalMember']);
            $goal_member_validation = $GoalMember->validates();
            if ($goal_member_validation !== true) {
                $validation['goal_member'] = $this->_validationExtract($GoalMember->validationErrors);
            }
        }

        // approval_history validation
        if (Hash::get($data, 'ApprovalHistory')) {
            $ApprovalHistory->set($data['ApprovalHistory']);
            $approval_history_validation = $ApprovalHistory->validates();
            if ($approval_history_validation !== true) {
                $validation['approval_history'] = $this->_validationExtract($ApprovalHistory->validationErrors);
            }
        }

        if (!empty($validation)) {
            return $validation;
        }
        return true;
    }

    /**
     * ゴール認定POSTデータを保存用に整形
     *
     * @param          $approvalType
     * @param  array   $requestData
     * @param  integer $userId
     *
     * @return array $saveData
     */
    function generateApprovalSaveData($approvalType, $requestData, $userId)
    {
        $goalMemberId = Hash::get($requestData, 'goal_member.id');
        $selectClearStatus = ApprovalHistory::STATUS_IS_CLEAR;
        $selectImportantStatus = ApprovalHistory::STATUS_IS_IMPORTANT;
        if ($approvalType === GoalMember::IS_NOT_TARGET_EVALUATION) {
            $selectClearStatus = Hash::get($requestData, 'approval_history.select_clear_status');
            $selectImportantStatus = Hash::get($requestData, 'approval_history.select_important_status');
        }
        $actionStatus = $this->getActionStatusByApprovalType($approvalType);

        $saveData = [
            'GoalMember'      => [
                'id'                   => $goalMemberId,
                'is_target_evaluation' => $approvalType,
                'approval_status'      => GoalMember::APPROVAL_STATUS_DONE
            ],
            'ApprovalHistory' => [
                'select_clear_status'     => $selectClearStatus,
                'select_important_status' => $selectImportantStatus,
                'goal_member_id'          => $goalMemberId,
                'user_id'                 => $userId,
                'comment'                 => Hash::get($requestData, 'approval_history.comment'),
                'action_status'           => $actionStatus
            ]
        ];

        return $saveData;
    }

    /**
     * 申請取り消しPOSTの保存データを定義
     *
     * @param  integer $goalMemberId
     *
     * @return array $saveData
     */
    function generateWithdrawSaveData($goalMemberId)
    {
        $saveData = [
            'GoalMember' => [
                'id'                   => $goalMemberId,
                'is_target_evaluation' => false,
                'approval_status'      => GoalMember::APPROVAL_STATUS_WITHDRAWN
            ]
        ];

        return $saveData;
    }

    /**
     * 認定コメントPOSTの保存データ定義
     *
     * @param  $requestData
     * @param  $userId
     *
     * @return $saveData
     */
    function generateCommentSaveData($requestData, $userId) {
        $saveData = [
            'ApprovalHistory' => [
                'user_id'                 => $userId,
                'goal_member_id'          => Hash::get($requestData, 'goal_member.id'),
                'comment'                 => Hash::get($requestData, 'approval_history.comment'),
                'action_status'           => ApprovalHistory::STATUS_ACTION_ONLY_COMMENT
            ]
        ];

        return $saveData;
    }

    /**
     * 認定タイプからコーチのアクションステータスを返す
     * @param  $approvalType
     * @return $actionStatus
     */
    function getActionStatusByApprovalType($approvalType) {
        $actionStatus = ApprovalHistory::STATUS_ACTION_NOTHING;
        if($approvalType === GoalMember::IS_NOT_TARGET_EVALUATION) {
            $actionStatus = ApprovalHistory::STATUS_ACTION_IS_NOT_TARGET_FOR_EVALUATION;
        } else if($approvalType === GoalMember::IS_TARGET_EVALUATION) {
            $actionStatus = ApprovalHistory::STATUS_ACTION_IS_TARGET_FOR_EVALUATION;
        }
        return $actionStatus;
    }

    /**
     * 対象ユーザが認定処理が可能かどうか？
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     *
     * @param      $target_user_id
     * @param null $team_id 通常は不要。shellからアクセスがあった場合に必要。
     *
     * @return bool
     */
    function isApprovable($target_user_id, $team_id = null)
    {
        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        if ($team_id) {
            $EvaluationSetting->current_team_id = $team_id;
            $TeamMember->current_team_id = $team_id;
        }

        $teamEvaluateIsEnabled = $EvaluationSetting->isEnabled();
        $coacheeEvaluateIsEnabled = $TeamMember->getEvaluationEnableFlg($target_user_id);
        $coachId = $TeamMember->getCoachId($target_user_id);
        return (bool)$teamEvaluateIsEnabled && (bool)$coacheeEvaluateIsEnabled && (bool)$coachId;
    }

    function showApprovable($target_user_id, $team_id = null)
    {
        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        if ($team_id) {
            $EvaluationSetting->current_team_id = $team_id;
            $TeamMember->current_team_id = $team_id;
        }

        $teamEvaluateIsEnabled = $EvaluationSetting->isEnabled();
        $coacheeEvaluateIsEnabled = $TeamMember->getEvaluationEnableFlg($target_user_id);
        return (bool)$teamEvaluateIsEnabled && (bool)$coacheeEvaluateIsEnabled;
    }

    /**
     * ゴール認定用にゴールとTKRのスナップショットを保存する
     *
     * @param  $collaboUserId
     *
     * @return boolean
     */
    function saveSnapshotForApproval($goalMemberId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var keyResult $keyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");

        $goalId = Hash::get($GoalMember->findById($goalMemberId, ['goal_id']), 'GoalMember.goal_id');
        if(!$goalId) {
            $this->log("Failed to get goal member by GoalMember.id : $goalMemberId");
            return false;
        }

        $tkrId = Hash::get($KeyResult->getTkr($goalId), 'KeyResult.id');
        if (!$tkrId) {
            $this->log("Failed to get tkr id by Goal.id : $goalMemberId");
            return false;
        }

        $savedGoalSnapshot = $GoalChangeLog->saveSnapshot($goalId);
        $savedTkrSnapshot = $KrChangeLog->saveSnapshot($goalId, $tkrId, $KrChangeLog::TYPE_APPROVAL_BY_COACH);

        return $savedGoalSnapshot && $savedTkrSnapshot;
    }

    function genRequestApprovalData(int $userId, int $teamId, int $goalId): array
    {
        /** @var GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Goal */
        $Goal = ClassRegistry::init("Goal");

        $goal = $Goal->findById($goalId);
        $gm = $GoalMember->find('first', [
            'conditions' => ['user_id' => $userId, 'goal_id' => $goalId, 'team_id' => $teamId]
        ]);

        $canRequestApproval = true;
        $cannotRequestApprovalReason = null;
        $isWishApproval = false;
        $isTargetEvaluation = false;

        $coachId = $TeamMember->getCoachUserIdByMemberUserId($userId);

        if (!empty($gm)) {
            $isWishApproval = $gm['GoalMember']['is_wish_approval'];
            $isTargetEvaluation = $gm['GoalMember']['is_target_evaluation'];
        }

        if ($isWishApproval || $isTargetEvaluation) {
            $canRequestApproval = false;
        }

        if (empty($coachId)) {
            $canRequestApproval = false;
            $cannotRequestApprovalReason = __("Goal cannot be approved because the coach is not set. Contact the team administrator.");
        } else {
            $coachPolicy = new GoalPolicy($coachId, $teamId);

            if (!$coachPolicy->read($goal['Goal'])) {
                $canRequestApproval = false;
                $cannotRequestApprovalReason = __("Goal cannot be approved because the coach is not set. Contact the team administrator.");
            }
        }

        return [
            "showApprove" => $this->showApprovable($userId, $teamId),
            "defaultChecked" => $isWishApproval || $isTargetEvaluation,
            "pendingApproval" => $isWishApproval && !$isTargetEvaluation,
            "canRequestApproval" => $canRequestApproval,
            "cannotRequestApprovalReason" => $cannotRequestApprovalReason
        ];
    }
}
