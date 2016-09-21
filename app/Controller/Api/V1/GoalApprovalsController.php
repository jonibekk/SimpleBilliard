<?php
App::uses('ApiController', 'Controller/Api');
App::uses('Collaborator', 'Model');
App::uses('TeamMember', 'Model');

/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal    $Goal
*/
class GoalApprovalsController extends ApiController
{
    /*
     * ログインしているユーザータイプ
     * 1: コーチのみ存在
     * 2: コーチとメンバーが存在
     * 3: メンバーのみ存在
     */
    const USER_TYPE_NOT_AVAILABLE = 0;
    const USER_TYPE_ONLY_COACH = 1;
    const USER_TYPE_COACH_AND_MEMBER = 2;
    const USER_TYPE_ONLY_MEMBER = 3;

    /*
     * オーバーライド
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }


    /**
     * 認定対象のゴールリスト取得
     */
    function get_list()
    {
        $this->log(__METHOD__.' start');
        // チームの評価設定が無効であれば404
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            throw new NotFoundException();
        }

        $userId = $this->Auth->user('id');
        $teamId = $this->Session->read('current_team_id');

//        /* coachとしてのゴールリスト取得 */
//        // コーチとして管理している評価対象のメンバーID取得
//        $memberIds = $this->Team->TeamMember->getMyMembersList($userId);
//
//
//        /* coacheeとしてのゴールリスト取得 */
//        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);
//        $this->done_cnt = $this->Goal->Collaborator->countCollaboGoal(
//            $teamId, $userId, $this->goal_user_ids,
//            [$this->goal_status['approval'], $this->goal_status['hold']]
//        );
//
//        $userType = $this->_getUserType($coachId, $memberIds);
//        /* ゴールリスト結合&並び替え */


        // コーチとして管理している評価対象のメンバーID取得
        $memberIds = $this->Team->TeamMember->getMyMembersList($userId);
        // 自分のコーチのユーザーIDを取得
        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);
        $userType = $this->_getUserType($coachId, $memberIds);
$this->log(compact('memberIds','coachId','userType'));

        $myEvaluationFlg = $this->Team->TeamMember->getEvaluationEnableFlg($userId, $teamId);
        $goalUserIds = $this->_getCollaboratorUserId($userId, $userType, $memberIds);

        $todoCnt = $this->Goal->Collaborator->countCollaboGoal(
            $teamId, $userId, $goalUserIds,
            [Collaborator::APPROVAL_STATUS_NEW, Collaborator::APPROVAL_STATUS_REAPPLICATION]
        );
$this->log(compact('myEvaluationFlg','goalUserIds','todoCnt'));


        $goals = $this->_getGoalInfo(
            $userId,
            $teamId,
            $userType,
            $memberIds
        );
$this->log(compact('goals'));

        foreach ($goals as $key => $val) {
            $goals[$key]['my_goal'] = false;
            $goals[$key]['is_present_term'] = $this->Goal->isPresentTermGoal($val['Goal']['id']);

            if ($userId === $val['User']['id']) {
                $goals[$key]['my_goal'] = true;
                if ($myEvaluationFlg === false) {
                    unset($goals[$key]);
                }
            }

            $approvalMsgList = [];
            if ($val['Collaborator']['approval_status'] === (string)Collaborator::APPROVAL_STATUS_REAPPLICATION) {
                $goals[$key]['status'] = $approvalMsgList[self::APPROVAL_MEMBER_GOAL_MSG];

            } else {
                if ($val['Collaborator']['approval_status'] === (string)Collaborator::APPROVAL_STATUS_DONE) {
                    $goals[$key]['status'] = $approvalMsgList[self::NOT_APPROVAL_MEMBER_GOAL_MSG];
                }
            }
        }

        $res = $goals;
        return $this->_getResponseSuccess($res);
    }

    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function _getCollaboratorUserId($userId, $userType, $memberIds)
    {
        $goalUserIds = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $goalUserIds = [$userId];
        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $goalUserIds = array_merge([$userId], $memberIds);
        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $goalUserIds = $memberIds;
        }
        return $goalUserIds;
    }

    /*
     * ゴールリスト取得
     */
    public function _getGoalInfo($userId, $teamId, $userType, $memberIds)
    {
        $goals = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $goals = $this->Goal->Collaborator->getCollaboGoalDetail(
                $teamId, [$userId], null, true, EvaluateTerm::TYPE_CURRENT);

        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $member_goal_info = $this->Goal->Collaborator->getCollaboGoalDetail(
                $teamId, $memberIds, false, EvaluateTerm::TYPE_CURRENT);

            $my_goal_info = $this->Goal->Collaborator->getCollaboGoalDetail(
                $teamId, [$userId], null, true, EvaluateTerm::TYPE_CURRENT);

            $goals = array_merge($member_goal_info, $my_goal_info);

        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $goals = $this->Goal->Collaborator->getCollaboGoalDetail(
                $teamId, $memberIds, null, false, EvaluateTerm::TYPE_CURRENT);
        }

        return $goals;
    }

    /*
     * コーチ認定機能を使えるユーザーか判定
     * 1: コーチがいる、メンバーいない
     * 2: コーチいる、メンバーがいる
     * 3: コーチがいない、メンバーがいる
     */
    public function _getUserType($coachId, $memberIds)
    {
        $isCoach = !empty($coachId);
        $isMember = !empty($memberIds);
        if ($isCoach === true && $isMember === false) {
            return self::USER_TYPE_ONLY_COACH;
        }

        if ($isCoach === true && $isMember === true) {
            return self::USER_TYPE_COACH_AND_MEMBER;
        }

        if ($isCoach === false && $isMember === true) {
            return self::USER_TYPE_ONLY_MEMBER;
        }

        return self::USER_TYPE_NOT_AVAILABLE;
    }


}
