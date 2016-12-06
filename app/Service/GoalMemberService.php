<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::uses('GoalMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

class GoalMemberService extends AppService
{
    /* コラボレーターの拡張種別 */
    const EXTEND_COACH = "GOAL:EXTEND_COACH";
    const EXTEND_COACHEE = "GOAL:EXTEND_COACHEE";

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
        if(!$goalMemberId) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findById($goalMemberId, ['user_id']);
        if(!$goalMember) {
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
        if(empty($goalId) || empty($userId)) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findByGoalIdAndUserId($goalId, $userId);
        if(empty($goalMember)) {
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

    public function validateExchangeLeader(int $goalId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // パラメータ存在チェック
        if (empty($goalId)) {
            return __("Invalid Request.");
        }

        // ゴールが存在するか
        $goal = $this->Goal->findById($goalId);
        if (empty($goal)) {
            return __("This goal doesn't exist.");
        }

        // 今期以降のゴールか
        $isAfterCurrentGoal = $GoalService->isAfterCurrentGoal($goalId);
        if(!$isAfterCurrentGoal) {
            return __("Can change leader after current term's goal.");
        }

        // 評価開始前か
        $termType = $GoalService->getTermType(Hash::get($goal, 'Goal.start_date'));
        if ($termType == GoalService::TERM_TYPE_CURRENT) {
            $currentTermId = $EvaluateTerm->getCurrentTermId();
            $isStartedEvaluation = $EvaluateTerm->isStartedEvaluation($currentTermId);
            if ($isStartedEvaluation) {
                return __("Some error occurred. Please try again from the start.");
            }
        }

        // 自分がゴールメンバーかどうか
        if (!$GoalMember->isCollaborated()) {
            return __("You don't have a permission to edit this goal.");
        }

        // 自分がリーダーかどうか
        $loginUserisLeader = $GoalMember->isLeader($goalId, $GoalMember->my_uid);
        if ($loginUserisLeader) {
            return true;
        }

        // リーダーが存在するか
        // 自分がリーダーでなく、他にリーダーが存在する場合はリーダー変更の権限無し
        $goalLeaderId = $GoalMember->getGoalLeaderId($goalId);
        if ($goalLeaderId) {
            return __("You don't have a permission to edit this goal.");
        }

        return true;
    }
}
