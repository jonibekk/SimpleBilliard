<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::import('Service', 'GoalApprovalService');
App::uses('GoalMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

class GoalMemberService extends AppService
{
    /* コラボレーターの拡張種別 */
    const EXTEND_COACH = "GOAL:EXTEND_COACH";
    const EXTEND_COACHEE = "GOAL:EXTEND_COACHEE";

    /* リーダー変更リクエストの種類 */
    const CHANGE_LEADER_WITH_COLLABORATION = 1;
    const CHANGE_LEADER_WITH_QUIT = 2;
    const CHANGE_LEADER_FROM_GOAL_MEMBER = 3;

    /**
     * idによる単体データ取得
     *
     * @param       $id
     * @param array $extends
     *
     * @return array|mixed
     */
    function get($id, $extends = [])
    {
        $GoalMember = ClassRegistry::init("GoalMember");

        $data = Hash::extract($GoalMember->findById($id), 'GoalMember');
        if (empty($data)) {
            return $data;
        }

        return $this->extend($data, $extends);
    }

    /**
     * データ拡張
     *
     * @param $data
     * @param $extends
     *
     * @return mixed
     */
    function extend($data, $extends)
    {
        if (empty($data) || empty($extends)) {
            return $data;
        }

        $TeamMember = ClassRegistry::init("TeamMember");
        $User = ClassRegistry::init("User");

        if (in_array(self::EXTEND_COACH, $extends)) {
            $coachId = $TeamMember->getCoachId($data['user_id']);
            $data['coach'] = Hash::extract($User->findById($coachId), 'User');
        }

        if (in_array(self::EXTEND_COACHEE, $extends)) {
            $data['coachee'] = Hash::extract($User->findById($data['user_id']), 'User');
        }
        return $data;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalMemberId
     *
     * @return boolean
     */
    function isApprovableGoalMember($goalMemberId)
    {
        if (!$goalMemberId) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findById($goalMemberId, ['user_id']);
        if (!$goalMember) {
            return false;
        }
        $goalMemberUserId = Hash::get($goalMember, 'GoalMember.user_id');

        $userIsApproval = $GoalApprovalService->isApprovable($goalMemberUserId);
        $goalIsApproval = $GoalMember->isWishGoalApproval($goalMemberId);

        return $userIsApproval && $goalIsApproval;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalId
     * @param  $userId
     *
     * @return boolean
     */
    function isApprovableByGoalId($goalId, $userId)
    {
        if (empty($goalId) || empty($userId)) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findByGoalIdAndUserId($goalId, $userId);
        if (empty($goalMember)) {
            return false;
        }
        $goalMemberId = Hash::get($goalMember, 'GoalMember.id');

        $userIsApproval = $GoalApprovalService->isApprovable($userId);
        $goalIsApproval = $GoalMember->isWishGoalApproval($goalMemberId);

        return $userIsApproval && $goalIsApproval;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalId
     * @param  $userId
     *
     * @return boolean
     */
    function isLeader($goalId, $userId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        return $GoalMember->isLeader($goalId, $userId);
    }

    /**
     * ゴールメンバーか判定
     *
     * @param  $goalId
     * @param  $userId
     *
     * @return boolean
     */
    function isMember(int $goalId, int $userId): bool
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        return $GoalMember->isCollaborated($goalId, $userId);
    }

    /**
     * ゴール変更リクエストのバリデーション
     *
     * @param  array $formData
     * @param  int   $changeType
     *
     * @return true || string
     */
    public function validateChangeLeader(array $formData, int $changeType)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // パラメータ存在チェック
        $goalId = Hash::get($formData, 'Goal.id');
        if (empty($goalId) || empty($changeType)) {
            return __("Invalid value");
        }

        // ゴールが存在するか
        $goal = $Goal->findById($goalId);
        if (empty($goal)) {
            return __("The Goal doesn't exist.");
        }

        // 今期以降のゴールか
        $isAfterCurrentGoal = $GoalService->isGoalAfterCurrentTerm($goalId);
        if (!$isAfterCurrentGoal) {
            return __("You can't change leader in the Goal before current term.");
        }

        // 評価開始前か
        $termType = $GoalService->getTermType(Hash::get($goal, 'Goal.start_date'));
        if ($termType == GoalService::TERM_TYPE_CURRENT) {
            $currentTermId = $EvaluateTerm->getCurrentTermId();
            $isStartedEvaluation = $EvaluateTerm->isStartedEvaluation($currentTermId);
            if ($isStartedEvaluation) {
                $this->log(sprintf("[%s]%s", __METHOD__,
                    sprintf("Failed to change leader being evaluating. goalId:%s", $goalId)));
                return __("You cant't change leader in the Goal during the evaluation period.");
            }
        }

        // リーダーがinactiveのケース
        if ($changeType === self::CHANGE_LEADER_FROM_GOAL_MEMBER) {
            // 自分がゴールメンバーかどうか
            if (!$GoalMember->isCollaborated($goalId)) {
                $this->log(sprintf("[%s]%s", __METHOD__,
                    sprintf("Failed to change leader not being goal member. goalId:%s, userId:%s", $goalId,
                        $GoalMember->my_uid)));
                return __("You don't have a permission to edit this Goal.");
            }

            // アクティブなリーダーが存在する場合は、ゴールメンバーである自分にはリーダー変更権限がない
            $goalLeader = $GoalMember->getActiveLeader($goalId);
            if ($goalLeader) {
                $this->log(sprintf("[%s]%s", __METHOD__,
                    sprintf("Failed to change leader existing leader. goalId:%s, userId:%s", $goalId,
                        $GoalMember->my_uid)));
                return __("You don't have a permission to edit this Goal.");
            }
            // 自分がリーダーのケース
        } else {
            // 自分がリーダーかどうか
            $loginUserIsLeader = $GoalMember->isLeader($goalId, $GoalMember->my_uid);
            if (!$loginUserIsLeader) {
                $this->log(sprintf("[%s]%s", __METHOD__,
                    sprintf("Failed to change leader not being leader. goalId:%s, userId:%s", $goalId,
                        $GoalMember->my_uid)));
                return __("You don't have a permission to edit this Goal.");
            }

            // リーダーを変更して自分がコラボする場合
            if ($changeType === self::CHANGE_LEADER_WITH_COLLABORATION) {
                $GoalMember->set($formData);
                if (!$GoalMember->validates()) {
                    $this->log(sprintf("[%s]%s", __METHOD__,
                        sprintf("Failed to change leader not being able to collab. data:%s",
                            var_export($formData, true))));
                    return __("Invalid value");
                }
            }
        }

        // 変更後のリーダーがアクティブなゴールメンバーかどうか
        $newLeaderId = Hash::get($formData, 'NewLeader.id');
        if (!$GoalMember->isActiveGoalMember($newLeaderId, $goalId)) {
            $this->log(sprintf("Failed to change leader not being active member. goalId:%s, newLeaderId:%s", $goalId,
                $newLeaderId));
            return __("Some error occurred. Please try again from the start.");
        }

        return true;
    }

    /**
     * リーダー変更処理
     *
     * @param  array $data
     *
     * @return bool
     */
    function changeLeader(array $data, int $changeType): bool
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        try {
            // トランザクション開始
            $GoalMember->begin();

            // コラボレーター -> リーダー
            $newLeader = ['id'          => Hash::get($data, 'NewLeader.id'),
                          'type'        => $GoalMember::TYPE_OWNER,
                          'role'        => null,
                          'description' => null
            ];
            if (!$GoalMember->save($newLeader, false)) {
                throw new Exception(sprintf("Failed to change leader. data:%s"
                    , var_export($newLeader, true)));
            }
            $newLeaderUserId = $GoalMember->getUserIdByGoalMemberId(Hash::get($data, 'NewLeader.id'));

            // Goalのリーダーuid変更
            $Goal->id = Hash::get($data, 'Goal.id');
            if (!$Goal->saveField('user_id', $newLeaderUserId)) {
                throw new Exception(sprintf("Failed to change leader uid Goal model. data:%s"
                    , var_export($newLeaderUserId, true)));
            }

            // リーダー -> コラボレーター
            // 現リーダーがアクティブの場合は役割とタイプを変更
            // 現リーダーが非アクティブの場合はタイプのみ変更
            $data['GoalMember']['type'] = $GoalMember::TYPE_COLLABORATOR;
            if (!$GoalMember->save($data['GoalMember'], false)) {
                throw new Exception(sprintf("Failed to collaborate. data:%s"
                    , var_export($data['GoalMember'], true)));
            }

            // ゴール脱退
            if ($changeType === self::CHANGE_LEADER_WITH_QUIT) {
                $goalMemberId = Hash::get($data, 'GoalMember.id');
                $GoalMember->delete($goalMemberId);
                // 論理削除している関係で必ずdelete()メソッドは必ずfalseを返す。
                // よって削除が成功してるかどうか泥臭くチェックしてる。泣
                if (!empty($GoalMember->findById($goalMemberId))) {
                    throw new Exception(sprintf("Failed to quit goal. data:%s"
                        , var_export($goalMemberId, true)));
                }

                // 認定対象の場合のみ未認定カウントキャッシュを削除
                $goalMemberId = Hash::get($data, 'GoalMember.id');
                if ($this->isApprovableGoalMember($goalMemberId)) {
                    $quitUserId = $GoalMember->getUserIdByGoalMemberId($goalMemberId);
                    $coachId = $TeamMember->getCoachId($quitUserId);
                    Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
                }

                // アクション可能ゴール一覧キャッシュ削除(旧リーダー)
                Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
                // ユーザページのマイゴール一覧キャッシュ削除(旧リーダー)
                Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');
            }
            // アクション可能ゴール一覧キャッシュ削除(新リーダー)
            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true, $newLeaderUserId), 'user_data');
            // ユーザページのマイゴール一覧キャッシュ削除
            Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true, $newLeaderUserId), 'user_data');

            // トランザクション完了
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
     * ゴールのリーダー変更可能かチェックする
     * # 条件
     * ## リーダーのケース
     * - 自分がリーダーで
     * - アクティブなコラボレーターが一人以上存在する場合
     * ## コラボレーターのケース
     * - 自分がコラボレーターで
     * - アクティブなリーダーが存在しない場合
     *
     * @param  int $goalId
     *
     * @return bool
     */
    function canChangeLeader(int $goalId): bool
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");

        // リーダーのケース
        $isLeader = $GoalMember->isLeader($goalId, $GoalMember->my_uid);
        $collaborators = $GoalMember->getActiveCollaboratorList($goalId);
        if ($isLeader && count($collaborators) > 0) {
            return true;
        }

        // コラボレーターのケース
        $isCollaborator = $GoalMember->isCollaborator($goalId, $GoalMember->my_uid);
        if ($isCollaborator && !$GoalMember->getActiveLeader($goalId)) {
            return true;
        }

        return false;
    }
}
