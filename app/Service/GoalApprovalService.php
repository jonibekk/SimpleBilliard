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
App::uses('TkrChangeLog', 'Model');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');

class GoalApprovalService extends AppService
{
    /**
     * コーチとしての未対応認定件数取得
     *
     * @param $userId
     *
     * @return mixed
     */
    function countUnapprovedGoal($userId)
    {
        $GoalMember = ClassRegistry::init("GoalMember");
        // Redisのキャッシュデータ取得
        $count = Cache::read($GoalMember->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
        // Redisから無ければDBから取得してRedisに保存
        if ($count === false) {
            $count = $GoalMember->countUnapprovedGoal($userId);
            Cache::write($GoalMember->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), $count, 'user_data');
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
    function haveAccessAuthoriyOnApproval($goalMemberId, $userId)
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
     * 認定詳細ページの初期データレスポンスのためにモデルデータをフォーマット
     *
     * @param  $resByModel
     * @param  $myUserId
     *
     * @return $res
     */
    public function processGoalApprovalForResponse($resByModel, $myUserId)
    {
        /** @var GoalCategory $GoalCategory */
        $GoalCategory = ClassRegistry::init("GoalCategory");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // モデル名整形(大文字->小文字)
        $res = Hash::extract($resByModel, 'GoalMember');
        $res['user'] = Hash::extract($resByModel, 'User');
        $res['goal'] = Hash::extract($resByModel, 'Goal');
        $res['goal']['category'] = Hash::extract($resByModel, 'Goal.GoalCategory');
        $res['goal']['leader'] = Hash::extract($resByModel, 'Goal.Leader.0');
        $res['goal']['leader']['user'] = Hash::extract($resByModel, 'Goal.Leader.0.User');
        $res['goal']['top_key_result'] = Hash::extract($resByModel, 'Goal.TopKeyResult');
        $res['approval_histories'] = Hash::map($resByModel, 'ApprovalHistory', function ($value) {
            $value['user'] = Hash::extract($value, 'User');
            unset($value['User']);
            return $value;
        });

        // 画像パス追加
        $res['user'] = $User->attachImgUrl($res['user'], 'User');
        $res['goal'] = $Goal->attachImgUrl($res['goal'], 'Goal');

        // 認定履歴の文言を追加
        $goal_memberUserId = $res['user']['id'];
        $res['approval_histories'] = $this->addClearImportantWordToApprovalHistories($res['approval_histories'],
            $goal_memberUserId);
        $res['histories_view_more_text'] = __('View %s comments', count($res['approval_histories']) - 1);

        // TKRの整形
        $res['goal']['top_key_result'] = $KeyResultService->processKeyResult($res['goal']['top_key_result']);

        // ゴール/TKRの変更前のスナップショットを取得
        $res['goal'] = $this->processChangeLog($res['goal']);
        if(Hash::get($res, 'goal.tkr_change_log')) {
            // 画像パス追加
            $res['goal']['goal_change_log'] = $Goal->attachImgUrl($res['goal']['goal_change_log'], 'Goal');
            // TKRの整形
            $res['goal']['tkr_change_log'] = $KeyResultService->processKeyResult($res['goal']['tkr_change_log']);
            // カテゴリ追加
            $category =  $GoalCategory->findById($res['goal']['goal_change_log']['goal_category_id'], ['name']);
            $res['goal']['goal_change_log']['category'] = Hash::get($category, 'GoalCategory');
        }

        // マッピング
        $res['is_leader'] = (boolean)$res['type'];
        $res['is_mine'] = $res['user']['id'] == $myUserId;
        $res['type'] = GoalMember::$TYPE[$res['type']];

        // 不要な要素の削除
        unset($res['User'], $res['Goal'], $res['ApprovalHistory'], $res['goal']['GoalCategory'], $res['goal']['Leader'], $res['goal']['TopKeyResult'], $res['goal']['leader']['User']);

        return $res;
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
                'comment'                 => Hash::get($requestData, 'approval_history.comment')
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
                'comment'                 => Hash::get($requestData, 'approval_history.comment')
            ]
        ];

        return $saveData;
    }

