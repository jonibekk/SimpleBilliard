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

        // コラボ情報の保存
        if (Hash::get($saveData, 'GoalMember')) {
            $isSaveSuccessGoalMember = $GoalMember->save($saveData);
            if (!$isSaveSuccessGoalMember) {
                $GoalMember->rollback();
                return false;
            }

            // コラボレータとコーチの認定未処理件数キャッシュを削除
            $collaboUserId = $GoalMember->getUserIdByGoalMemberId($GoalMember->getLastInsertID());
            $coachUserId = $TeamMember->getCoachId($collaboUserId);
            $this->deleteUnapprovedCountCache([$collaboUserId, $coachUserId]);
        }

        // 認定履歴情報の保存
        if (Hash::get($saveData, 'ApprovalHistory')) {
            $isSaveSuccessApprovalHistory = $ApprovalHistory->add($saveData);
            if (!$isSaveSuccessApprovalHistory) {
                $GoalMember->rollback();
                return false;
            }
        }

        $GoalMember->commit();
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
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");
        /** @var TkrChangeLog $TkrChangeLog */
        $TkrChangeLog = ClassRegistry::init("TkrChangeLog");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $res = Hash::extract($resByModel, 'GoalMember');

        // モデル名整形(大文字->小文字)
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

        // ゴール/TKRの変更前のスナップショットを取得
        $goalId = Hash::extract($res, 'goal.id');
        $res['goal']['goal_change_log'] = Hash::get($GoalChangeLog->findLatestSnapshot($goalId), 'data');
        $res['goal']['tkr_change_log'] = Hash::get($TkrChangeLog->findLatestSnapshot($goalId), 'data');

        // 認定履歴の文言を追加
        $goal_memberUserId = $res['user']['id'];
        $res['approval_histories'] = $this->addClearImportantWordToApprovalHistories($res['approval_histories'],
            $goal_memberUserId);
        $res['histories_view_more_text'] = __('View %s comments', count($res['approval_histories']) - 1);

        // TKRの整形
        $res['goal']['top_key_result'] = $KeyResultService->processKeyResult($res['goal']['top_key_result']);
        if(Hash::get($res, 'goal.tkr_change_log')) {
            $res['goal']['tkr_change_log'] = $KeyResultService->processKeyResult($res['goal']['tkr_change_log']);
        }

        // 画像パス追加
        $res['user']['original_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo');
        $res['user']['small_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo', ['style' => 'small']);
        $res['user']['large_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo', ['style' => 'large']);
        $res['goal']['original_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo');
        $res['goal']['small_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo', ['style' => 'small']);
        $res['goal']['large_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo', ['style' => 'large']);
        if(Hash::get($res, 'goal.goal_change_log.photo_file_name')) {
            // Uploadヘルパーに認識させるため、一時的に仮の配列を作る
            $tmp = ['Goal' => $res['goal']['goal_change_log']];
            $res['goal']['goal_change_log']['original_img_url'] = $Upload->uploadUrl($tmp, 'Goal.photo');
            $res['goal']['goal_change_log']['small_img_url'] = $Upload->uploadUrl($tmp, 'Goal.photo', ['style' => 'small']);
            $res['goal']['goal_change_log']['large_img_url'] = $Upload->uploadUrl($tmp, 'Goal.photo', ['style' => 'large']);
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
    function generateSaveData($approvalType, $requestData, $userId)
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
}
