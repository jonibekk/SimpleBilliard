<?php
App::uses('ApiController', 'Controller/Api');
App::uses('Collaborator', 'Model');
App::uses('TeamMember', 'Model');
App::uses('ApprovalHistory', 'Model');

/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal $Goal
 */
class GoalApprovalsController extends ApiController
{

    /*
     * 使用モデル
     */
    public $uses = [
        'Collaborator',
        'TeamMember',
        'ApprovalHistory',
    ];

    /*
     * 処理待ち && 自分のゴールの場合
     */
    const WAIT_MY_GOAL_MSG = 0;

    /*
     * 処理待ち && メンバーのゴール && valued_flag=3 の場合
     */
    const MODIFY_MEMBER_GOAL_MSG = 1;

    /*
     * 処理済み && メンバーのゴール && valued_flag=1 の場合
     */
    const APPROVAL_MEMBER_GOAL_MSG = 2;

    /*
     * 処理済み && メンバーのゴール && valued_flag=2 の場合
     */
    const NOT_APPROVAL_MEMBER_GOAL_MSG = 3;

    /*
     * 処理済み用のメッセージリスト
     */
    public $approval_msg_list = [];

    /*
     * ログインしているユーザータイプ
     * 1: コーチのみ存在
     * 2: コーチとメンバーが存在
     * 3: メンバーのみ存在
     */
    public $user_type = 0;
    const USER_TYPE_NOT_AVAILABLE = 0;
    const USER_TYPE_ONLY_COACH = 1;
    const USER_TYPE_COACH_AND_MEMBER = 2;
    const USER_TYPE_ONLY_MEMBER = 3;

    /*
     * 評価ステータス
     */
    public $goal_status = [
        'unapproved' => Collaborator::STATUS_UNAPPROVED,
        'approval'   => Collaborator::STATUS_APPROVAL,
        'hold'       => Collaborator::STATUS_HOLD,
        'modify'     => Collaborator::STATUS_MODIFY,
    ];


//    /*
//     * ログインユーザーの評価対象フラグ
//     */
//    public $my_evaluation_flg = false;
//

    /*
     * オーバーライド
     */
    public function beforeFilter()
    {

        parent::beforeFilter();

    }


    /**
     * GET visions
     *
     * @return string
     */
    function get_list()
    {

        $userId = $this->Auth->user('id');
        $teamId = $this->Session->read('current_team_id');

        $coachId = $this->TeamMember->getCoachUserIdByMemberUserId($userId);
        $memberIds = $this->TeamMember->getMyMembersList($userId);

        // コーチ認定機能が使えるユーザーはトップページ
        $userType = $this->_getUserType($coachId, $memberIds);
        if ($userType === self::USER_TYPE_NOT_AVAILABLE) {
        }

        $this->my_evaluation_flg = $this->TeamMember->getEvaluationEnableFlg($userId, $teamId);
        $this->goal_user_ids = $this->_getCollaboratorUserId($userId, $userType);

        $this->done_cnt = $this->Collaborator->countCollaboGoal(
            $teamId, $userId, $this->goal_user_ids,
            [$this->goal_status['approval'], $this->goal_status['hold']]
        );


        $goals = $this->_getGoalInfo(
            $userId,
            $teamId,
            [$this->goal_status['approval'], $this->goal_status['hold']],
            $userType
        );

        foreach ($goals as $key => $val) {
            $goals[$key]['my_goal'] = false;
            $goals[$key]['is_present_term'] = $this->Goal->isPresentTermGoal($val['Goal']['id']);

            if ($userId === $val['User']['id']) {
                $goals[$key]['my_goal'] = true;
                if ($this->my_evaluation_flg === false) {
                    unset($goals[$key]);
                }
            }

            if ($val['Collaborator']['approval_status'] === (string)Collaborator::STATUS_APPROVAL) {
                $goals[$key]['status'] = $this->approval_msg_list[self::APPROVAL_MEMBER_GOAL_MSG];

            } else {
                if ($val['Collaborator']['approval_status'] === (string)Collaborator::STATUS_HOLD) {
                    $goals[$key]['status'] = $this->approval_msg_list[self::NOT_APPROVAL_MEMBER_GOAL_MSG];
                }
            }
        }

        $res = $goals;
        return $this->_getResponseSuccess($res);
    }

    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function _getCollaboratorUserId($userId, $userType)
    {
        $goalUserIds = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $goalUserIds = [$userId];
        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $goalUserIds = array_merge([$userId], $this->member_ids);
        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $goalUserIds = $this->member_ids;
        }
        return $goalUserIds;
    }

    /*
     * ゴールリスト取得
     */
    public function _getGoalInfo($userId, $teamId, $goalStatus, $userType)
    {
        $goals = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $goals = $this->Collaborator->getCollaboGoalDetail(
                $teamId, [$userId], $goalStatus, true, EvaluateTerm::TYPE_CURRENT);

        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $member_goal_info = $this->Collaborator->getCollaboGoalDetail(
                $teamId, $this->member_ids, $goalStatus, false, EvaluateTerm::TYPE_CURRENT);

            $my_goal_info = $this->Collaborator->getCollaboGoalDetail(
                $teamId, [$userId], $goalStatus, true, EvaluateTerm::TYPE_CURRENT);

            $goals = array_merge($member_goal_info, $my_goal_info);

        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $goals = $this->Collaborator->getCollaboGoalDetail(
                $teamId, $this->member_ids, $goalStatus, false, EvaluateTerm::TYPE_CURRENT);
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