    function addClearImportantWordToApprovalHistories($approvalHistories, $goal_memberUserId)
    {
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        return Hash::map($approvalHistories, '',
            function ($approvalHistory) use ($goal_memberUserId, $ApprovalHistory) {
                $clearStatus = $approvalHistory['select_clear_status'];
                $importantStatus = $approvalHistory['select_important_status'];

                if ($approvalHistory['user_id'] == $goal_memberUserId) {
                    $clearAndImportantWord = '';
                } elseif ($clearStatus == $ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == $ApprovalHistory::STATUS_IS_IMPORTANT) {
                    $clearAndImportantWord = __('This Top Key Result is clear and most important.');
                } elseif ($clearStatus == $ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == $ApprovalHistory::STATUS_IS_NOT_IMPORTANT) {
                    $clearAndImportantWord = __('This Top Key Result is not most important.');
                } else {
                    $clearAndImportantWord = __('This Top Key Result is not clear.');
                }

                $approvalHistory['clear_and_important_word'] = $clearAndImportantWord;
                return $approvalHistory;
            });
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
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");
        /** @var TkrChangeLog $TkrChangeLog */
        $TkrChangeLog = ClassRegistry::init("TkrChangeLog");

        $goalId = Hash::get($GoalMember->findById($goalMemberId, ['goal_id']), 'GoalMember.goal_id');
        if(!$goalId) {
            $this->log("Failed to get goal member by GoalMember.id : $goalMemberId");
            return false;
        }

        return $GoalChangeLog->saveSnapshot($goalId) && $TkrChangeLog->saveSnapshot($goalId);
    }

    /**
     * ゴール編集ログの差分を確認し、差分があればレスポンスにログを追加する
     *
     * @param  $goal
     *
     * @return $goal
     */
    function processChangeLog($goal)
    {
        // goal
        $goalDiffCheckPaths = ['name', 'photo_file_name', 'goal_category_id'];
        $goal['goal_change_log'] = $this->processChangeGoalLog($goal, $goalDiffCheckPaths);

        // tkr
        $tkrDiffCheckPaths = ['name', 'start_value', 'target_value', 'value_unit', 'description'];
        $goal['tkr_change_log'] = $this->processChangeTkrLog($goal, $tkrDiffCheckPaths);

        return $goal;
    }

    function processChangeGoalLog($goal, $diffCheckPaths)
    {
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");

        $goalId = Hash::extract($goal, 'id');
        $goalChangeLog = $GoalChangeLog->findLatestSnapshot($goalId);
        if(!$goalChangeLog) {
            return null;
        }

        // 現在のゴールと変更ログとの差分を計算。値が違うキーだけ抽出される
        $goalChangeDiff = Hash::diff($goal, $goalChangeLog);

        // Calc goal diff
        foreach($diffCheckPaths as $path) {
            if(Hash::get($goalChangeDiff, $path)) {
                return $goalChangeLog;
            }
        }

        return null;
    }

    function processChangeTkrLog($goal, $diffCheckPaths)
    {
        /** @var TkrChangeLog $TkrChangeLog */
        $TkrChangeLog = ClassRegistry::init("TkrChangeLog");

        $goalId = Hash::extract($goal, 'id');
        $tkrChangeLog = $TkrChangeLog->findLatestSnapshot($goalId);
        if(!$tkrChangeLog) {
            return null;
        }

        // 現在のtkrと変更ログとの差分を計算。値が違うキーだけ抽出される
        $tkrChangeDiff = Hash::diff($goal['top_key_result'], $tkrChangeLog);

        // Calc tkr diff
        foreach($diffCheckPaths as $path) {
            if(Hash::get($tkrChangeDiff, $path)) {
                return $tkrChangeLog;
            }
        }

        return null;
    }

}
