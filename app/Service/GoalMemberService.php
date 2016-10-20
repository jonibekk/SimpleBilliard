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
}
